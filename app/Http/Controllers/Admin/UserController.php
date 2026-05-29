<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // សម្រាប់បង្ហាញបញ្ជី User និង Filter
    public function manage_user(Request $request)
    {
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ];

        // ២. ទាញទិន្នន័យជាមួយនឹង Search & Filter
        $users = User::query()
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, fn($q, $role) => $q->where('role', $role))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10);
        return view('admin.manage.user', compact('users', 'stats'));
    }

    // សម្រាប់ប្តូរ Status (Toggle)
    public function toggleStatus($id)
        {
            $user = User::findOrFail($id);

            // ប្តូរស្ថានភាព
            $user->status = ($user->status === 'active') ? 'suspended' : 'active';
            $user->save();

            return redirect()->back()->with('success', 'Status updated successfully!');
        }

        public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required',
            'password' => 'required|min:6',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
            'status' => 'active', // កំណត់លំនាំដើម
        ]);

        return redirect()->back()->with('success', 'User added successfully!');
    }
}

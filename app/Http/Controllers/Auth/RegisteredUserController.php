<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Notifications\NewVendorRegistered;
use Illuminate\Support\Facades\Notification;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string'] ,// បន្ថែម validation សម្រាប់ role
        ]);

        $roleValue = ($request->role === 'vendor') ? 1 : 2;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $roleValue,
            'is_approved' => ($request->role === 'vendor') ? false : true, // Vendor ត្រូវបានកំណត់ is_approved ជា false ដោយ default
        ]);
        if ($request->role === 'vendor') {
    // រក Admin (ប្រសិនបើអ្នកកំណត់ role 2 ជា admin ក្នុង database របស់អ្នក សូមប្តូរជាលេខ 2)
            $admin = User::where('role', 0)->first(); // ឧទាហរណ៍ 0 គឺជា Admin

            if ($admin) {
                $admin->notify(new NewVendorRegistered($user));
             }
        }
        // if ($request->role === 'vendor') {
        //     $admin = User::where('role', 0)->first();

        //     // បន្ថែមបន្ទាត់នេះ ដើម្បី Debug
        //     if (!$admin) {
        //         dd('រកមិនឃើញ Admin ដែលមាន role = 0 ទេ!');
        //     }

        //     Notification::send($admin, new \App\Notifications\NewVendorRegistered($user));

        //     // បន្ថែមបន្ទាត់នេះ ដើម្បីបញ្ជាក់ថាវាបានផ្ញើហើយ
        //     dd('Notification បានផ្ញើទៅកាន់ Admin រួចរាល់!');
        // }

        event(new Registered($user));

        Auth::login($user);


        if ($user->role === 1) {
            return redirect()->route('home')
            ->with('vendor_registered', 'Registered successfully! Please wait for Admin។');
        }

        // return redirect(route('login'));
        // return redirect()->route('login');
        return redirect()->route('home');
    }
}

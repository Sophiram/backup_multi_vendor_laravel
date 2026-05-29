<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
class UserMainController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = Auth::id();

        if (!$user) {
            return redirect()->route('login');
        }
    $totalOrders = Order::where('user_id', $userId)->count();

    // ២. ទាញយកទិន្នន័យ Orders ចុងក្រោយដើម្បីបោះទៅកាន់ Loop (ដោះស្រាយកំហុស Error)
    $orders = Order::where('user_id', $userId)->latest()->get();
        return view('user.profile', compact('totalOrders', 'orders'));
    }

    public function history(){
        $orders = Order::where('user_id', auth()->id())->latest()->get();
        return view('user.history', compact('orders'));
    }

    public function payment(){
        return view('user.payment');
    }
    public function affiliate(){
        return view('user.affiliate');
    }
}

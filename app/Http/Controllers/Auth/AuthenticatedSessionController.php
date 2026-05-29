<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // 1. ពិនិត្យករណី Vendor ដែលមិនទាន់បានអនុម័ត (is_approved == false)
        if ($user->role == 1 && $user->is_approved == false) {
            Auth::guard('web')->logout(); // បង្ខំឱ្យ Logout ចេញវិញ
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account is pending approval from the Admin.',
            ]);
        }

        // 2. ការ Redirect ទៅតាម Role
        if ($user->role == 0) {
            return redirect()->intended(route('admin', absolute: false));
        }
        elseif ($user->role == 1) {
            return redirect()->intended(route('vendor', absolute: false));
        }
        else {
            return redirect()->to('/');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

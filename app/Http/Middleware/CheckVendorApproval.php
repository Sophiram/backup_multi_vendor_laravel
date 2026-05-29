<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckVendorApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
        {
            // ពិនិត្យមើលថា User បាន Login ហើយជា Vendor និងមាន is_approved ជា false
            if (auth()->check() && auth()->user()->role == 1 && auth()->user()->is_approved == false) {
                auth()->logout(); // បង្ខំឱ្យ Logout ចេញ
                return redirect()->route('login')->withErrors([
                    'email' => 'Account is pending approval from Admin។'
                ]);
            }

            return $next($request);
        }
}

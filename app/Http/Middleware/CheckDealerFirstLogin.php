<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDealerFirstLogin
{
    /**
     * Handle an incoming request.
     * Redirect dealer users to complete their profile on first login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isDealer() && $user->is_first_login
            && !$request->routeIs('dealer.*')
            && !$request->routeIs('logout')) {

            if ($request->expectsJson()) {
                return response()->json([
                    'redirect' => route('dealer.complete-profile')
                ]);
            }

            return redirect()->route('dealer.complete-profile');
        }

        return $next($request);
    }
}

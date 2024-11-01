<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jika pengguna login tapi belum mengaktifkan 2FA
        if ($user && !$user->two_factor_confirmed_at) {
            return redirect()->route('home_page')
                ->with('showTwoFactorModal', true);
        }

        return $next($request);
    }
}

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

        // Jika pengguna belum mengaktifkan 2fa
        if ($user && !$user->two_factor_confirmed_at && !$user->two_factor_secret) {
            return redirect()->route('two_factor_activate')
                ->with('showTwoFactorModal', true);
        }
        // Jika pengguna gagal verifikasi 2fa
        else if ($user && !$user->two_factor_confirmed_at) {
            return redirect()->route('two_factor_activate')
                ->with('showTwoFactorModal', true)
                ->with('warning', 'Anda harus mengaktifkan autentikasi dua faktor untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}

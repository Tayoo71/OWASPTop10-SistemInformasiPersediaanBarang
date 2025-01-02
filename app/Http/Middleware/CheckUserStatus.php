<?php

namespace App\Http\Middleware;

use App\Traits\LogActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    use LogActivity;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jika pengguna Aktif
        if ($user && $user->status === "Aktif") {
            // Jika pengguna belum mengaktifkan 2fa
            if (!$user->two_factor_confirmed_at) {
                return redirect()->route('two_factor_activate')
                    ->with('showTwoFactorModal', true);
            }
        } else {
            $this->logActivity('Login Gagal. Percobaan Login dengan Status Akun Tidak Aktif');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors([
                    'status' => 'Status Akun Anda Tidak Aktif. Silakan Hubungi Administrator. ',
                ])
                ->header('Content-Length', 0)
                ->header('Content-Type', 'text/plain');
        }

        return $next($request);
    }
}

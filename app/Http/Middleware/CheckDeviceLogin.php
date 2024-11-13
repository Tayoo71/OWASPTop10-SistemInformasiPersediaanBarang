<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceLogin
{
    use LogActivity;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentSession = session()->getId();
        $currentUserID = Auth::user()->id;

        $sessions = DB::table('sessions')
            ->where('user_id', $currentUserID)
            ->select('id', 'ip_address', 'user_agent')
            ->get();

        // Pisahkan sesi saat ini dari sesi lainnya
        $currentSessionData = $sessions->firstWhere('id', $currentSession);
        $otherSessions = $sessions->filter(function ($session) use ($currentSession) {
            return $session->id !== $currentSession;
        });

        // Hapus semua sesi lain jika ada dan lakukan logging
        if ($otherSessions->isNotEmpty()) {
            DB::table('sessions')
                ->where('user_id', $currentUserID)
                ->where('id', '!=', $currentSession)
                ->delete();

            $this->logActivity('Sistem Logout Akun Otomatis di Perangkat atau Lokasi Lain Pada Username: ' . $currentUserID);
        }

        // Lakukan pengecekan IP dan User Agent untuk sesi saat ini
        if (
            $currentSessionData &&
            ($currentSessionData->ip_address !== $request->ip() || $currentSessionData->user_agent !== $request->header('User-Agent'))
        ) {
            $this->logActivity('Sistem Logout Akun Otomatis Dikarenakan menggunakan Device yang berbeda Pada Username: ' . $currentUserID);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Device atau Lokasi Anda Berubah, Silahkan Login Kembali. ');
        }

        return $next($request);
    }
}

<?php

namespace App\Providers;

use App\Traits\LogActivity;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class FortifyServiceProvider extends ServiceProvider
{
    use LogActivity;
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::twoFactorChallengeView(function () {
            return view('pages.auth.two-factor-challenge');
        });
        Fortify::loginView(function () {
            return view('pages.auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            $this->logActivity('Percobaan Login Pada Username: ' . $request->input(Fortify::username()), true);
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            $this->logActivity('Percobaan Kode Autentikasi 2 Faktor (2FA) Login Pada Username: ' . $request->session()->get('login.id'), true);
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}

<x-header-layout>
    <x-slot:title>2FA</x-slot:title>
    @if (session('showTwoFactorModal'))
        <x-auth.modal-activate-2fa />
    @elseif (session('status') == 'two-factor-authentication-enabled')
        <x-auth.modal-verify-2fa />
    @elseif (session('status') == 'two-factor-authentication-confirmed')
        <x-auth.modal-success-2fa />
    @else
        @php
            header('Location: ' . route('home_page'));
            exit();
        @endphp
    @endif
</x-header-layout>

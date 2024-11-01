<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <h2 class="text-l">Selamat Datang Di Aplikasi Persediaan Toko X</h2>

    @if (session('showTwoFactorModal'))
        <x-auth.modal-activate-2fa />
    @elseif (session('status') == 'two-factor-authentication-enabled')
        <x-auth.modal-verify-2fa />
    @elseif (session('status') == 'two-factor-authentication-confirmed')
        <x-auth.modal-success-2fa />
    @elseif (session('failed2FA'))
        <x-alert type="error">
            Gagal Aktifkan 2FA, Silahkan Coba Lagi
        </x-alert>
    @endif

</x-layout>

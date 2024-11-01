<div class="bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40"></div>

<div tabindex="-1" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
    <div class="relative p-4 w-full max-w-md md:max-w-2xl lg:max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-lg text-gray-500 dark:text-gray-400">
                    Untuk melanjutkan, silakan pindai kode di bawah ini menggunakan aplikasi Authenticator, seperti
                    Google Authenticator, atau salin kode berikut untuk keperluan autentikasi
                </h3>
                <div class="flex justify-center mb-4">
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                </div>
                <p for="otp-code" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-6">
                    {{ auth()->user()->twoFactorQrCodeUrl() }}</p>
                <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}" class="space-y-4">
                    @csrf
                    <input type="number" id="code" name="code" required
                        class="block w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                        placeholder="Masukkan Kode Autentikasi">
                    <button type="submit"
                        class="mt-6 w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Aktifkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

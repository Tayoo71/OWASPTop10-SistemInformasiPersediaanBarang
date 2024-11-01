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
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                    Untuk melanjutkan penggunaan Aplikasi ini, harap Aktifkan Autentikasi Dua Faktor (2FA) sebagai
                    langkah
                    keamanan tambahan. Pastikan Anda memasukkan Kode Autentikasi dengan benar. Pilih 'Aktifkan' untuk
                    memulai proses
                    pengaturan
                </h3>
                <div class="flex justify-center space-x-4">
                    <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                        @csrf
                        <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Aktifkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

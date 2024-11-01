<div id="modal-overlay-success" class="bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40"></div>

<div id="success-popup-modal" tabindex="-1" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    x-data>
    <div class="relative p-4 w-full max-w-md md:max-w-2xl lg:max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                    Autentikasi dua faktor telah berhasil diaktifkan. Pastikan untuk mencatat dan menyimpan kode
                    pemulihan berikut dengan aman. Setiap Kode ini hanya dapat digunakan satu kali saja dan membantu
                    Anda mengakses akun jika kehilangan akses ke
                    perangkat autentikasi
                </h3>
                <div class="mb-5 p-4 bg-gray-100 rounded-lg dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kode Pemulihan Anda:</p>
                    <ul class="mt-2 space-y-1 text-gray-600 dark:text-gray-400">
                        @foreach (auth()->user()->recoveryCodes() as $code)
                            <li>{{ $code }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="flex justify-center">
                    <button
                        @click="document.getElementById('success-popup-modal').remove(); document.getElementById('modal-overlay-success').remove();"
                        type="button"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

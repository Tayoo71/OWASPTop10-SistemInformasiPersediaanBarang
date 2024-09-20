<!-- Modal Overlay -->
<div id="modal-overlay-ubah" class="bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40">
</div>

<!-- Modal for Ubah Barang -->
<div id="crud-modal-ubah" tabindex="-1" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    x-data>
    <div class="relative p-4 w-full max-w-md md:max-w-2xl lg:max-w-4xl max-h-full">
        <div
            class="relative bg-white rounded-lg shadow dark:bg-gray-700 overflow-y-auto max-h-[calc(100vh-2rem)] modal-content">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    @click="document.getElementById('crud-modal-ubah').remove(); document.getElementById('modal-overlay-ubah').remove();">
                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            {{ $slot }}
        </div>
    </div>

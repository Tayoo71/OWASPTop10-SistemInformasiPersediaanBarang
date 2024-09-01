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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ubah Gudang</h3>
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
            <form method="POST"
                action="{{ route('daftargudang.update', $gudang->kode_gudang) }}?{{ http_build_query(request()->only(['search'])) }}"
                class="p-4 md:p-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 mb-4">
                    <div class="col-span-2">
                        <label for="kode_gudang"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode
                            Gudang</label>
                        <input type="text" name="kode_gudang" id="kode_gudang"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Masukkan kode gudang" value="{{ $gudang->kode_gudang }}" required>
                    </div>

                    <div class="col-span-2">
                        <label for="nama_gudang"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                            Gudang</label>
                        <input type="text" name="nama_gudang" id="nama_gudang"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Masukkan nama gudang" value="{{ $gudang->nama_gudang }}" required>
                    </div>

                    <div class="col-span-2">
                        <label for="keterangan"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Masukkan keterangan">{{ $gudang->keterangan }}</textarea>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Ubah
                        Barang</button>
                </div>
        </div>
        </form>
    </div>
</div>

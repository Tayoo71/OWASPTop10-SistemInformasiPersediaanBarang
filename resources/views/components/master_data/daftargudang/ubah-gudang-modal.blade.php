<x-modal.modal-update title="Ubah Gudang">
    <form method="POST"
        action="{{ route('daftargudang.update', $gudang->kode_gudang) }}?{{ http_build_query(request()->only(['search', 'sort_by', 'direction'])) }}"
        class="p-4 md:p-5">
        @csrf
        @method('PUT')
        <div class="grid gap-4 mb-4">
            <div class="col-span-2">
                <label for="kode_gudang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode
                    Gudang</label>
                <input type="text" name="kode_gudang" id="kode_gudang"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Masukkan Kode Gudang" value="{{ $gudang->kode_gudang }}" required>
            </div>

            <div class="col-span-2">
                <label for="nama_gudang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                    Gudang</label>
                <input type="text" name="nama_gudang" id="nama_gudang"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Masukkan Nama Gudang" value="{{ $gudang->nama_gudang }}" required>
            </div>

            <div class="col-span-2">
                <label for="keterangan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Masukkan Keterangan">{{ $gudang->keterangan }}</textarea>
            </div>
        </div>

        <div class="flex justify-center">
            <button type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Ubah
                Barang</button>
        </div>
        </div>
    </form>
    </x-modal-update>

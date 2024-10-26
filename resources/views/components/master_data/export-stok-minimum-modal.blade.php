<x-modal.modal-export title="Cetak & Konversi Informasi Stok Minimum">
    <form method="POST" target="_blank"
        action="{{ route('stokminimum.export') }}?{{ http_build_query(request()->only(['search', 'sort_by', 'direction', 'gudang'])) }}"
        class="p-4 md:p-5"">
        @csrf
        <div class="grid gap-4 mb-4">
            <div class="col-span-2">
                <label for="format" class="block text-sm font-medium text-gray-900 dark:text-white">Format
                    Data</label>
                <p class="mb-2 text-xs text-red-600 dark:text-red-400">
                    Data yang akan dikonversi berdasarkan filter cari yang digunakan saat ini.
                </p>
                <select name="format" id="format" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    <option value="">Pilih Format Data</option>
                    <option value="pdf">PDF</option>
                    <option value="xlsx">XLSX (Excel)</option>
                    <option value="csv">CSV</option>
                </select>
            </div>
        </div>
        <div class="flex justify-center">
            <button type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Konversi Data
            </button>
        </div>
    </form>
</x-modal.modal-export>

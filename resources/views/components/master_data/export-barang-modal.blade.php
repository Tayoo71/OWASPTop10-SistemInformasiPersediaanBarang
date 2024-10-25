<x-modal-export title="Cetak & Konversi Daftar Barang">
    <form method="POST" target="_blank"
        action="{{ route('daftarbarang.export') }}?{{ http_build_query(request()->only(['search', 'sort_by', 'direction', 'gudang'])) }}"
        class="p-4 md:p-5">
        @csrf
        <div class="grid gap-4 mb-4">
            <!-- Pilih Format -->
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

            <!-- Pilihan Daftar Data yang Diekspor -->
            <div class="col-span-2">
                <label for="data_type" class="block text-sm font-medium text-gray-900 dark:text-white">Pilih Daftar
                    Data</label>
                <select name="data_type" id="data_type" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    <option value="lengkap">Daftar Barang Lengkap</option>
                    <option value="harga_pokok">Daftar Barang Harga Pokok</option>
                    <option value="harga_jual">Daftar Barang Harga Jual</option>
                    <option value="tanpa_harga">Daftar Barang Tanpa Harga</option>
                </select>
            </div>

            <!-- Pilihan Tampil Jika Stok Kosong -->
            <div class="col-span-2">
                <label for="stok" class="block text-sm font-medium text-gray-900 dark:text-white">Tampilkan Barang
                    Berdasarkan Stok</label>
                <select name="stok" id="stok" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    <option value="tampil_kosong">Tampilkan Jika Stok Kosong</option>
                    <option value="tidak_tampil_kosong">Jangan Tampilkan Jika Stok Kosong</option>
                </select>
            </div>

            <!-- Pilihan Tampil Berdasarkan Status Barang -->
            <div class="col-span-2">
                <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Tampilkan Barang
                    Berdasarkan Status</label>
                <select name="status" id="status" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    <option value="semua">Semua Status</option>
                    <option value="aktif">Barang Aktif</option>
                    <option value="tidak_aktif">Barang Tidak Aktif</option>
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
</x-modal-export>

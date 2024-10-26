<x-modal.modal-export title="Cetak & Konversi Daftar Barang">
    <form method="POST" target="_blank"
        action="{{ route('daftarbarang.export') }}?{{ http_build_query(request()->only(['search', 'sort_by', 'direction', 'gudang'])) }}"
        class="p-4 md:p-5">
        @csrf
        <div class="grid gap-4 mb-4">
            <x-modal.format-export-selection></x-modal>
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
                    <label for="stok" class="block text-sm font-medium text-gray-900 dark:text-white">Tampilkan
                        Barang
                        Berdasarkan Stok</label>
                    <select name="stok" id="stok" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                        <option value="tampil_kosong">Tampilkan Jika Stok Kosong</option>
                        <option value="tidak_tampil_kosong">Jangan Tampilkan Jika Stok Kosong</option>
                    </select>
                </div>

                <!-- Pilihan Tampil Berdasarkan Status Barang -->
                <div class="col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Tampilkan
                        Barang
                        Berdasarkan Status</label>
                    <select name="status" id="status" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Barang Aktif</option>
                        <option value="tidak_aktif">Barang Tidak Aktif</option>
                    </select>
                </div>
        </div>

        <x-modal.button-export-selection></x-modal>
    </form>
</x-modal.modal-export>

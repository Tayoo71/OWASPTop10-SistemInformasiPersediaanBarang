<x-modal.modal-update title="Ubah Barang">
    <form method="POST"
        action="{{ route('daftarbarang.update', $barang->id) }}?{{ http_build_query(request()->only(['search', 'gudang', 'sort_by', 'direction'])) }}"
        class="p-4 md:p-5" x-data="{ konversiSatuan: {{ json_encode($barang->konversiSatuans) }} }">
        @csrf
        @method('PUT')
        <div class="grid gap-4 mb-4">
            <div class="col-span-2">
                <label for="nama_item" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Item
                </label>
                <input type="text" name="nama_item" id="nama_item" value="{{ $barang->nama_item }}"
                    placeholder="Ubah Nama Item"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    required>
            </div>
            <div class="col-span-2">
                <label for="jenis_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Jenis
                </label>
                <select name="jenis_id" id="jenis_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    <option value="">-</option>
                    @foreach ($jenises as $option)
                        <option value="{{ $option->id }}" {{ $barang->jenis_id == $option->id ? 'selected' : '' }}>
                            {{ $option->nama_jenis }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label for="merek_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Merek
                </label>
                <select name="merek_id" id="merek_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    <option value="">-</option>
                    @foreach ($mereks as $option)
                        <option value="{{ $option->id }}" {{ $barang->merek_id == $option->id ? 'selected' : '' }}>
                            {{ $option->nama_merek }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label for="rak" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Rak
                </label>
                <input type="text" name="rak" id="rak" value="{{ $barang->rak }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Ubah Lokasi Rak">
            </div>
            <div class="col-span-2">
                <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Keterangan
                </label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Ubah Keterangan">{{ $barang->keterangan }}</textarea>
            </div>
            <div class="col-span-2">
                <label for="stok_minimum" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stok
                    Minimum</label>
                <input type="number" min="0" name="stok_minimum" id="stok_minimum"
                    value="{{ $barang->stok_minimum }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Ubah Stok Minimum">
            </div>

            <div class="col-span-2">
                <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Status
                    Barang</label>
                <p class="mb-3 text-xs text-red-600 dark:text-red-400">
                    Barang yang berstatus "Tidak Aktif" tidak dapat digunakan dalam fitur Transaksi, Informasi Stok
                    Minimum, serta tidak akan terdaftar dalam Laporan Daftar Barang.
                </p>
                <select name="status" id="status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    <option value="">Pilih Status Barang</option>
                    <option value="Aktif" {{ $barang->status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ $barang->status === 'Tidak Aktif' ? 'selected' : '' }}>Tidak
                        Aktif
                    </option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-900 dark:text-white">
                    Konversi Satuan
                </label>
                <span class="block mb-2 text-xs text-red-600 dark:text-gray-400">Tidak dapat melakukan
                    perubahan pada Nama Satuan dan Jumlah Konversi Satuan</span>
                <template x-for="(satuan, index) in konversiSatuan" :key="index">
                    <div class="flex space-x-2 mb-2">
                        <input type="hidden" x-model="satuan.id" :name="'konversiSatuan[' + index + '][id]'">
                        <input type="text" x-model="satuan.satuan" :id="'satuan_' + index"
                            :name="'konversiSatuan[' + index + '][satuan]'" placeholder="Nama Satuan"
                            class="bg-gray-200 border border-gray-400 text-gray-800 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-500 dark:text-gray-300"
                            required disabled>
                        <input type="number" min="1" x-model="satuan.jumlah" :id="'jumlah_' + index"
                            :name="'konversiSatuan[' + index + '][jumlah]'" placeholder="Jumlah"
                            class="bg-gray-200 border border-gray-400 text-gray-800 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-500 dark:text-gray-300"
                            required disabled>
                        <input type="number" min="0" x-model="satuan.harga_pokok" :id="'harga_pokok_' + index"
                            :name="'konversiSatuan[' + index + '][harga_pokok]'" placeholder="Harga Pokok"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                        <input type="number" min="0" x-model="satuan.harga_jual" :id="'harga_jual_' + index"
                            :name="'konversiSatuan[' + index + '][harga_jual]'" placeholder="Harga Jual"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                    </div>
                </template>
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

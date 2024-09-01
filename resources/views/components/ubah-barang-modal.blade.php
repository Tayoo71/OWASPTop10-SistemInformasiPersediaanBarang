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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ubah Barang</h3>
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
                action="{{ route('daftarbarang.update', $barang->id) }}?{{ http_build_query(request()->only(['search', 'gudang'])) }}"
                class="p-4 md:p-5" x-data="{ konversiSatuan: {{ json_encode($barang->konversiSatuans) }} }">
                @csrf
                @method('PUT')
                <div class="grid gap-4 mb-4">
                    <!-- Field Nama Item -->
                    <div class="col-span-2">
                        <label for="nama_item" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nama Item
                        </label>
                        <input type="text" name="nama_item" id="nama_item" value="{{ $barang->nama_item }}"
                            placeholder="Ubah nama item"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            required>
                    </div>

                    <!-- Dropdown Jenis -->
                    <div class="col-span-2">
                        <label for="jenis" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Jenis
                        </label>
                        <select name="jenis" id="jenis"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-</option>
                            @foreach ($jenises as $option)
                                <option value="{{ $option->id }}"
                                    {{ $barang->jenis_id == $option->id ? 'selected' : '' }}>
                                    {{ $option->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dropdown Merek -->
                    <div class="col-span-2">
                        <label for="merek" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Merek
                        </label>
                        <select name="merek" id="merek"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-</option>
                            @foreach ($mereks as $option)
                                <option value="{{ $option->id }}"
                                    {{ $barang->merek_id == $option->id ? 'selected' : '' }}>
                                    {{ $option->nama_merek }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Field Rak -->
                    <div class="col-span-2">
                        <label for="rak" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Rak
                        </label>
                        <input type="text" name="rak" id="rak" value="{{ $barang->rak }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ubah lokasi rak">
                    </div>

                    <!-- Field Keterangan -->
                    <div class="col-span-2">
                        <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Keterangan
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ubah keterangan">{{ $barang->keterangan }}</textarea>
                    </div>

                    <!-- Field Stok Minimum -->
                    <div class="col-span-2">
                        <label for="stok_minimum"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stok
                            Minimum</label>
                        <input type="number" min="0" name="stok_minimum" id="stok_minimum"
                            value="{{ $barang->stok_minimum }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ubah stok minimum">
                    </div>

                    <!-- Field Konversi Satuan -->
                    <div class="col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Konversi Satuan
                        </label>
                        <span class="block mb-2 text-xs text-gray-500 dark:text-gray-400">Tidak dapat melakukan
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
                                <input type="number" min="0" x-model="satuan.harga_pokok"
                                    :id="'harga_pokok_' + index" :name="'konversiSatuan[' + index + '][harga_pokok]'"
                                    placeholder="Harga Pokok"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                <input type="number" min="0" x-model="satuan.harga_jual"
                                    :id="'harga_jual_' + index" :name="'konversiSatuan[' + index + '][harga_jual]'"
                                    placeholder="Harga Jual"
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
    </div>
</div>

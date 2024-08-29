<!-- Modal for Tambah Barang -->
<div id="crud-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md md:max-w-2xl lg:max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tambah Barang
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="crud-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form method="POST"
                action="{{ route('daftarbarang.store') }}?{{ http_build_query(request()->only(['search', 'gudang'])) }}"
                class="p-4 md:p-5" x-data="{ konversiSatuan: [{ satuan: '', jumlah: '', harga_pokok: '', harga_jual: '' }] }">
                @csrf
                <div class="grid gap-4 mb-4">
                    <div class="col-span-2">
                        <label for="nama_item" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                            Item</label>
                        <input type="text" name="nama_item" id="nama_item"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Masukkan nama item" required>
                    </div>

                    <div class="col-span-2">
                        <label for="jenis"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis</label>
                        <select name="jenis" id="jenis"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            <option value="">Pilih jenis</option>
                            @foreach ($jenises as $option)
                                <option value="{{ $option->id }}">{{ $option->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label for="merek"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Merek</label>
                        <select name="merek" id="merek"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            <option value="">Pilih merek</option>
                            @foreach ($mereks as $option)
                                <option value="{{ $option->id }}">{{ $option->nama_merek }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label for="rak"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rak</label>
                        <input type="text" name="rak" id="rak"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Masukkan lokasi rak">
                    </div>

                    <div class="col-span-2">
                        <label for="keterangan"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Masukkan keterangan"></textarea>
                    </div>

                    <div class="col-span-2">
                        <label for="stok_minimum"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stok
                            Minimum</label>
                        <input type="number" min="0" name="stok_minimum" id="stok_minimum"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Masukkan stok minimum">
                    </div>

                    <div class="col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konversi
                            Satuan</label>

                        <template x-for="(satuan, index) in konversiSatuan" :key="index">
                            <div class="flex space-x-2 mb-2">
                                <input type="text" x-model="satuan.satuan" :id="'satuan_' + index"
                                    :name="'konversiSatuan[' + index + '][satuan]'" placeholder="Nama Satuan"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    required>
                                <input type="number" min="1" x-model="satuan.jumlah" :id="'jumlah_' + index"
                                    :name="'konversiSatuan[' + index + '][jumlah]'" placeholder="Jumlah"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    required>
                                <input type="number" min="0" x-model="satuan.harga_pokok"
                                    :id="'harga_pokok_' + index" :name="'konversiSatuan[' + index + '][harga_pokok]'"
                                    placeholder="Harga Pokok"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                <input type="number" min="0" x-model="satuan.harga_jual"
                                    :id="'harga_jual_' + index" :name="'konversiSatuan[' + index + '][harga_jual]'"
                                    placeholder="Harga Jual"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                <button type="button" @click="konversiSatuan.splice(index, 1)"
                                    class="text-red-500 hover:text-red-700">Hapus</button>
                            </div>
                        </template>
                        <button type="button"
                            @click="konversiSatuan.push({ satuan: '', jumlah: '', harga_pokok: '', harga_jual: '' })"
                            class="mt-2 text-sm text-blue-500 hover:text-blue-700">+ Tambah Satuan</button>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Tambah Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

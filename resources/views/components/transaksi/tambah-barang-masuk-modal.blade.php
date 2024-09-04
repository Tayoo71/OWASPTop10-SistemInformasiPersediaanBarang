<x-modal-create title="Tambah Transaksi Barang Masuk">
    <form method="POST"
        action="{{ route('barangmasuk.store') }}?{{ http_build_query(request()->only(['search', 'gudang', 'start', 'end'])) }}"
        class="p-4 md:p-5">
        @csrf
        <div class="mb-4">
            <label for ="selected_gudang"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gudang</label>
            <select name="selected_gudang" id="selected_gudang"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required>
                <option value="">Pilih Gudang</option>
                @foreach ($gudangs as $gudang)
                    <option value="{{ $gudang->kode_gudang }}">{{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}
                    </option>
                @endforeach
            </select>
        </div>
        <div x-data="barangSearch()" class="relative">
            <div class="mb-4">
                <label for="barang"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Barang</label>
                <input type="text" id="barang" x-model="search" @input.debounce.500ms="searchBarang"
                    placeholder="Cari Barang..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" />
                <div x-show="barangList.length > 0"
                    class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 w-full max-h-48 overflow-y-auto">
                    <template x-for="barang in barangList" :key="barang.id">
                        <div @click="selectBarang(barang)" class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                            <span x-text="barang.nama_item"></span>
                        </div>
                    </template>
                </div>
            </div>
            <input type="hidden" name="barang_id" x-model="selectedBarang.id" />
            <div class="mb-4" x-show="konversiSatuan.length > 0">
                <label for="satuan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Satuan
                    Stok</label>
                <select name="satuan" id="satuan" x-model="selectedKonversiSatuan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required>
                    <template x-for="satuan in konversiSatuan" :key="satuan.id">
                        <option :value="satuan.id" x-text="satuan.satuan"></option>
                    </template>
                </select>
            </div>
            <div class="mb-4" x-show="selectedBarang.id !== '' && konversiSatuan.length > 0">
                <label for="jumlah_stok_masuk"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah
                    Stok Masuk</label>
                <input type="number" name="jumlah_stok_masuk" id="jumlah_stok_masuk" min="1" step = "1"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5"
                    placeholder="Masukkan jumlah stok masuk" required>
            </div>
        </div>
        <div class="grid gap-4 mb-4">
            <div class="col-span-2">
                <label for="keterangan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Masukkan keterangan"></textarea>
            </div>
        </div>
        <div class="flex justify-center">
            <button type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Tambah Barang
            </button>
        </div>
    </form>
    <script>
        function barangSearch() {
            return {
                search: '',
                barangList: [],
                konversiSatuan: [],
                selectedBarang: {},
                selectedKonversiSatuan: '',

                searchBarang() {
                    if (this.search.length > 2) {
                        fetch(`/barangmasuk/search?search=${this.search}`)
                            .then(response => response.json())
                            .then(data => {
                                this.barangList = data;
                            });
                    } else {
                        this.barangList = [];
                    }
                },

                selectBarang(barang) {
                    this.selectedBarang = barang;
                    this.search = barang.nama_item;
                    this.konversiSatuan = barang.konversi_satuans;
                    this.barangList = [];
                }
            }
        }
    </script>
</x-modal-create>

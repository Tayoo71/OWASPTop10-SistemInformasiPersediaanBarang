<x-modal-create title="Tambah Transaksi Barang Keluar">
    <form method="POST"
        action="{{ route('barangkeluar.store') }}?{{ http_build_query(request()->only(['search', 'gudang', 'start', 'end'])) }}"
        class="p-4 md:p-5">
        @csrf
        <div x-data="barangSearch()" class="relative">
            <div class="mb-4">
                <label for ="selected_gudang"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gudang</label>
                <select name="selected_gudang" id="selected_gudang" x-model="selectedGudang"
                    @change="search !== '' ? updateStok() : null"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required>
                    <option value="">Pilih Gudang</option>
                    @foreach ($gudangs as $gudang)
                        <option value="{{ $gudang->kode_gudang }}">
                            {{ $gudang->kode_gudang }} -
                            {{ $gudang->nama_gudang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4" x-show="selectedGudang !== ''">
                <label for="barang"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Barang</label>
                <input type="text" id="barang" x-model="search" @input.debounce.500ms="searchBarang"
                    placeholder="Cari Barang..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required />
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
            <div class="mb-4" x-show="selectedBarang.id !== '' && konversiSatuan.length > 0 && selectedGudang !== ''">
                <label for="stokBarang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stok
                    Barang Saat Ini</label>
                <input type="text" name="stokBarang" id="stokBarang" x-model="stokBarang"
                    class="bg-gray-200 border border-gray-400 text-gray-900 cursor-not-allowed text-sm rounded-lg w-full p-2.5"
                    placeholder="Loading..." disabled>
            </div>
            <div class="mb-4" x-show="konversiSatuan.length > 0 && selectedGudang !== '' && selectedBarang.id !== ''">
                <label for="satuan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Satuan
                    Stok</label>
                <select name="satuan" id="satuan" x-model="selectedKonversiSatuan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required>
                    <option value="">Pilih Satuan</option>
                    <template x-for="satuan in konversiSatuan" :key="satuan.id">
                        <option :value="satuan.id" x-text="satuan.satuan"></option>
                    </template>
                </select>
            </div>
            <div class="mb-4" x-show="selectedBarang.id !== '' && konversiSatuan.length > 0 && selectedGudang !== ''">
                <label for="jumlah_stok_keluar"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah
                    Stok Keluar</label>
                <input type="number" name="jumlah_stok_keluar" id="jumlah_stok_keluar" min="1" step = "1"
                    x-model="jumlahStokKeluar"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5"
                    placeholder="Masukkan Jumlah Stok Keluar" required>
            </div>
        </div>
        <div class="grid gap-4 mb-4">
            <div class="col-span-2">
                <label for="keterangan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Masukkan Keterangan"></textarea>
            </div>
        </div>
        <div class="flex justify-center">
            <button type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Tambah Transaksi
            </button>
        </div>
    </form>
    @push('scripts')
        <script>
            function barangSearch() {
                return {
                    search: '',
                    selectedGudang: '',
                    barangList: [],
                    konversiSatuan: [],
                    selectedBarang: {},
                    stokBarang: '',
                    selectedKonversiSatuan: '',
                    jumlahStokKeluar: '',

                    searchBarang() {
                        if (this.selectedBarang && this.selectedBarang.id) {
                            this.resetVariables();
                        }
                        if (this.search.length > 0) {
                            this.fetchAPI(this.search, this.selectedGudang)
                                .then(data => {
                                    this.barangList = data;
                                })
                                .catch(error => {
                                    console.error('Error fetching data:', error);
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
                        this.stokBarang = this.selectedBarang.stok;
                        this.jumlahStokKeluar = '';
                    },

                    updateStok() {
                        this.fetchAPI(this.search, this.selectedGudang)
                            .then(data => {
                                this.stokBarang = data[0].stok;
                            })
                            .catch(error => {
                                console.error('Error fetching data:', error);
                            });
                    },

                    fetchAPI(search, gudang) {
                        return fetch(`/barang/search?search=${search}&gudang=${gudang}`)
                            .then(response => response.json());
                    },

                    resetVariables() {
                        this.selectedBarang = {};
                        this.konversiSatuan = [];
                        this.barangList = [];
                        this.stokBarang = '';
                        this.jumlahStokKeluar = '';
                        this.selectedKonversiSatuan = '';
                    }
                }
            }
        </script>
    @endpush
</x-modal-create>

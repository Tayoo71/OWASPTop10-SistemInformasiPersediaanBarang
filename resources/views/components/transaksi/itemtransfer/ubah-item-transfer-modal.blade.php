<x-modal.modal-update title="Ubah Transaksi Item Transfer">
    <form method="POST"
        action="{{ route('itemtransfer.update', $transaksi->id) }}?{{ http_build_query(request()->only(['search', 'gudang', 'start', 'end', 'sort_by', 'direction'])) }}"
        class="p-4 md:p-5">
        @csrf
        @method('PUT')
        <div x-data="ubahBarangSearch()" x-init="updateStok()" class="relative">
            <div class="mb-4">
                <label for="selected_gudang_asal"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gudang Asal</label>
                <select name="selected_gudang_asal" id="selected_gudang_asal" x-model="ubahSelectedGudangAsal"
                    @change="ubahSearch !== '' ? updateStok() : null; ubahSelectedGudangTujuan=''"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required>
                    <option value="">Pilih Gudang Asal</option>
                    @foreach ($gudangs as $gudang)
                        <option value="{{ $gudang->kode_gudang }}"
                            {{ $transaksi->gudang_asal == $gudang->kode_gudang ? 'selected' : '' }}>
                            {{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="selected_gudang_tujuan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gudang Tujuan</label>
                <select name="selected_gudang_tujuan" id="selected_gudang_tujuan" x-model="ubahSelectedGudangTujuan"
                    @change="ubahSearch !== '' ? updateStok() : null"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required>
                    <option value="">Pilih Gudang Tujuan</option>
                    @foreach ($gudangs as $gudang)
                        <option value="{{ $gudang->kode_gudang }}"
                            x-bind:hidden="ubahSelectedGudangAsal === '{{ $gudang->kode_gudang }}'"
                            {{ $transaksi->gudang_tujuan == $gudang->kode_gudang ? 'selected' : '' }}>
                            {{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4" x-show="ubahSelectedGudangAsal !== ''">
                <label for="barang"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Barang</label>
                <input type="text" id="barang" x-model="ubahSearch" @input.debounce.500ms="ubahSearchBarang"
                    placeholder="Cari Barang..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required />
                <div x-show="ubahBarangList.length > 0"
                    class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 w-full max-h-48 overflow-y-auto">
                    <template x-for="barang in ubahBarangList" :key="barang.id">
                        <div @click="ubahSelectBarang(barang)" class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                            <span x-text="barang.nama_item"></span>
                        </div>
                    </template>
                </div>
            </div>
            <input type="hidden" name="barang_id" x-model="ubahSelectedBarang" />
            @if (auth()->user()->can('transaksi.tampil_stok.akses'))
                <div class="mb-4"
                    x-show="ubahSelectedBarang !== '' && ubahKonversiSatuan.length > 0 && ubahSelectedGudangAsal !== ''">
                    <label for="ubahStokBarang"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stok
                        Barang Saat Ini Pada Gudang Asal</label>
                    <input type="text" name="ubahStokBarang" id="ubahStokBarang" x-model="ubahStokBarang"
                        class="bg-gray-200 border border-gray-400 text-gray-900 cursor-not-allowed text-sm rounded-lg w-full p-2.5"
                        placeholder="Loading..." disabled>
                </div>
            @endif
            <div class="mb-4"
                x-show="ubahKonversiSatuan.length > 0 && ubahSelectedGudangAsal !== '' && ubahSelectedBarang !== ''">
                <label for="satuan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih
                    Satuan
                    Stok</label>
                <select name="satuan" id="satuan" x-model="ubahSelectedKonversiSatuan"
                    @change="ubahSearch !== '' ? updateStok() : null"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required>
                    <option value="">Pilih Satuan</option>
                    <template x-for="satuan in ubahKonversiSatuan" :key="satuan.id">
                        <option :value="satuan.id" x-text="satuan.satuan"
                            :selected="satuan.id == ubahSelectedKonversiSatuan"></option>
                    </template>
                </select>
            </div>
            <div class="mb-4"
                x-show="ubahSelectedBarang !== '' && ubahKonversiSatuan.length > 0 && ubahSelectedGudangAsal !== ''">
                <label for="jumlah_stok_transfer"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Stok
                    Transfer</label>
                <input type="number" name="jumlah_stok_transfer" id="jumlah_stok_transfer" min="1"
                    step="1" x-model="ubahJumlahStokTransfer" @input.debounce.500ms="updateStok()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5"
                    placeholder="Masukkan Jumlah Stok Transfer" required>
            </div>
            <div class="grid gap-4 mb-4">
                <div class="col-span-2">
                    <label for="keterangan"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="3" x-model="ubahKeterangan"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Masukkan Keterangan"></textarea>
                </div>
            </div>
        </div>
        <div class="flex justify-center">
            <button type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Ubah Transaksi
            </button>
        </div>
    </form>
    @push('scripts')
        <script>
            function ubahBarangSearch() {
                return {
                    ubahSearch: '{{ $transaksi->barang->nama_item }}',
                    ubahSelectedGudangAsal: '{{ $transaksi->gudang_asal }}',
                    ubahSelectedGudangTujuan: '{{ $transaksi->gudang_tujuan }}',
                    ubahBarangList: [],
                    ubahKonversiSatuan: @json($transaksi->barang->konversiSatuans),
                    ubahSelectedBarang: '{{ $transaksi->barang_id }}',
                    ubahSelectedKonversiSatuan: '{{ $editTransaksiSatuan['id'] }}',
                    ubahJumlahStokTransfer: '{{ $editTransaksiSatuan['jumlah'] }}',
                    ubahKeterangan: '{{ $transaksi->keterangan }}',
                    ubahStokBarang: '',

                    ubahSearchBarang() {
                        if (this.ubahSelectedBarang && this.ubahSelectedBarang) {
                            this.ubahResetVariables();
                        }
                        if (this.ubahSearch.length > 0) {
                            this.fetchAPI(this.ubahSearch, this.ubahSelectedGudangAsal)
                                .then(data => {
                                    this.ubahBarangList = data;
                                })
                                .catch(error => {
                                    console.error('Error fetching data:', error);
                                });
                        } else {
                            this.ubahBarangList = [];
                        }
                    },

                    ubahSelectBarang(barang) {
                        this.ubahSelectedBarang = barang.id;
                        this.ubahSearch = barang.nama_item;
                        this.ubahKonversiSatuan = barang.konversi_satuans;
                        this.ubahBarangList = [];
                        this.ubahSelectedKonversiSatuan = "";
                        this.ubahJumlahStokTransfer = "";
                        this.ubahKeterangan = "";
                        this.ubahStokBarang = barang.stok;
                    },

                    updateStok() {
                        this.fetchAPI(this.ubahSearch, this.ubahSelectedGudangAsal)
                            .then(data => {
                                this.ubahStokBarang = data[0].stok;
                            })
                            .catch(error => {
                                console.error('Error fetching data:', error);
                            });
                    },

                    fetchAPI(search, gudang) {
                        return axios.get(`/barang/search`, {
                                params: {
                                    search: search,
                                    gudang: gudang
                                }
                            })
                            .then(response => response.data);

                    },

                    ubahResetVariables() {
                        this.ubahSelectedBarang = {};
                        this.ubahKonversiSatuan = [];
                        this.ubahBarangList = [];
                        this.ubahStokBarang = '';
                        this.ubahJumlahStokTransfer = '';
                        this.ubahSelectedKonversiSatuan = '';
                    }
                }
            }
        </script>
    @endpush
</x-modal.modal-update>

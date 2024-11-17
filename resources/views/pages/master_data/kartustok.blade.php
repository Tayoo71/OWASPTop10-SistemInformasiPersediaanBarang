<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <!-- Search Box -->
    <form class="w-full max-w-lg lg:max-w-3xl xl:max-w-4xl mx-auto mb-4" method="GET" action="">
        <!-- Datepicker -->
        <div class="flex justify-center mb-4">
            <div id="date-range-picker" date-rangepicker datepicker-buttons datepicker-format="dd/mm/yyyy"
                class="flex items-center">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input id="datepicker-range-start" name="start" type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                        required placeholder="Pilih Tanggal Mulai" value="{{ request('start') }}">
                </div>
                <span class="mx-3 text-gray-500">Sampai</span>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input id="datepicker-range-end" name="end" type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                        required placeholder="Pilih Tanggal Akhir" value="{{ request('end') }}">
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center relative" x-data="{ dropdownOpen: false }">
            <div class="flex flex-grow">
                <!-- Dropdown Gudang -->
                <label for="gudang-dropdown" class="mb-2 text-sm font-medium text-gray-900 sr-only">Gudang</label>
                <button id="gudang-dropdown" @click="dropdownOpen = !dropdownOpen"
                    class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-l-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 transition-none"
                    type="button">
                    {{ request('gudang', 'Semua Gudang') }}
                    <svg class="w-2.5 h-2.5 ml-2.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>

                <!-- Dropdown Content -->
                <div x-show="dropdownOpen" @click.outside="dropdownOpen = false"
                    class="z-20 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full left-0 mt-1">
                    <ul class="py-2 text-sm text-gray-700">
                        <li class="flex justify-center items-center">
                            <button type="submit" name="gudang" value=""
                                class="flex justify-center items-center w-full px-4 py-2 hover:bg-gray-100">
                                Semua Gudang
                            </button>
                        </li>

                        @foreach ($gudangs as $gudang)
                            <li class="flex justify-center items-center">
                                <button type="submit" name="gudang" value="{{ $gudang->kode_gudang }}"
                                    class="flex justify-center items-center w-full px-4 py-2 hover:bg-gray-100">
                                    {{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <input type="hidden" name="gudang" id="gudang" value="{{ request('gudang') }}"
                        x-bind:disabled="dropdownOpen">
                </div>

                <!-- Search Bar -->
                <div class="relative w-full" x-data="searchBarangFunction()" x-init="updateVariable()">
                    <div class="flex">
                        <div class="relative flex-grow">
                            <input type="search" id="search-dropdown"
                                class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-none border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                x-model="searchBarang" @input.debounce.500ms="searchBarangAPI"
                                :placeholder="selectedBarang !== '' ? 'Loading...' : 'Cari Barang...'"
                                placeholder="Cari Barang..." required />

                            <!-- Dropdown -->
                            <div x-show="searchBarangList.length > 0"
                                class="absolute z-50 bg-white border border-gray-300 rounded-lg mt-1 w-full max-h-48 overflow-y-auto">
                                <template x-for="barang in searchBarangList" :key="barang.id">
                                    <div @click="selectBarang(barang)"
                                        class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                        <span x-text="barang.nama_item"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <button type="submit"
                            class="p-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-0 transition-none flex-shrink-0">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                            <span class="sr-only">Search</span>
                        </button>
                    </div>

                    <input type="hidden" name="search" x-model="selectedBarang" />
                </div>
            </div>
        </div>
    </form>
    @if ($kartuStok && $canExportKartuStok)
        <div class="flex justify-end items-center">
            <button data-modal-target="export-modal" data-modal-toggle="export-modal"
                class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 18v4h12v-4M8 18h8" />
                </svg>
                Cetak & Konversi
            </button>
        </div>
    @endif
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <!-- Tabel -->
        <table class="w-full text-sm text-center text-gray-500">
            <thead class="text-xs text-gray-700 bg-gray-50 sticky top-0 shadow-md">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50">NOMOR TRANSAKSI</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">GUDANG</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">TANGGAL TRANSAKSI</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">TIPE TRANSAKSI</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">JUMLAH</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">SALDO STOK</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kartuStok as $data)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4 align-middle">{{ $data['nomor_transaksi'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $data['gudang'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $data['tanggal'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $data['tipe_transaksi'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $data['jumlah'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $data['saldo_stok'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $data['keterangan'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Silahkan mengisi kolom
                            pencarian untuk melihat Data Kartu Stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($kartuStok && $canExportKartuStok)
        {{-- Modal Export --}}
        <x-master_data.kartustok.export-kartu-stok-modal />
    @endif
    @push('scripts')
        <script>
            function searchBarangFunction() {
                return {
                    searchBarang: '',
                    selectedBarang: '{{ request('search') }}',
                    searchBarangList: [],

                    searchBarangAPI() {
                        this.selectedBarang = '';
                        if (this.searchBarang.length > 0) {
                            this.fetchAPI(this.searchBarang, 'search')
                                .then(data => {
                                    this.searchBarangList = data;
                                })
                                .catch(error => {
                                    console.error('Error fetching data:', error);
                                });
                        } else {
                            this.searchBarangList = [];
                        }
                    },
                    updateVariable() {
                        if (this.selectedBarang != '') {
                            this.fetchAPI(this.selectedBarang, 'update')
                                .then(data => {
                                    this.searchBarang = data[0].nama_item
                                })
                                .catch(error => {
                                    console.error('Error fetching data:', error);
                                });
                        }
                    },
                    selectBarang(barang) {
                        this.selectedBarang = barang.id;
                        this.searchBarang = barang.nama_item;
                        this.searchBarangList = [];
                    },
                    fetchAPI(search, mode) {
                        return axios.get(`/barang/search/barang`, {
                                params: {
                                    search: search,
                                    mode: mode
                                }
                            })
                            .then(response => response.data);
                    },
                }
            }
        </script>
    @endpush
</x-layout>

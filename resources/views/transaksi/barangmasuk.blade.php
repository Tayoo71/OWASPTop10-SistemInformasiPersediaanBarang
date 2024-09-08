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
                        placeholder="Pilih tanggal mulai" value="{{ request('start') }}">
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
                        placeholder="Pilih tanggal akhir" value="{{ request('end') }}">
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
                <div class="relative w-full flex">
                    <input type="search" id="search-dropdown" name="search"
                        class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-r-none border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari Transaksi Barang Masuk" value="{{ request('search') }}" />
                    <button type="submit"
                        class="p-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-0 transition-none flex-shrink-0">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                        <span class="sr-only">Search</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Wrapper untuk Tabel dan Button Tambah -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <!-- Tambah Barang Button -->
        <div class="flex justify-between items-center mb-4">
            <!-- Modal Trigger -->
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Tambah Transaksi Barang Masuk
            </button>
        </div>

        <!-- Tabel -->
        <table class="w-full text-sm text-center text-gray-500">
            <thead class="text-xs text-gray-700 bg-gray-50 sticky top-0 shadow-md">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50">NOMOR TRANSAKSI</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">TANGGAL BUAT</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">TANGGAL UBAH</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">GUDANG</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">NAMA BARANG</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">JUMLAH STOK MASUK</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">KETERANGAN</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">USER BUAT</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">USER UPDATE</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksies as $transaksi)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4 align-middle">{{ $transaksi['id'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $transaksi['created_at'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $transaksi['updated_at'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $transaksi['kode_gudang'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $transaksi['nama_item'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $transaksi['jumlah_stok_masuk'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $transaksi['keterangan'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $transaksi['user_buat_id'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $transaksi['user_update_id'] }}</td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex justify-center items-center">
                                <a href="{{ route('barangmasuk.index', array_merge(request()->only(['search', 'gudang', 'start', 'end']), ['edit' => $transaksi['id']])) }}"
                                    class="font-medium text-yellow-300 hover:underline">
                                    Ubah
                                </a>
                                <a href="{{ route('barangmasuk.index', array_merge(request()->only(['search', 'gudang', 'start', 'end']), ['delete' => $transaksi['id']])) }}"
                                    class="font-medium text-red-600 hover:underline ml-3">
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">Data Transaksi Barang Masuk
                            tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="py-4 px-4 mt-4">
        {{ $transaksies->links() }}
    </div>

    {{-- Modal Tambah Transaksi --}}
    <x-tambah-barang-masuk-modal :gudangs="$gudangs" />
    @if ($editTransaksi && !$errors->any() && !session('error'))
        {{-- Modal Ubah Transaksi --}}
        <x-ubah-barang-masuk-modal :gudangs="$gudangs" :transaksi="$editTransaksi" :editTransaksiSatuan="$editTransaksiSatuan" />
    @elseif ($deleteTransaksi && !$errors->any() && !session('error'))
        {{-- Modal Hapus Transaksi --}}
        <x-modal-delete :action="route(
            'barangmasuk.destroy',
            ['barangmasuk' => $deleteTransaksi->id] + request()->only('search', 'gudang', 'start', 'end'),
        )"
            message='Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin ingin menghapus transaksi barang masuk dengan Nomor Transaksi "{{ $deleteTransaksi->id }}" | Nama Item "{{ $deleteTransaksi->barang->nama_item }}"?' />
    @endif
</x-layout>

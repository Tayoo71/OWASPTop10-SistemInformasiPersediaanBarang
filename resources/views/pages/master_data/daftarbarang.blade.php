<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <!-- Search Box -->
    <form class="w-full max-w-lg lg:max-w-3xl xl:max-w-4xl mx-auto mb-4" method="GET" action="">
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
                        placeholder="Cari Barang" value="{{ request('search') }}" />
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

    <!-- Tambah Barang Button -->
    <div class="flex justify-between items-center mb-4">
        <!-- Modal Trigger -->
        @if ($canCreateDaftarBarang)
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Tambah Barang
            </button>
        @endif
        @if ($barangs->isNotEmpty() && $canExportDaftarBarang)
            <button data-modal-target="export-modal" data-modal-toggle="export-modal"
                class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 18v4h12v-4M8 18h8" />
                </svg>
                Cetak & Konversi
            </button>
        @endif
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <!-- Tabel -->
        <table class="w-full text-sm text-center text-gray-500">
            <thead class="text-xs text-gray-700 bg-gray-50 sticky top-0 shadow-md">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('daftarbarang.index', array_merge(request()->query(), ['sort_by' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            KODE ITEM
                            @if (request('sort_by') === 'id')
                                @if (request('direction') === 'asc')
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15l-4 4-4-4" />
                                    </svg>
                                @endif
                            @else
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                </svg>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('daftarbarang.index', array_merge(request()->query(), ['sort_by' => 'nama_item', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            NAMA BARANG
                            @if (request('sort_by') === 'nama_item')
                                @if (request('direction') === 'asc')
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15l-4 4-4-4" />
                                    </svg>
                                @endif
                            @else
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                </svg>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">STOK</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('daftarbarang.index', array_merge(request()->query(), ['sort_by' => 'jenis', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            JENIS
                            @if (request('sort_by') === 'jenis')
                                @if (request('direction') === 'asc')
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15l-4 4-4-4" />
                                    </svg>
                                @endif
                            @else
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                </svg>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('daftarbarang.index', array_merge(request()->query(), ['sort_by' => 'merek', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            MEREK
                            @if (request('sort_by') === 'merek')
                                @if (request('direction') === 'asc')
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15l-4 4-4-4" />
                                    </svg>
                                @endif
                            @else
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                </svg>
                            @endif
                        </a>
                    </th>
                    @if ($canAccessHargaPokok)
                        <th scope="col" class="px-6 py-3 bg-gray-50">HARGA POKOK</th>
                    @endif
                    @if ($canAccessHargaJual)
                        <th scope="col" class="px-6 py-3 bg-gray-50">HARGA JUAL</th>
                    @endif
                    <th scope="col" class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('daftarbarang.index', array_merge(request()->query(), ['sort_by' => 'rak', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            RAK
                            @if (request('sort_by') === 'rak')
                                @if (request('direction') === 'asc')
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15l-4 4-4-4" />
                                    </svg>
                                @endif
                            @else
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                </svg>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('daftarbarang.index', array_merge(request()->query(), ['sort_by' => 'keterangan', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            KETERANGAN
                            @if (request('sort_by') === 'keterangan')
                                @if (request('direction') === 'asc')
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15l-4 4-4-4" />
                                    </svg>
                                @endif
                            @else
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                </svg>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('daftarbarang.index', array_merge(request()->query(), ['sort_by' => 'status', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            STATUS
                            @if (request('sort_by') === 'status')
                                @if (request('direction') === 'asc')
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15l-4 4-4-4" />
                                    </svg>
                                @endif
                            @else
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                </svg>
                            @endif
                        </a>
                    </th>
                    @if ($canUpdateDaftarBarang || $canDeleteDaftarBarang)
                        <th scope="col" class="px-6 py-3 bg-gray-50">AKSI</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($barangs as $barang)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4 align-middle">{{ $barang['id'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['nama_item'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['stok'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['jenis'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['merek'] }}</td>
                        @if ($canAccessHargaPokok)
                            <td class="px-6 py-4 align-middle">{{ $barang['harga_pokok'] }}</td>
                        @endif
                        @if ($canAccessHargaJual)
                            <td class="px-6 py-4 align-middle">{{ $barang['harga_jual'] }}</td>
                        @endif
                        <td class="px-6 py-4 align-middle">{{ $barang['rak'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['keterangan'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['status'] }}</td>
                        @if ($canUpdateDaftarBarang || $canDeleteDaftarBarang)
                            <td class="px-6 py-4 align-middle">
                                <div class="flex justify-center items-center">
                                    @if ($canUpdateDaftarBarang)
                                        <a href="{{ route('daftarbarang.index', array_merge(request()->only(['search', 'gudang', 'sort_by', 'direction']), ['edit' => $barang['id']])) }}"
                                            class="font-medium text-yellow-300 hover:underline">
                                            Ubah
                                        </a>
                                    @endif
                                    @if ($barang['statusTransaksi'] === false && $canDeleteDaftarBarang)
                                        <a href="{{ route('daftarbarang.index', array_merge(request()->only(['search', 'gudang', 'sort_by', 'direction']), ['delete' => $barang['id']])) }}"
                                            class="font-medium text-red-600 hover:underline ml-3">
                                            Hapus
                                        </a>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-6 py-4 text-center text-gray-500">Data barang tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="py-4 px-4 mt-4">
        {{ $barangs->links() }}
    </div>

    {{-- Modal Tambah Barang --}}
    @if ($canCreateDaftarBarang)
        <x-master_data.daftarbarang.tambah-barang-modal :jenises="$jenises" :mereks="$mereks" />
    @endif
    @if ($barangs->isNotEmpty() && $canExportDaftarBarang)
        {{-- Modal Export --}}
        <x-master_data.daftarbarang.export-barang-modal :canAccessHargaPokok="$canAccessHargaPokok" :canAccessHargaJual="$canAccessHargaJual" />
    @endif
    @if ($editBarang && !$errors->any() && !session('error') && $canUpdateDaftarBarang)
        {{-- Modal Ubah Barang --}}
        <x-master_data.daftarbarang.ubah-barang-modal :barang="$editBarang" :jenises="$jenises" :mereks="$mereks" />
    @elseif ($deleteBarang && !$errors->any() && !session('error') && $canDeleteDaftarBarang)
        {{-- Modal Hapus Barang --}}
        <x-modal.modal-delete :action="route(
            'daftarbarang.destroy',
            ['daftarbarang' => $deleteBarang->id] + request()->only('search', 'gudang', 'sort_by', 'direction'),
        )"
            message='Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh data terkait. Apakah Anda yakin ingin menghapus barang dengan Nama Item "{{ $deleteBarang->nama_item }}"?' />
    @endif
</x-layout>

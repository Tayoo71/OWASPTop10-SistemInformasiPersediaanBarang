<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <!-- Search Box -->
    <form class="w-full max-w-lg lg:max-w-3xl xl:max-w-4xl mx-auto mb-4" method="GET" action="">
        <div class="flex justify-between items-center relative">
            <div class="flex flex-grow">
                <!-- Search Bar -->
                <div class="relative w-full flex">
                    <input type="search" id="search-dropdown" name="search"
                        class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-l-lg border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari Gudang" value="{{ request('search') }}" />
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

    <div class="flex justify-between items-center mb-4">
        <!-- Modal Trigger -->
        @if ($canCreateDaftarGudang)
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Tambah Gudang
            </button>
        @endif
        @if ($gudangs->isNotEmpty() && $canExportDaftarGudang)
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
                        <a href="{{ route('daftargudang.index', array_merge(request()->query(), ['sort_by' => 'kode_gudang', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            KODE GUDANG
                            @if (request('sort_by') === 'kode_gudang')
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
                        <a href="{{ route('daftargudang.index', array_merge(request()->query(), ['sort_by' => 'nama_gudang', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            NAMA GUDANG
                            @if (request('sort_by') === 'nama_gudang')
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
                        <a href="{{ route('daftargudang.index', array_merge(request()->query(), ['sort_by' => 'keterangan', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
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
                    @if ($canUpdateDaftarGudang || $canDeleteDaftarGudang)
                        <th scope="col" class="px-6 py-3 bg-gray-50">AKSI</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($gudangs as $gudang)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4 align-middle">{{ $gudang['kode_gudang'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $gudang['nama_gudang'] }}</td>
                        <td class="px-6 py-4 align-middle">
                            {{ $gudang['keterangan'] ? $gudang['keterangan'] : '-' }}
                        </td>
                        @if ($canUpdateDaftarGudang || $canDeleteDaftarGudang)
                            <td class="px-6 py-4 align-middle">
                                <div class="flex justify-center items-center">
                                    @if ($canUpdateDaftarGudang)
                                        <a href="{{ route('daftargudang.index', array_merge(request()->only(['search', 'sort_by', 'direction']), ['edit' => $gudang['kode_gudang']])) }}"
                                            class="font-medium text-yellow-300 hover:underline">
                                            Ubah
                                        </a>
                                    @endif
                                    @if ($gudang['statusTransaksi'] === false && $canDeleteDaftarGudang)
                                        <a href="{{ route('daftargudang.index', array_merge(request()->only(['search', 'sort_by', 'direction']), ['delete' => $gudang['kode_gudang']])) }}"
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
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Data Gudang tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="py-4 px-4 mt-4">
        {{ $gudangs->links() }}
    </div>

    {{-- Modal Tambah Gudang --}}
    @if ($canCreateDaftarGudang)
        <x-master_data.daftargudang.tambah-gudang-modal />
    @endif
    @if ($gudangs->isNotEmpty() && $canExportDaftarGudang)
        {{-- Modal Export --}}
        <x-master_data.daftargudang.export-gudang-modal />
    @endif
    @if ($editGudang && !$errors->any() && !session('error') && $canUpdateDaftarGudang)
        {{-- Modal Ubah Gudang --}}
        <x-master_data.daftargudang.ubah-gudang-modal :gudang="$editGudang" />
    @elseif ($deleteGudang && !$errors->any() && !session('error') && $canDeleteDaftarGudang)
        {{-- Modal Hapus Gudang --}}
        <x-modal.modal-delete :action="route(
            'daftargudang.destroy',
            ['daftargudang' => $deleteGudang->kode_gudang] + request()->only('search', 'sort_by', 'direction'),
        )"
            message='Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh data terkait. Apakah Anda yakin ingin menghapus Gudang "{{ $deleteGudang->nama_gudang }}"?' />
    @endif
</x-layout>

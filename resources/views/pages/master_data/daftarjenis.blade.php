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
                        placeholder="Cari Jenis" value="{{ request('search') }}" />
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
        @if ($canCreateDaftarJenis)
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Tambah Jenis
            </button>
        @endif
        @if ($jenises->isNotEmpty() && $canExportDaftarJenis)
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
                        <a href="{{ route('daftarjenis.index', array_merge(request()->query(), ['sort_by' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            KODE JENIS
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
                        <a href="{{ route('daftarjenis.index', array_merge(request()->query(), ['sort_by' => 'nama_jenis', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            NAMA JENIS
                            @if (request('sort_by') === 'nama_jenis')
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
                        <a href="{{ route('daftarjenis.index', array_merge(request()->query(), ['sort_by' => 'keterangan', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
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
                    @if ($canUpdateDaftarJenis || $canDeleteDaftarJenis)
                        <th scope="col" class="px-6 py-3 bg-gray-50">AKSI</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($jenises as $jenis)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4 align-middle">{{ $jenis['id'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $jenis['nama_jenis'] }}</td>
                        <td class="px-6 py-4 align-middle">
                            {{ $jenis['keterangan'] ? $jenis['keterangan'] : '-' }}
                        </td>
                        @if ($canUpdateDaftarJenis || $canDeleteDaftarJenis)
                            <td class="px-6 py-4 align-middle">
                                <div class="flex justify-center items-center">
                                    @if ($canUpdateDaftarJenis)
                                        <a href="{{ route('daftarjenis.index', array_merge(request()->only(['search', 'sort_by', 'direction']), ['edit' => $jenis['id']])) }}"
                                            class="font-medium text-yellow-300 hover:underline">
                                            Ubah
                                        </a>
                                    @endif
                                    @if ($canDeleteDaftarJenis)
                                        <a href="{{ route('daftarjenis.index', array_merge(request()->only(['search', 'sort_by', 'direction']), ['delete' => $jenis['id']])) }}"
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
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Data Jenis tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="py-4 px-4 mt-4">
        {{ $jenises->links() }}
    </div>

    @if ($canCreateDaftarJenis)
        <x-master_data.daftarjenis.tambah-jenis-modal />
    @endif
    @if ($jenises->isNotEmpty() && $canExportDaftarJenis)
        {{-- Modal Export --}}
        <x-master_data.daftarjenis.export-jenis-modal />
    @endif
    @if ($editJenis && !$errors->any() && !session('error') && $canUpdateDaftarJenis)
        {{-- Modal Ubah Jenis --}}
        <x-master_data.daftarjenis.ubah-jenis-modal :jenis="$editJenis" />
    @elseif ($deleteJenis && !$errors->any() && !session('error') && $canDeleteDaftarJenis)
        {{-- Modal Hapus Jenis --}}
        <x-modal.modal-delete :action="route(
            'daftarjenis.destroy',
            ['daftarjenis' => $deleteJenis->id] + request()->only('search', 'sort_by', 'direction'),
        )"
            message='Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh data terkait. Apakah Anda yakin ingin menghapus Jenis "{{ $deleteJenis->nama_jenis }}"?' />
    @endif
</x-layout>

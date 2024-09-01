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

    <!-- Wrapper untuk Tabel dan Button Tambah -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <!-- Tambah Barang Button -->
        <div class="flex justify-between items-center mb-4">
            <!-- Modal Trigger -->
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Tambah Jenis
            </button>
        </div>

        <!-- Tabel -->
        <table class="w-full text-sm text-center text-gray-500">
            <thead class="text-xs text-gray-700 bg-gray-50 sticky top-0 shadow-md">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50">KODE JENIS</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">NAMA JENIS</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">KETERANGAN</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jenises as $jenis)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4 align-middle">{{ $jenis['id'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $jenis['nama_jenis'] }}</td>
                        <td class="px-6 py-4 align-middle">
                            {{ $jenis['keterangan'] ? $jenis['keterangan'] : '-' }}
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex justify-center items-center">
                                <a href="{{ route('daftarjenis.index', array_merge(request()->only(['search']), ['edit' => $jenis['id']])) }}"
                                    class="font-medium text-blue-600 hover:underline">
                                    Ubah
                                </a>
                                <a href="{{ route('daftarjenis.index', array_merge(request()->only(['search']), ['delete' => $jenis['id']])) }}"
                                    class="font-medium text-red-600 hover:underline ml-3">
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="py-4 px-4 mt-4">
        {{ $jenises->links() }}
    </div>

    {{-- Modal Tambah Jenis --}}
    <x-tambah-jenis-modal />
    @if ($editJenis && !$errors->any() && !session('error'))
        {{-- Modal Ubah Jenis --}}
        <x-ubah-jenis-modal :jenis="$editJenis" />
    @elseif ($deleteJenis && !$errors->any() && !session('error'))
        {{-- Modal Hapus Jenis --}}
        <x-modal-delete :action="route('daftarjenis.destroy', ['daftarjenis' => $deleteJenis->id] + request()->only('search'))"
            message='Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh data terkait. Apakah Anda yakin ingin menghapus Jenis "{{ $deleteJenis->nama_jenis }}"?' />
    @endif
</x-layout>

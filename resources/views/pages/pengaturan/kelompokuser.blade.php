<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <form class="w-full max-w-lg lg:max-w-3xl xl:max-w-4xl mx-auto mb-4" method="GET" action="">
        <div class="flex justify-between items-center relative">
            <div class="flex flex-grow">
                <div class="relative w-full flex">
                    <input type="search" id="search-dropdown" name="search"
                        class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-l-lg border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari Kelompok" value="{{ request('search') }}" />
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
    <div class="flex justify-between items-center my-4">
        <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
            Tambah Kelompok
        </button>
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-s text-center text-gray-500">
            <thead class="text-sm text-gray-700 bg-gray-50 sticky top-0 shadow-md">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('kelompokuser.index', array_merge(request()->query(), ['sort_by' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            ID
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
                        <a href="{{ route('kelompokuser.index', array_merge(request()->query(), ['sort_by' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                            class="flex justify-center items-center">
                            NAMA KELOMPOK
                            @if (request('sort_by') === 'name')
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
                    <th scope="col" class="px-6 py-3 bg-gray-50">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4 align-middle">{{ $role['id'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $role['name'] }}</td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex justify-center items-center">
                                <a href="{{ route('kelompokuser.index', array_merge(request()->only(['search', 'sort_by', 'direction']), ['edit' => $role['id']])) }}"
                                    class="font-medium text-yellow-300 hover:underline">Ubah</a>
                                <a href="{{ route('kelompokuser.index', array_merge(request()->only(['search', 'sort_by', 'direction']), ['delete' => $role['id']])) }}"
                                    class="font-medium text-red-600 hover:underline ml-3">Hapus</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Data Kelompok tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="py-4 px-4 mt-4">
        {{ $roles->links() }}
    </div>

    <x-pengaturan.kelompokuser.tambah-kelompok-modal />
    @if ($editRole && !$errors->any() && !session('error'))
        {{-- Modal Ubah Jenis --}}
        <x-pengaturan.kelompokuser.ubah-kelompok-modal :role="$editRole" />
    @elseif ($deleteRole && !$errors->any() && !session('error'))
        {{-- Modal Hapus Jenis --}}
        <x-modal.modal-delete :action="route(
            'kelompokuser.destroy',
            ['kelompokuser' => $deleteRole->id] + request()->only('search', 'sort_by', 'direction'),
        )"
            message='Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh data terkait. Apakah Anda yakin ingin menghapus Kelompok "{{ $deleteRole->name }}"?' />
    @endif

    <x-auth.logout-form />
</x-layout>

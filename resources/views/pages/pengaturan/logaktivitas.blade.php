<x-header-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="h-screen flex bg-gray-200">
        <x-sidebar />
        <div class="flex-1 flex flex-col">
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b">
                <h3 class="text-3xl font-bold text-gray-900">{{ $title }}</h3>
            </header>
            <main class="flex-1 p-6 bg-gray-100 overflow-auto">
                <div class="text-center text-yellow-300 mb-4">
                    <p>Log Yang Disimpan Hanya 6 Bulan Terakhir.</p>
                </div>
                <form class="w-full max-w-lg lg:max-w-3xl xl:max-w-4xl mx-auto mb-4" method="GET" action="">
                    <div class="flex justify-center mb-4">
                        <div id="date-range-picker" date-rangepicker datepicker-buttons datepicker-format="dd/mm/yyyy"
                            class="flex items-center">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <input id="datepicker-range-start" name="start" type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                    placeholder="Pilih Tanggal Mulai" value="{{ request('start') }}">
                            </div>
                            <span class="mx-3 text-gray-500">Sampai</span>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <input id="datepicker-range-end" name="end" type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                    placeholder="Pilih Tanggal Akhir" value="{{ request('end') }}">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center relative">
                        <div class="flex flex-grow">
                            <div class="relative w-full flex">
                                <input type="search" id="search-dropdown" name="search"
                                    class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-l-lg border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Cari Log" value="{{ request('search') }}" />
                                <button type="submit"
                                    class="p-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-0 transition-none flex-shrink-0">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                    <span class="sr-only">Search</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-s text-center text-gray-500">
                        <thead class="text-sm text-gray-700 bg-gray-50 sticky top-0 shadow-md">
                            <tr>
                                <th scope="col" class="px-6 py-3 bg-gray-50">
                                    <a href="{{ route('logaktivitas.index', array_merge(request()->query(), ['direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="flex justify-center items-center">
                                        TANGGAL
                                        @if (request('direction') === 'asc')
                                            <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 9l4-4 4 4" />
                                            </svg>
                                        @elseif (request('direction') === 'desc')
                                            <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 15l-4 4-4-4" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 9l4-4 4 4M8 15l4 4 4-4" />
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 bg-gray-50">AKTIVITAS</th>
                                <th scope="col" class="px-6 py-3 bg-gray-50">USERNAME</th>
                                <th scope="col" class="px-6 py-3 bg-gray-50">PERANGKAT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">{{ $log['tanggal'] }}</td>
                                    <td class="px-6 py-4 align-middle">{{ $log['deskripsi'] }}</td>
                                    <td class="px-6 py-4 align-middle">{{ $log['user'] }}</td>
                                    <td class="px-6 py-4 align-middle">{{ $log['device'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Data Log tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="py-4 px-4 mt-4">
                    {{ $logs->links() }}
                </div>
            </main>
        </div>
    </div>
    <x-auth.logout-form />
</x-header-layout>

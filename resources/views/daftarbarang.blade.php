<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <form class="max-w-lg lg:max-w-3xl xl:max-w-4xl mx-auto mb-4" method="GET" action="">
        <div class="flex" x-data="{ dropdownOpen: false }">
            <label for="gudang-dropdown" class="mb-2 text-sm font-medium text-gray-900 sr-only">Gudang</label>
            <button id="dropdown-button" @click="dropdownOpen = !dropdownOpen"
                class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-l-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 transition-none"
                type="button">
                {{ request('gudang', 'Semua Gudang') }}
                <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="dropdownOpen" @click.outside="dropdownOpen = false"
                class="z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 absolute mt-12">
                <ul class="py-2 text-sm text-gray-700">
                    <!-- Opsi "Semua Gudang" -->
                    <li class="flex justify-center items-center">
                        <button type="submit" name="gudang" value=""
                            class="flex justify-center items-center w-full px-4 py-2 hover:bg-gray-100">
                            Semua Gudang
                        </button>
                    </li>
                    <!-- Opsi untuk setiap gudang -->
                    @foreach ($gudangs as $gudang)
                        <li class="flex justify-center items-center">
                            <button type="submit" name="gudang" value="{{ $gudang->kode_gudang }}"
                                class="flex justify-center items-center w-full px-4 py-2 hover:bg-gray-100">
                                {{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Search Bar With Button -->
            <div class="relative w-full flex">
                <input type="search" id="search-dropdown" name="search"
                    class="block p-2.5 w-full lg:w-2/3 xl:w-3/4 z-20 text-sm text-gray-900 bg-gray-50 rounded-r-none border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari Barang" value="{{ request('search') }}" />
                <button type="submit"
                    class="p-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-0 transition-none">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                    <span class="sr-only">Search</span>
                </button>
            </div>
        </div>
    </form>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-center text-gray-500">
            <thead class="text-xs text-gray-700 bg-gray-50 sticky top-0 shadow-md">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50">KODE ITEM</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">NAMA ITEM</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">STOK</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">JENIS</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">MEREK</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">HARGA POKOK</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">HARGA JUAL</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">RAK</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">KETERANGAN</th>
                    <th scope="col" class="px-6 py-3 bg-gray-50">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barangs as $barang)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4 align-middle">{{ $barang['kode_item'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['nama_item'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['stok'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['jenis'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['merek'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['harga_pokok'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['harga_jual'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['rak'] }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang['keterangan'] }}</td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex justify-center items-center">
                                <a href="#" class="font-medium text-blue-600 hover:underline">Ubah</a>
                                <a href="#" class="font-medium text-red-600 hover:underline ml-3">Hapus</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-layout>

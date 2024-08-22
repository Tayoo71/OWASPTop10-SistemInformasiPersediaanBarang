<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <form class="w-full max-w-lg lg:max-w-3xl xl:max-w-4xl mx-auto mb-4" method="GET" action="">
        <div class="flex justify-between items-center relative" x-data="{ dropdownOpen: false }">
            <div class="flex flex-grow">
                <!-- Dropdown Gudang -->
                <label for="gudang-dropdown" class="mb-2 text-sm font-medium text-gray-900 sr-only">Gudang</label>
                <button id="gudang-dropdown" @click="dropdownOpen = !dropdownOpen"
                    class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-l-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 transition-none"
                    type="button">
                    {{ request('gudang', 'Semua Gudang') }}
                    <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
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
                </div>

                <!-- Search Bar -->
                <div class="relative w-full flex">
                    <input type="search" id="search-dropdown" name="search"
                        class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-r-none border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari Barang" value="{{ request('search') }}" />
                    <button type="submit"
                        class="p-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-0 transition-none flex-shrink-0">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                        <span class="sr-only">Search</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Wrapper untuk Tabel dan Button Tambah Barang -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <!-- Tambah Barang Button -->
        <div class="flex justify-between items-center mb-4">
            <!-- Modal Trigger -->
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Tambah Barang
            </button>
        </div>

        <!-- Tabel -->
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

    <div class="py-4 px-4 mt-4">
        {{ $barangs->links() }}
    </div>

    <!-- Modal for Tambah Barang -->
    <div id="crud-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md md:max-w-2xl lg:max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Tambah Barang
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form class="p-4 md:p-5" x-data="{ konversiSatuan: [{ satuan: '', jumlah: '', harga_pokok: '', harga_jual: '' }] }">
                    <div class="grid gap-4 mb-4">
                        <div class="col-span-2">
                            <label for="nama_item"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                                Barang</label>
                            <input type="text" name="nama_item" id="nama_item"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Masukkan nama barang" required>
                        </div>

                        <div class="col-span-2">
                            <label for="jenis"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis</label>
                            <select name="jenis" id="jenis"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                <option value="">Pilih jenis</option>
                                @foreach ($jenises as $option)
                                    <option value="{{ $option->id }}">{{ $option->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label for="merek"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Merek</label>
                            <select name="merek" id="merek"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                <option value="">Pilih merek</option>
                                @foreach ($mereks as $option)
                                    <option value="{{ $option->id }}">{{ $option->nama_merek }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label for="rak"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rak</label>
                            <input type="text" name="rak" id="rak"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Masukkan lokasi rak" required>
                        </div>

                        <div class="col-span-2">
                            <label for="keterangan"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" rows="3"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Masukkan keterangan"></textarea>
                        </div>

                        <div class="col-span-2">
                            <label for="stok_minimum"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stok
                                Minimum</label>
                            <input type="number" name="stok_minimum" id="stok_minimum"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Masukkan stok minimum" required>
                        </div>

                        <div class="col-span-2">
                            <label for="konversi_satuan"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konversi
                                Satuan</label>

                            <template x-for="(satuan, index) in konversiSatuan" :key="index">
                                <div class="flex space-x-2 mb-2">
                                    <input type="text" x-model="satuan.satuan" placeholder="Nama Satuan"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                        required>
                                    <input type="number" x-model="satuan.jumlah" placeholder="Jumlah"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                        required>
                                    <input type="number" x-model="satuan.harga_pokok" placeholder="Harga Pokok"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                        required>
                                    <input type="number" x-model="satuan.harga_jual" placeholder="Harga Jual"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                        required>
                                    <button type="button" @click="konversiSatuan.splice(index, 1)"
                                        class="text-red-500 hover:text-red-700">Hapus</button>
                                </div>
                            </template>
                            <button type="button"
                                @click="konversiSatuan.push({ satuan: '', jumlah: '', harga_pokok: '', harga_jual: '' })"
                                class="mt-2 text-sm text-blue-500 hover:text-blue-700">+ Tambah Satuan</button>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Tambah Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>

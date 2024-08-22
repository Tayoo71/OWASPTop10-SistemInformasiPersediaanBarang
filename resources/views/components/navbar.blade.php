<nav class="bg-gray-800" x-data="{ isOpen: false, isMasterDataOpen: false, isTransaksiOpen: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500"
                        alt="Logo Perusahaan">
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/"
                            class="rounded-md {{ request()->is('/') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Home</a>

                        <div class="relative">
                            <button @click="isMasterDataOpen = !isMasterDataOpen"
                                class="rounded-md px-3 py-2 text-sm font-medium {{ request()->is(['daftarbarang', 'daftargudang', 'kartustok']) ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                Master Data
                            </button>
                            <div x-show="isMasterDataOpen" @click.away="isMasterDataOpen = false"
                                x-transition:enter="transition ease-out duration-100 transform"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75 transform"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-48 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                id="master-data-dropdown">
                                <a href="/daftarbarang"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('daftarbarang') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Daftar
                                    Barang</a>
                                <a href="/kartustok"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('kartustok') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Kartu
                                    Stok</a>
                                <a href="/daftargudang"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('daftargudang') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Daftar
                                    Gudang</a>
                                <a href="/daftarjenis"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('daftarjenis') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Daftar
                                    Jenis</a>
                                <a href="/daftarmerek"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('daftarmerek') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Daftar
                                    Merek</a>
                            </div>
                        </div>

                        <div class="relative">
                            <button @click="isTransaksiOpen = !isTransaksiOpen"
                                class="rounded-md px-3 py-2 text-sm font-medium {{ request()->is(['barangmasuk', 'barangkeluar', 'stokopname', 'itemtransfer']) ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                Transaksi
                            </button>
                            <div x-show="isTransaksiOpen" @click.away="isTransaksiOpen = false"
                                x-transition:enter="transition ease-out duration-100 transform"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75 transform"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-48 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                id="transaksi-dropdown">
                                <a href="/barangmasuk"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('barangmasuk') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Barang
                                    Masuk</a>
                                <a href="/barangkeluar"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('barangkeluar') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Barang
                                    Keluar</a>
                                <a href="/stokopname"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('stokopname') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Stok
                                    Opname</a>
                                <a href="/itemtransfer"
                                    class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('itemtransfer') ? 'bg-gray-300' : 'hover:bg-gray-100' }}">Item
                                    Transfer</a>
                            </div>
                        </div>

                        <a href="/laporan"
                            class="rounded-md {{ request()->is('laporan') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Laporan
                            Daftar Barang</a>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <div class="relative ml-3">
                        <div>
                            <button type="button" @click="isOpen = !isOpen"
                                class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                id="user-menu-button">
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">Open user menu</span>
                                <div class="underline text-base font-medium leading-none text-white">Halo, Pengguna
                                </div>
                            </button>
                        </div>

                        <div x-show="isOpen" @click.away="isOpen = false"
                            x-transition:enter="transition ease-out duration-100 transform"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75 transform"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            role="menu" tabindex="-1">
                            <a href="#"
                                class="block px-4 py-2 text-sm text-gray-700 {{ request()->is('pengaturan') ? 'bg-gray-300' : 'hover:bg-gray-100' }} role="menuitem"
                                tabindex="-1" id="user-menu-item-1">Pengaturan</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem" tabindex="-1" id="user-menu-item-2">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <button type="button" @click="isOpen = !isOpen"
                    class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                    <span class="absolute -inset-0.5"></span>
                    <span class="sr-only">Open main menu</span>
                    <svg :class="{ 'hidden': isOpen, 'block': !isOpen }" class="block h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg :class="{ 'block': isOpen, 'hidden': !isOpen }" class="hidden h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="isOpen" @click.away="isOpen = false" class="md:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
            <a href="/"
                class="block rounded-md px-3 py-2 text-base font-medium {{ request()->is('/') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Home</a>
            <p
                class="block rounded-md px-3 py-2 text-base font-medium {{ request()->is(['daftarbarang', 'daftargudang', 'kartustok']) ? 'bg-gray-900 text-white' : 'text-gray-400' }}">
                Master Data</p>
            <a href="/daftarbarang"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('daftarbarang') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Daftar
                Barang</a>
            <a href="/kartustok"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('kartustok') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Kartu
                Stok</a>
            <a href="/daftargudang"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('daftargudang') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Daftar
                Gudang</a>
            <a href="/daftarjenis"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('daftarjenis') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Daftar
                Jenis</a>
            <a href="/daftarmerek"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('daftarmerek') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Daftar
                Merek</a>
            <p
                class="block rounded-md px-3 py-2 text-base font-medium {{ request()->is(['barangmasuk', 'barangkeluar', 'stokopname', 'itemtransfer']) ? 'bg-gray-900 text-white' : 'text-gray-400' }}">
                Transaksi</p>
            <a href="/barangmasuk"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('barangmasuk') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Barang
                Masuk</a>
            <a href="/barangkeluar"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('barangkeluar') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Barang
                Keluar</a>
            <a href="/stokopname"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('stokopname') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Stok
                Opname</a>
            <a href="/itemtransfer"
                class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('itemtransfer') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Item
                Transfer</a>
            <a href="/laporan"
                class="block rounded-md px-3 py-2 text-base font-medium {{ request()->is('laporan') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Laporan
                Daftar Barang</a>
        </div>
        <div class="border-t border-gray-700 pb-3 pt-4">
            <div class="flex items-center px-5">
                <div class="">
                    <div class="text-base font-medium leading-none text-gray-400">Halo, Pengguna</div>
                </div>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <a href="#"
                    class="block rounded-md px-8 py-1 text-base font-medium {{ request()->is('pengaturan') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Pengaturan</a>
                <a href="#"
                    class="block rounded-md px-8 py-1 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Logout</a>
            </div>
        </div>
    </div>
</nav>

        <div class="w-64 bg-gray-800 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-center mt-8">
                    <span class="text-2xl font-semibold text-white">Pengaturan</span>
                </div>
                <nav class="mt-10">
                    @if (auth()->user()->can('user_manajemen.akses'))
                        <a class="flex items-center px-6 py-2 {{ request()->is('daftaruser') ? 'bg-gray-700 text-gray-100' : 'text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}"
                            href="{{ route('daftaruser.index') }}">
                            <span class="mx-3">Daftar User</span>
                        </a>
                        <a class="flex items-center px-6 py-2 mt-4 {{ request()->is('kelompokuser') ? 'bg-gray-700 text-gray-100' : 'text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}"
                            href="{{ route('kelompokuser.index') }}">
                            <span class="mx-3">Kelompok User</span>
                        </a>
                        <a class="flex items-center px-6 py-2 mt-4 {{ request()->is('akseskelompok') ? 'bg-gray-700 text-gray-100' : 'text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}"
                            href="{{ route('akseskelompok.index') }}">
                            <span class="mx-3">Akses Kelompok</span>
                        </a>
                    @endif
                    @if (auth()->user()->can('log_aktivitas.akses'))
                        <a class="flex items-center px-6 py-2 mt-4 {{ request()->is('logaktivitas') ? 'bg-gray-700 text-gray-100' : 'text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}"
                            href="{{ route('logaktivitas.index') }}">
                            <span class="mx-3">Log Aktivitas</span>
                        </a>
                    @endif
                </nav>
            </div>
            <div class="mb-4">
                <a class="flex items-center justify-center px-6 py-2 text-gray-100 hover:bg-gray-700 hover:bg-opacity-25"
                    href="{{ route('home_page') }}">
                    <span class="mr-2">
                        << </span>
                            <span>Kembali ke Halaman Utama</span>
                </a>
                <a class="flex items-center justify-center px-6 py-2 mt-4 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                    href="#logout-form"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span>Logout</span>
                </a>
            </div>
        </div>

<x-modal.modal-update title="Ubah User">
    <form method="POST"
        action="{{ route('daftaruser.update', $editUser['id']) }}?{{ http_build_query(request()->only(['search', 'sort_by', 'direction'])) }}"
        class="p-4 md:p-5">
        @csrf
        @method('PUT')
        <div class="grid gap-4 mb-4">
            <div class="col-span-2">
                <label for="ubah_id"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                <input type="text" name="ubah_id" id="ubah_id" value="{{ $editUser['id'] }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Ubah Username" required>
            </div>
        </div>
        <div class="grid gap-4 mb-4 relative">
            <div class="col-span-2">
                <label for="ubah_password"
                    class="block text-sm font-medium text-gray-900 dark:text-white">Password</label>
                <p class="mb-3 text-xs text-red-600 dark:text-red-400">
                    Dengan Alasan Keamanan, Password Saat Ini Tidak Dapat Ditampilkan. Silakan Masukkan Password Baru
                    Jika Ingin Melakukan Perubahan.
                </p>
                <div class="flex items-center relative">
                    <input type="password" name="ubah_password" id="ubah_password" maxlength="50" minlength="12"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 pr-12 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Ubah Password">
                    <button type="button" onclick="togglePasswordVisibility('ubah_password', 'ubah_eyeIcon')"
                        class="absolute right-8 text-gray-500 mr-2">
                        <svg id="ubah_eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                    <button type="button" onclick="generatePassword('ubah_password')"
                        class="absolute right-2 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 400 400">
                            <g>
                                <path
                                    d="M62.772,95.042C90.904,54.899,137.496,30,187.343,30c83.743,0,151.874,68.13,151.874,151.874h30   C369.217,81.588,287.629,0,187.343,0c-35.038,0-69.061,9.989-98.391,28.888C70.368,40.862,54.245,56.032,41.221,73.593   L2.081,34.641v113.365h113.91L62.772,95.042z" />
                                <path
                                    d="M381.667,235.742h-113.91l53.219,52.965c-28.132,40.142-74.724,65.042-124.571,65.042   c-83.744,0-151.874-68.13-151.874-151.874h-30c0,100.286,81.588,181.874,181.874,181.874c35.038,0,69.062-9.989,98.391-28.888   c18.584-11.975,34.707-27.145,47.731-44.706l39.139,38.952V235.742z" />
                            </g>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-span-2 mb-4">
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Autentikasi Dua Faktor (2FA)</label>
            <div class="flex items-center justify-between mt-2">
                <p class="text-sm {{ $editUser['is_2fa_active'] ? 'text-green-600' : 'text-gray-500' }}">
                    {{ $editUser['is_2fa_active'] ? 'Aktif' : 'Tidak Aktif' }}
                </p>
                @if ($editUser['is_2fa_active'])
                    <label class="flex items-center space-x-2 text-red-500 font-medium text-sm">
                        <input type="checkbox" name="reset_2fa" id="reset_2fa"
                            class="rounded focus:ring-primary-500 focus:border-primary-500">
                        <span>Reset 2FA</span>
                    </label>
                @else
                    <p class="text-xs text-gray-500 mt-1">Autentikasi 2FA Tidak Aktif. Reset Hanya Tersedia Jika 2FA
                        Sedang Aktif.</p>
                @endif
            </div>
        </div>
        <div class="col-span-2 mb-4">
            <label for="ubah_role_id"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelompok</label>
            <select name="ubah_role_id" id="ubah_role_id" required
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                <option value="" disabled selected>Pilih Kelompok</option>
                @foreach ($roles as $id => $name)
                    <option value="{{ $id }}" {{ $editUser['role_id'] == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2 mb-4">
            <label for="ubah_status" class="block text-sm font-medium text-gray-900 dark:text-white">Status
                User</label>
            <p class="mb-3 text-xs text-red-600 dark:text-red-400">
                User yang berstatus "Tidak Aktif" tidak dapat masuk atau Login ke dalam Aplikasi.
            </p>
            <select name="ubah_status" id="ubah_status"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                <option value="Aktif" {{ $editUser['status'] === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Tidak Aktif" {{ $editUser['status'] === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif
                </option>
            </select>
        </div>
        <div class="flex justify-center">
            <button type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Ubah User
            </button>
        </div>
    </form>
</x-modal.modal-update>

<x-modal.modal-create title="Tambah User">
    <form method="POST"
        action="{{ route('daftaruser.store') }}?{{ http_build_query(request()->only(['search', 'sort_by', 'direction'])) }}"
        class="p-4 md:p-5">
        @csrf
        <div class="grid gap-4 mb-4">
            <div class="col-span-2">
                <label for="id"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                <input type="text" name="id" id="id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Masukkan Username" required>
            </div>
        </div>
        <div class="grid gap-4 mb-4 relative">
            <div class="col-span-2">
                <label for="password"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                <div class="flex items-center relative">
                    <input type="password" name="password" id="password" maxlength="50" minlength="12"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 pr-12 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Masukkan Password" required>
                    <button type="button" onclick="togglePasswordVisibility()"
                        class="absolute right-8 text-gray-500 mr-2">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                    <button type="button" onclick="generatePassword()" class="absolute right-2 text-gray-500">
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
            <label for="role_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelompok</label>
            <select name="role_id" id="role_id" required
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                <option value="" disabled selected>Pilih Kelompok</option>
                @foreach ($roles as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2 mb-4">
            <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Status
                User</label>
            <p class="mb-3 text-xs text-red-600 dark:text-red-400">
                User yang berstatus "Tidak Aktif" tidak dapat masuk atau Login ke dalam Aplikasi.
            </p>
            <select name="status" id="status"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                <option value="Aktif">Aktif</option>
                <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
        </div>
        <div class="flex justify-center">
            <button type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Tambah User
            </button>
        </div>
    </form>
</x-modal.modal-create>
@push('scripts')
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z"/>
                <circle cx="12" cy="12" r="3" />
                <path d="M1 1l22 22" stroke="currentColor" stroke-width="2" /> <!-- Slash for eye-off effect -->
            `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z"/>
                <circle cx="12" cy="12" r="3" />
            `;
            }
        }

        function generatePassword(length = 30) {
            const passwordInput = document.getElementById('password');
            const crypto = window.crypto || window.msCrypto;
            if (typeof crypto === 'undefined') {
                throw new Error('Crypto API is not supported. Please upgrade your web browser');
            }
            const charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()';
            const indexes = crypto.getRandomValues(new Uint32Array(length));
            let secret = '';
            for (const index of indexes) {
                secret += charset[index % charset.length];
            }
            passwordInput.value = secret;
        }
    </script>
@endpush

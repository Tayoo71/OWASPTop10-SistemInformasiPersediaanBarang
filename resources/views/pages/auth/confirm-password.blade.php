<x-header-layout>
    <x-slot:title>2FA</x-slot:title>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-10 w-auto" src="{{ asset('images/logo/logo_perusahaan.png') }}" alt="Logo Perusahaan">
            <h2 id="titlePageLabel" class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
                Masukkan Password Anda untuk Melanjutkan Tindakan Ini
            </h2>
        </div>

        <div class="mt-5 sm:mx-auto sm:w-full sm:max-w-sm">
            <x-display-error />

            <form id="twoFactorForm" class="space-y-6" action="{{ url('/user/confirm-password') }}" method="POST">
                @csrf
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                    </div>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="off" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-700 sm:text-sm/6">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-blue-700 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                        Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-header-layout>

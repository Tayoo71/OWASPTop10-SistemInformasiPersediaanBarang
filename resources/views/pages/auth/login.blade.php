<x-header-layout>
    <x-slot:title>Login</x-slot:title>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-10 w-auto" src="{{ asset('images/logo/logo_perusahaan.png') }}" alt="Logo Perusahaan">
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Silahkan Login ke akun Anda
            </h2>
        </div>

        <div class="mt-5 sm:mx-auto sm:w-full sm:max-w-sm">
            <x-display-error />
            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf
                <div>
                    <label for="id" class="block text-sm/6 font-medium text-gray-900">Username</label>
                    <div class="mt-2">
                        <input id="id" name="id" type="text" autocomplete="off" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-700 sm:text-sm/6">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                    </div>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="off" required maxlength="50"
                            minlength="12"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-700 sm:text-sm/6">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-blue-700 hover:bg-blue-800 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm  focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 ">Login</button>
                </div>

            </form>
        </div>
    </div>
</x-header-layout>

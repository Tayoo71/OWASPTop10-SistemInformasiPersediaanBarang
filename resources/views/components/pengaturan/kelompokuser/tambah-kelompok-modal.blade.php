<x-modal.modal-create title="Tambah Kelompok User">
    <form method="POST"
        action="{{ route('kelompokuser.store') }}?{{ http_build_query(request()->only(['search', 'sort_by', 'direction'])) }}"
        class="p-4 md:p-5">
        @csrf
        <div class="grid gap-4 mb-4">
            <div class="col-span-2">
                <label for="nama" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                    Kelompok</label>
                <input type="text" name="nama" id="nama"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Masukkan Nama Kelompok" required>
            </div>
        </div>

        <div class="flex justify-center">
            <button type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Tambah Kelompok
            </button>
        </div>
    </form>
</x-modal.modal-create>

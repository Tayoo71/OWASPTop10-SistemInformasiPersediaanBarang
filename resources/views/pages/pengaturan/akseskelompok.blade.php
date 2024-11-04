<x-header-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="h-screen flex bg-gray-200">
        <x-sidebar />
        <div class="flex-1 flex flex-col">
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b">
                <h3 class="text-3xl font-bold text-gray-900">{{ $title }}</h3>
            </header>
            <main class="flex-1 p-6 bg-gray-100 overflow-auto">
                <div x-data="{ roles: ['Admin', 'User'] }" class="mb-4">
                    <div class="flex items-center space-x-4">
                        <select
                            class="flex-grow block p-2.5 text-gray-900 bg-white border rounded-lg focus:ring focus:ring-blue-300"
                            x-model="roles[0]">
                            <template x-for="role in roles" :key="role">
                                <option x-text="role"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Simpan Akses User
                    </button>
                </div>
                <!-- Matrix RBAC CRUD -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-center text-gray-500">
                        <thead class="text-sm text-gray-700 bg-gray-50 sticky top-0 shadow-md">
                            <tr>
                                <th class="px-6 py-3 bg-gray-50">FITUR</th>
                                <th class="px-6 py-3 bg-gray-50">BUKA</th>
                                <th class="px-6 py-3 bg-gray-50">BARU</th>
                                <th class="px-6 py-3 bg-gray-50">UBAH</th>
                                <th class="px-6 py-3 bg-gray-50">HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Daftar Barang -->
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <td class="px-6 py-4 align-middle">Daftar Barang</td>
                                <td class="px-6 py-4 align-middle">
                                    <input type="checkbox" name="permissions[daftar_barang][read]"
                                        class="form-checkbox text-blue-600">
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <input type="checkbox" name="permissions[daftar_barang][create]"
                                        class="form-checkbox text-blue-600">
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <input type="checkbox" name="permissions[daftar_barang][update]"
                                        class="form-checkbox text-blue-600">
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <input type="checkbox" name="permissions[daftar_barang][delete]"
                                        class="form-checkbox text-blue-600">
                                </td>
                            </tr>

                            <!-- Daftar Merek -->
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <td class="px-6 py-4 align-middle">Daftar Merek</td>
                                <td class="px-6 py-4 align-middle">
                                    <input type="checkbox" name="permissions[daftar_merek][read]"
                                        class="form-checkbox text-blue-600">
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <input type="checkbox" name="permissions[daftar_merek][create]"
                                        class="form-checkbox text-blue-600">
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <input type="checkbox" name="permissions[daftar_merek][update]"
                                        class="form-checkbox text-blue-600">
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <input type="checkbox" name="permissions[daftar_merek][delete]"
                                        class="form-checkbox text-blue-600">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </form>
            </main>
        </div>
    </div>
    <x-auth.logout-form />
</x-header-layout>

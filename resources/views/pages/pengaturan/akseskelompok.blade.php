<x-header-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="h-screen flex bg-gray-200">
        <x-sidebar />
        <div class="flex-1 flex flex-col">
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b">
                <h3 class="text-3xl font-bold text-gray-900">{{ $title }}</h3>
            </header>
            <main class="flex-1 p-6 bg-gray-100 overflow-auto">
                <x-display-error />
                <form action="{{ route('akseskelompok.update') }}?{{ http_build_query(request()->only(['role'])) }}""
                    method="POST" x-data="{ role_id: '{{ request('role_id') }}' }">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <div class="flex items-center space-x-4">
                            <select name= "role_id" x-model="role_id"
                                @change="if (role_id) window.location.href = `?role_id=${role_id}`"
                                class="flex-grow block p-2.5 text-gray-900 bg-white border rounded-lg focus:ring focus:ring-blue-300">
                                <option value="" disabled selected>Pilih Kelompok</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
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
                                    <th class="px-6 py-3 bg-gray-50">AKSES</th>
                                    <th class="px-6 py-3 bg-gray-50">BUKA</th>
                                    <th class="px-6 py-3 bg-gray-50">BARU</th>
                                    <th class="px-6 py-3 bg-gray-50">UBAH</th>
                                    <th class="px-6 py-3 bg-gray-50">HAPUS</th>
                                    <th class="px-6 py-3 bg-gray-50">CETAK & KONVERSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Daftar Barang</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_barang.read]"
                                            {{ in_array('daftar_barang.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_barang.create]"
                                            {{ in_array('daftar_barang.create', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_barang.update]"
                                            {{ in_array('daftar_barang.update', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_barang.delete]"
                                            {{ in_array('daftar_barang.delete', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_barang.export]"
                                            {{ in_array('daftar_barang.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Tampil Harga Pokok Pada Daftar Barang</td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_barang.harga_pokok]"
                                            {{ in_array('daftar_barang.harga_pokok', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Tampil Harga Jual Pada Daftar Barang</td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_barang.harga_jual]"
                                            {{ in_array('daftar_barang.harga_jual', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Kartu Stok</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[kartu_stok.read]"
                                            {{ in_array('kartu_stok.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td />
                                    <td />
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[kartu_stok.export]"
                                            {{ in_array('kartu_stok.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Daftar Gudang</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_gudang.read]"
                                            {{ in_array('daftar_gudang.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_gudang.create]"
                                            {{ in_array('daftar_gudang.create', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_gudang.update]"
                                            {{ in_array('daftar_gudang.update', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_gudang.delete]"
                                            {{ in_array('daftar_gudang.delete', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_gudang.export]"
                                            {{ in_array('daftar_gudang.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Daftar Jenis</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_jenis.read]"
                                            {{ in_array('daftar_jenis.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_jenis.create]"
                                            {{ in_array('daftar_jenis.create', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_jenis.update]"
                                            {{ in_array('daftar_jenis.update', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_jenis.delete]"
                                            {{ in_array('daftar_jenis.delete', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_jenis.export]"
                                            {{ in_array('daftar_jenis.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Daftar Merek</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_merek.read]"
                                            {{ in_array('daftar_merek.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_merek.create]"
                                            {{ in_array('daftar_merek.create', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_merek.update]"
                                            {{ in_array('daftar_merek.update', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_merek.delete]"
                                            {{ in_array('daftar_merek.delete', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[daftar_merek.export]"
                                            {{ in_array('daftar_merek.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Informasi Stok Minimum</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[stok_minimum.read]"
                                            {{ in_array('stok_minimum.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td />
                                    <td />
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[stok_minimum.export]"
                                            {{ in_array('stok_minimum.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Barang Masuk</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_masuk.read]"
                                            {{ in_array('barang_masuk.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_masuk.create]"
                                            {{ in_array('barang_masuk.create', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_masuk.update]"
                                            {{ in_array('barang_masuk.update', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_masuk.delete]"
                                            {{ in_array('barang_masuk.delete', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_masuk.export]"
                                            {{ in_array('barang_masuk.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Barang Keluar</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_keluar.read]"
                                            {{ in_array('barang_keluar.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_keluar.create]"
                                            {{ in_array('barang_keluar.create', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_keluar.update]"
                                            {{ in_array('barang_keluar.update', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_keluar.delete]"
                                            {{ in_array('barang_keluar.delete', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[barang_keluar.export]"
                                            {{ in_array('barang_keluar.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Item Transfer</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[item_transfer.read]"
                                            {{ in_array('item_transfer.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[item_transfer.create]"
                                            {{ in_array('item_transfer.create', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[item_transfer.update]"
                                            {{ in_array('item_transfer.update', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[item_transfer.delete]"
                                            {{ in_array('item_transfer.delete', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[item_transfer.export]"
                                            {{ in_array('item_transfer.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Stok Opname</td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[stok_opname.read]"
                                            {{ in_array('stok_opname.read', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[stok_opname.create]"
                                            {{ in_array('stok_opname.create', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td />
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[stok_opname.delete]"
                                            {{ in_array('stok_opname.delete', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[stok_opname.export]"
                                            {{ in_array('stok_opname.export', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Tampil Stok Saat Ini Pada Transaksi</td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[transaksi.tampil_stok]"
                                            {{ in_array('transaksi.tampil_stok', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">User Manajemen (Daftar User, Kelompok User,
                                        Akses Kelompok)</td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[user_manajemen.akses]"
                                            {{ in_array('user_manajemen.akses', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50 border-b">
                                    <td class="px-6 py-4 align-middle">Log Aktivitas</td>
                                    <td class="px-6 py-4 align-middle">
                                        <input type="checkbox" name="permissions[log_aktivitas.akses]"
                                            {{ in_array('log_aktivitas.akses', $permissions) ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                    </td>
                                    <td />
                                    <td />
                                    <td />
                                    <td />
                                    <td />
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

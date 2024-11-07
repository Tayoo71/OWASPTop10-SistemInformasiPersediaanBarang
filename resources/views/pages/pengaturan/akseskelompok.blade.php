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
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-s text-center text-gray-500">
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
                                @foreach ($featuresName as $feature)
                                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                                        <td class="px-6 py-4 align-middle">{{ $feature['name'] }}</td>
                                        <td class="px-6 py-4 align-middle">
                                            @if (in_array('akses', $feature['actions']))
                                                <input type="checkbox"
                                                    name="permissions[{{ $feature['feature'] }}.akses]"
                                                    class="form-checkbox text-blue-600"
                                                    {{ in_array("{$feature['feature']}.akses", $permissions) ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            @if (in_array('read', $feature['actions']))
                                                <input type="checkbox"
                                                    name="permissions[{{ $feature['feature'] }}.read]"
                                                    class="form-checkbox text-blue-600"
                                                    {{ in_array("{$feature['feature']}.read", $permissions) ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            @if (in_array('create', $feature['actions']))
                                                <input type="checkbox"
                                                    name="permissions[{{ $feature['feature'] }}.create]"
                                                    class="form-checkbox text-blue-600"
                                                    {{ in_array("{$feature['feature']}.create", $permissions) ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            @if (in_array('update', $feature['actions']))
                                                <input type="checkbox"
                                                    name="permissions[{{ $feature['feature'] }}.update]"
                                                    class="form-checkbox text-blue-600"
                                                    {{ in_array("{$feature['feature']}.update", $permissions) ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            @if (in_array('delete', $feature['actions']))
                                                <input type="checkbox"
                                                    name="permissions[{{ $feature['feature'] }}.delete]"
                                                    class="form-checkbox text-blue-600"
                                                    {{ in_array("{$feature['feature']}.delete", $permissions) ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            @if (in_array('export', $feature['actions']))
                                                <input type="checkbox"
                                                    name="permissions[{{ $feature['feature'] }}.export]"
                                                    class="form-checkbox text-blue-600"
                                                    {{ in_array("{$feature['feature']}.export", $permissions) ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </main>
        </div>
    </div>
    <x-auth.logout-form />
</x-header-layout>

<?php

namespace App\Http\Controllers\Pengaturan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Pengaturan\AksesKelompok\ViewAksesKelompokRequest;
use App\Http\Requests\Pengaturan\AksesKelompok\UpdateAksesKelompokRequest;

class AksesKelompokController extends Controller
{
    public function index(ViewAksesKelompokRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $roles = Role::select('id', 'name')
                ->where('id', '!=', 1)
                ->get();

            if (!(empty($validatedData['role_id']))) {
                $role = Role::findOrFail($validatedData['role_id']);
                $permissions = $role->permissions->pluck('name')->toArray();
            }

            return view('pages/pengaturan/akseskelompok', [
                'title' => 'Akses Kelompok',
                'roles' => $roles,
                'permissions' => $permissions ?? []
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Akses Kelompok pada halaman Akses Kelompok. ', 'home_page');
        }
    }
    public function update(UpdateAksesKelompokRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            $role = Role::findOrFail($filteredData['role_id']);

            $permissions = array_keys($filteredData['permissions']);

            // Pastikan semua permission tersedia di database
            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName]);
            }

            // Sinkronisasi permission ke role
            // Hanya izin yang ada di array $permissions yang akan tetap ada
            $role->syncPermissions($permissions);

            DB::commit();
            return redirect()->route('akseskelompok.index', $this->buildQueryParams($request, "AksesKelompokController"))->with('success', 'Data Akses Kelompok berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Akses Kelompok. ', redirect: 'akseskelompok.index');
        }
    }
}

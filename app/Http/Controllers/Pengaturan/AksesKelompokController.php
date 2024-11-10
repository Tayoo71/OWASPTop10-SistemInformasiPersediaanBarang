<?php

namespace App\Http\Controllers\Pengaturan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Pengaturan\AksesKelompok\ViewAksesKelompokRequest;
use App\Http\Requests\Pengaturan\AksesKelompok\UpdateAksesKelompokRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AksesKelompokController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:user_manajemen.akses', only: ['index', 'store', 'update']),
        ];
    }
    // Fitur Aplikasi yang akan diatur aksesnya
    private $features = [
        'daftar_barang' => ['read', 'create', 'update', 'delete', 'export'],
        'daftar_barang.harga_pokok' => ['akses'],
        'daftar_barang.harga_jual' => ['akses'],
        'kartu_stok' => ['read', 'export'],
        'daftar_gudang' => ['read', 'create', 'update', 'delete', 'export'],
        'daftar_jenis' => ['read', 'create', 'update', 'delete', 'export'],
        'daftar_merek' => ['read', 'create', 'update', 'delete', 'export'],
        'stok_minimum' => ['read', 'export'],
        'barang_masuk' => ['read', 'create', 'update', 'delete', 'export'],
        'barang_keluar' => ['read', 'create', 'update', 'delete', 'export'],
        'item_transfer' => ['read', 'create', 'update', 'delete', 'export'],
        'stok_opname' => ['read', 'create', 'delete', 'export'],
        'transaksi.tampil_stok' => ['akses'],
        'user_manajemen' => ['akses'],
        'log_aktivitas' => ['akses'],
    ];
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
                'permissions' => $permissions ?? [],
                'featuresName' => $this->getFormattedFeatures($this->features)
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Akses Kelompok pada halaman Akses Kelompok. ', 'home_page');
        }
    }
    public function update(UpdateAksesKelompokRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->setFeatures($this->features);
            $filteredData = $request->validated();

            $role = Role::findOrFail($filteredData['role_id']);

            $permissions = !(empty($filteredData['permissions'])) ? array_keys($filteredData['permissions']) : [];

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
    private function getFormattedFeatures($features)
    {
        // Nama khusus untuk beberapa fitur
        $customNames = [
            'daftar_barang.harga_pokok' => 'Tampil Harga Pokok Pada Daftar Barang',
            'daftar_barang.harga_jual' => 'Tampil Harga Jual Pada Daftar Barang',
            'user_manajemen' => 'User Manajemen (Daftar User, Kelompok User, Akses Kelompok)',
            'transaksi.tampil_stok' => 'Tampil Stok Saat Ini Pada Transaksi'
        ];

        // Proses seluruh fitur untuk menghasilkan array yang sudah diformat
        $formattedFeatures = [];
        foreach ($features as $feature => $actions) {
            // Format nama fitur: gunakan nama khusus atau nama default
            $formattedName = $customNames[$feature] ?? ucwords(str_replace('_', ' ', $feature));

            // Tambahkan ke array hasil dengan nama dan aksi yang terkait
            $formattedFeatures[] = [
                'name' => $formattedName,
                'feature' => $feature,
                'actions' => $actions
            ];
        }
        return $formattedFeatures;
    }
}

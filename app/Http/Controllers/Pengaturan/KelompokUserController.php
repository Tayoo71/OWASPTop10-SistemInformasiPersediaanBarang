<?php

namespace App\Http\Controllers\Pengaturan;

use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Pengaturan\KelompokUser\ViewKelompokUSerRequest;
use App\Http\Requests\Pengaturan\KelompokUser\StoreKelompokUserRequest;
use App\Http\Requests\Pengaturan\KelompokUser\UpdateKelompokUserRequest;
use App\Http\Requests\Pengaturan\KelompokUser\DestroyKelompokUserRequest;

class KelompokUserController extends Controller implements HasMiddleware
{
    use LogActivity;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:user_manajemen.akses', only: ['index', 'store', 'update', 'destroy']),
        ];
    }
    public function index(ViewKelompokUSerRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'search',
                'edit',
                'delete',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'name';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

            $roles = Role::where('name', 'like', '%' . $filters['search'] . '%')
                ->where('id', '!=', 1)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->paginate(20)
                ->withQueryString();

            $this->logActivity(
                'Melihat Daftar Kelompok User dengan Sort By: ' . ($filters['sort_by'] ?? '-')
                    . ' | Arah: ' . ($filters['direction'] ?? '-')
                    . ' | Pencarian: ' . ($filters['search'] ?? '-')
                    . (!empty($filters['edit']) ? ' | Edit ID Kelompok: ' . $filters['edit'] : '')
                    . (!empty($filters['delete']) ? ' | Delete ID Kelompok: ' . $filters['delete'] : '')
            );

            return view('pages/pengaturan/kelompokuser', [
                'title' => 'Kelompok User',
                'roles' => $roles,
                'editRole' => !empty($filters['edit']) ? Role::find($filters['edit']) : null,
                'deleteRole' => !empty($filters['delete']) ? Role::select('id', 'name')->find($filters['delete']) : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Kelompok User pada halaman Kelompok User. ', 'home_page');
        }
    }
    public function store(StoreKelompokUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            $role = Role::create(['name' => $filteredData['nama']]);

            DB::commit();

            $this->logActivity('Menambahkan Kelompok User dengan ID: ' . $role->id . ' | Nama Kelompok: ' . $role->name);

            return redirect()->route('kelompokuser.index', $this->buildQueryParams($request, "KelompokUserController"))->with('success', 'Data Kelompok User berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Kelompok User. ', redirect: 'kelompokuser.index');
        }
    }
    public function update(UpdateKelompokUserRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $role = Role::where('id', '!=', 1)
                ->where('id', $id)->lockForUpdate()->firstOrFail();
            $filteredData = $request->validated();

            $role->update(['name' => $filteredData['nama']]);

            DB::commit();

            $this->logActivity('Memperbarui Kelompok User dengan ID: ' . $role->id . ' | Nama Kelompok: ' . $role->name);
            return redirect()->route('kelompokuser.index', $this->buildQueryParams($request, "KelompokUserController"))->with('success', 'Data Kelompok User berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Kelompok User. ', redirect: 'kelompokuser.index');
        }
    }
    public function destroy(DestroyKelompokUserRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $role = Role::where('id', '!=', 1)
                ->where('id', $id)->firstOrFail();

            if ($role->users()->exists()) {
                DB::rollBack();
                $this->logActivity('Gagal Menghapus Kelompok User dengan ID: ' . $role->id . ' | Nama Kelompok: ' . $role->name . ' karena masih terdapat User yang terhubung dengan Kelompok ini.');
                return redirect()->route("kelompokuser.index")->withErrors("Data Kelompok User tidak dapat dihapus dikarenakan terdapat User yang masih terhubung dengan Kelompok ini. ");
            } else {
                $role->delete();

                DB::commit();
                $this->logActivity('Menghapus Kelompok User dengan ID: ' . $role->id . ' | Nama Kelompok: ' . $role->name);
                return redirect()->route('kelompokuser.index', $this->buildQueryParams($request, "KelompokUserController"))->with('success', 'Data Kelompok User berhasil dihapus.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Kelompok User. ', redirect: 'kelompokuser.index');
        }
    }
}

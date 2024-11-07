<?php

namespace App\Http\Controllers\Pengaturan;

use App\Models\Shared\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Pengaturan\DaftarUser\ViewUserRequest;
use App\Http\Requests\Pengaturan\DaftarUser\StoreUserRequest;

class UserController extends Controller
{
    public function index(ViewUserRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'search',
                'edit',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'status';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

            $users = User::with('roles')
                ->where('id', 'like', '%' . $request->input('search', '') . '%')
                ->get();

            // Lakukan sorting pada koleksi setelah pengambilan data
            $sortedUsers = $users->sortBy(function ($user) use ($filters) {
                if ($filters['sort_by'] === 'role') {
                    // Sorting berdasarkan nama role
                    return optional($user->roles->first())->name;
                }
                // Sorting berdasarkan field lainnya
                return $user->{$filters['sort_by'] ?? 'id'};
            }, SORT_REGULAR, $filters['direction'] === 'desc');

            // Konfigurasi pagination
            $perPage = 20;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $paginatedUsers = new LengthAwarePaginator(
                $sortedUsers->forPage($currentPage, $perPage), // Ambil data sesuai halaman
                $sortedUsers->count(), // Total data yang disortir
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()] // Konfigurasi URL
            );

            // Transformasi data sesuai kebutuhan
            $paginatedUsers->getCollection()->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'role' => optional($user->roles->first())->name,
                    'status' => $user->status
                ];
            });

            return view('pages/pengaturan/daftaruser', [
                'title' => 'Daftar User',
                'users' => $paginatedUsers,
                'roles' => Role::where('id', '!=', 1)->get()->pluck('name', 'id'),
                // 'editRole' => !empty($filters['edit']) ? Role::find($filters['edit']) : null,
                // 'deleteRole' => !empty($filters['delete']) ? Role::select('id', 'name')->find($filters['delete']) : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data User pada halaman Daftar User. ', 'home_page');
        }
    }
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            $role = Role::findOrFail($filteredData['role_id']);
            $user = User::create($filteredData);
            $user->assignRole($role);

            DB::commit();
            return redirect()->route('daftaruser.index', $this->buildQueryParams($request, "UserController"))->with('success', 'Data User berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data User. ', redirect: 'daftaruser.index');
        }
    }
    public function update(Request $request, string $id)
    {
        //
    }
}

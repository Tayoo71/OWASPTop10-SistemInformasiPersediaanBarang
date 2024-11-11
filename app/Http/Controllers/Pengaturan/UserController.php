<?php

namespace App\Http\Controllers\Pengaturan;

use App\Models\Shared\User;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\Pengaturan\DaftarUser\ViewUserRequest;
use App\Http\Requests\Pengaturan\DaftarUser\StoreUserRequest;
use App\Http\Requests\Pengaturan\DaftarUser\UpdateUserRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:user_manajemen.akses', only: ['index', 'store', 'update']),
        ];
    }
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
                ->where('id', '!=', "admin")
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

            if (!empty($filters['edit'])) {
                $editUser = User::where('id', '!=', 'admin')
                    ->findOrFail($filters['edit']);

                $editUser = [
                    'id' => $editUser->id,
                    'status' => $editUser->status,
                    'role_id' => $editUser->roles->first()->id ?? null,
                    'is_2fa_active' => !empty($editUser->two_factor_confirmed_at) ? true : false
                ];
            }

            $this->logActivity(
                'Melihat Daftar User dengan Sort By: ' . ($filters['sort_by'] ?? '-')
                    . ' | Arah: ' . ($filters['direction'] ?? '-')
                    . ' | Pencarian: ' . ($filters['search'] ?? '-')
                    . (!empty($filters['edit']) ? ' | Edit User ID: ' . $filters['edit'] : '')
            );

            return view('pages/pengaturan/daftaruser', [
                'title' => 'Daftar User',
                'users' => $paginatedUsers,
                'roles' => Role::where('id', '!=', 1)->get()->pluck('name', 'id'),
                'editUser' => $editUser ?? null
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data User pada halaman Daftar User. ', 'home_page');
        }
    }
    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            app(CreateNewUser::class)->create($data);
            $this->logActivity(
                'Menambahkan User baru dengan Username: ' . $data['id']
            );

            return redirect()->route('daftaruser.index', $this->buildQueryParams($request, "UserController"))->with('success', 'Data User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data User. ', redirect: 'daftaruser.index');
        }
    }
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $data = $request->validated();
            app(UpdateUserProfileInformation::class)->update($id, $data);
            $this->logActivity(
                'Memperbarui Data User dengan Username Sebelumnya: ' . $id . ' | Username Baru: ' . $data['ubah_id']
            );

            return redirect()->route('daftaruser.index', $this->buildQueryParams($request, "UserController"))->with('success', 'Data User berhasil diubah.');
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data User. ', redirect: 'daftaruser.index');
        }
    }
}

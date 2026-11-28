<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan direktori seluruh akun pengguna sistem
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $roleFilter = $request->get('role', 'all');
        $statusFilter = $request->get('status', 'all');

        $query = User::query()->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip_nuptk', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'admin'    => User::where('role', 'admin')->count(),
            'operator' => User::where('role', 'operator')->count(),
            'guru'     => User::where('role', 'guru/walikelas')->count(),
            'ortu'     => User::where('role', 'orangtua')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'search', 'roleFilter', 'statusFilter'));
    }

    /**
     * Form tambah akun user baru
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan akun user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'username'  => 'nullable|string|max:50|unique:users,username',
            'email'     => 'nullable|email|max:150|unique:users,email',
            'role'      => 'required|in:admin,operator,guru/walikelas,orangtua',
            'nip_nuptk' => 'nullable|string|max:30|unique:users,nip_nuptk',
            'nisn'      => 'nullable|string|max:20|unique:users,nisn',
            'phone'     => 'nullable|string|max:30',
            'password'  => 'nullable|string|min:6',
            'status'    => 'required|in:active,inactive',
        ], [
            'name.required'     => 'Nama pengguna wajib diisi.',
            'username.unique'   => 'Username ini sudah digunakan.',
            'email.unique'      => 'Email ini sudah terdaftar.',
            'nip_nuptk.unique'  => 'NIP/NUPTK sudah digunakan akun lain.',
            'nisn.unique'       => 'NISN sudah digunakan akun lain.',
        ]);

        // Default password generator jika kosong
        $password = $validated['password'];
        if (empty($password)) {
            if ($validated['role'] === 'orangtua' && !empty($validated['nisn'])) {
                $password = $validated['nisn'];
            } elseif (!empty($validated['nip_nuptk'])) {
                $password = $validated['nip_nuptk'];
            } else {
                $password = '12345678';
            }
        }

        $user = User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'] ?: ($validated['nip_nuptk'] ?: ($validated['nisn'] ?: null)),
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'nip_nuptk' => $validated['nip_nuptk'],
            'nisn'      => $validated['nisn'],
            'phone'     => $validated['phone'],
            'status'    => $validated['status'],
            'password'  => Hash::make($password),
        ]);

        ActivityLog::record(
            'CREATE_USER',
            'Manajemen User',
            "Membuat akun pengguna baru: {$user->name} (Role: {$user->role_label})"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} ({$user->role_label}) berhasil dibuat! Password: {$password}");
    }

    /**
     * Form edit akun user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Perbarui data akun user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'username'  => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'     => ['nullable', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'role'      => 'required|in:admin,operator,guru/walikelas,orangtua',
            'nip_nuptk' => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
            'nisn'      => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'phone'     => 'nullable|string|max:30',
            'password'  => 'nullable|string|min:6',
            'status'    => 'required|in:active,inactive',
        ]);

        // Proteksi self-locking admin
        if (auth()->id() === $user->id) {
            if ($validated['role'] !== 'admin') {
                return back()->with('error', 'Anda tidak dapat mengubah role akun admin Anda sendiri.');
            }
            if ($validated['status'] !== 'active') {
                return back()->with('error', 'Anda tidak dapat menonaktifkan akun admin Anda sendiri.');
            }
        }

        $user->name      = $validated['name'];
        $user->username  = $validated['username'];
        $user->email     = $validated['email'];
        $user->role      = $validated['role'];
        $user->nip_nuptk = $validated['nip_nuptk'];
        $user->nisn      = $validated['nisn'];
        $user->phone     = $validated['phone'];
        $user->status    = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLog::record(
            'UPDATE_USER',
            'Manajemen User',
            "Memperbarui data akun: {$user->name} (Role: {$user->role_label})"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Data akun {$user->name} berhasil diperbarui!");
    }

    /**
     * Hapus akun pengguna
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Jika wali kelas, lepaskan rombel
        if ($user->homeroomClassroom) {
            $user->homeroomClassroom->update([
                'homeroom_teacher_id' => null,
                'homeroom_teacher'    => null,
                'homeroom_nip'        => null,
            ]);
        }

        $name = $user->name;
        $role = $user->role_label;
        $user->delete();

        ActivityLog::record(
            'DELETE_USER',
            'Manajemen User',
            "Menghapus akun pengguna: {$name} ({$role})"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$name} ({$role}) berhasil dihapus!");
    }

    /**
     * Toggle status aktif / suspend akun pengguna
     */
    public function toggleStatus(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $newStatus = ($user->status === 'active') ? 'inactive' : 'active';
        $user->status = $newStatus;
        $user->save();

        $statusText = ($newStatus === 'active') ? 'Diaktifkan Kembali' : 'Ditangguhkan (Suspend)';

        ActivityLog::record(
            'UPDATE_USER',
            'Manajemen User',
            "Mengubah status akun {$user->name} menjadi {$newStatus}"
        );

        return back()->with('success', "Status akun {$user->name} berhasil diubah menjadi: {$statusText}!");
    }

    /**
     * Reset password akun ke NIP atau NISN
     */
    public function resetPassword(User $user)
    {
        $defaultPassword = $user->nip_nuptk ?: ($user->nisn ?: '12345678');
        $user->password = Hash::make($defaultPassword);
        $user->save();

        ActivityLog::record(
            'RESET_PASSWORD',
            'Manajemen User',
            "Mereset password akun {$user->name} ({$user->role_label}) ke default: '{$defaultPassword}'"
        );

        return back()->with('success', "Password akun {$user->name} berhasil direset ke: '{$defaultPassword}'.");
    }
}

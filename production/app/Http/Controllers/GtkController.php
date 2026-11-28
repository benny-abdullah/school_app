<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GtkController extends Controller
{
    /**
     * Tampilkan direktori Master Guru & Tenaga Kependidikan (GTK)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statusFilter = $request->get('status', 'all');
        $employmentFilter = $request->get('employment', 'all');
        $roleFilter = $request->get('role', 'all');

        $query = User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])
            ->with('homeroomClassroom')
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip_nuptk', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($employmentFilter !== 'all') {
            $query->where('employment_status', $employmentFilter);
        }

        if ($roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        $gtks = $query->paginate(15)->withQueryString();

        $stats = [
            'total_gtk'        => User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])->count(),
            'total_guru_kelas' => Classroom::whereNotNull('homeroom_teacher_id')->count(),
            'total_pns'        => User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])->where('employment_status', 'PNS')->count(),
            'total_pppk'       => User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])->where('employment_status', 'PPPK')->count(),
            'total_honorer'    => User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])->whereIn('employment_status', ['GTT', 'PTT', 'Honorer'])->count(),
        ];

        return view('operator.gtk.index', compact('gtks', 'stats', 'search', 'statusFilter', 'employmentFilter', 'roleFilter'));
    }

    /**
     * Form tambah GTK baru
     */
    public function create()
    {
        return view('operator.gtk.create');
    }

    /**
     * Simpan GTK baru ke database dan generate akun user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:150',
            'nip_nuptk'         => 'nullable|string|max:30|unique:users,nip_nuptk',
            'nuptk'             => 'nullable|string|max:30|unique:users,nuptk',
            'gender'            => 'required|in:L,P',
            'role'              => 'required|in:guru/walikelas,operator,admin',
            'employment_status' => 'required|string|max:30',
            'position'          => 'required|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:150|unique:users,email',
            'avatar'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required'              => 'Nama lengkap dan gelar GTK wajib diisi.',
            'nip_nuptk.unique'           => 'NIP ini sudah terdaftar untuk pengguna lain.',
            'nuptk.unique'               => 'NUPTK ini sudah terdaftar untuk pengguna lain.',
            'gender.required'            => 'Jenis kelamin wajib dipilih.',
            'role.required'              => 'Hak akses role sistem wajib dipilih.',
            'employment_status.required' => 'Status kepegawaian wajib dipilih.',
            'position.required'          => 'Jabatan utama wajib diisi.',
            'email.unique'               => 'Email ini sudah digunakan oleh akun lain.',
            'avatar.max'                 => 'Ukuran berkas foto maksimal 2MB.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('gtk', 'public');
        }

        // Identifier utama untuk username & default password
        $identifier = $validated['nip_nuptk'] ?: ($validated['nuptk'] ?: preg_replace('/[^A-Za-z0-9]/', '', strtolower($validated['name'])));
        $username = 'gtk_' . preg_replace('/[^A-Za-z0-9_]/', '', strtolower(substr($validated['name'], 0, 10))) . '_' . rand(100, 999);
        $password = $validated['nip_nuptk'] ?: ($validated['nuptk'] ?: '12345678');

        $user = User::create([
            'name'              => $validated['name'],
            'username'          => $username,
            'nip_nuptk'         => $validated['nip_nuptk'],
            'nuptk'             => $validated['nuptk'],
            'gender'            => $validated['gender'],
            'role'              => $validated['role'],
            'employment_status' => $validated['employment_status'],
            'position'          => $validated['position'],
            'phone'             => $validated['phone'],
            'email'             => $validated['email'],
            'avatar'            => $avatarPath,
            'status'            => 'active',
            'password'          => Hash::make($password),
            'additional_info'   => [
                'jabatan'  => $validated['position'],
                'instansi' => 'SDN Kebonagung Kota Pasuruan',
            ],
        ]);

        ActivityLog::record(
            'CREATE_GTK',
            'Master GTK',
            "Menambahkan GTK baru: {$user->name} ({$user->position}, Status: {$user->employment_status})"
        );

        return redirect()->route('gtk.index')
            ->with('success', "Data GTK {$user->name} berhasil ditambahkan! Akun login dibuat dengan username/NIP: {$password}.");
    }

    /**
     * Form edit GTK
     */
    public function edit(User $gtk)
    {
        $gtk->load('homeroomClassroom');
        return view('operator.gtk.edit', compact('gtk'));
    }

    /**
     * Perbarui data GTK
     */
    public function update(Request $request, User $gtk)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:150',
            'nip_nuptk'         => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($gtk->id)],
            'nuptk'             => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($gtk->id)],
            'gender'            => 'required|in:L,P',
            'role'              => 'required|in:guru/walikelas,operator,admin',
            'employment_status' => 'required|string|max:30',
            'position'          => 'required|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'email'             => ['nullable', 'email', 'max:150', Rule::unique('users')->ignore($gtk->id)],
            'status'            => 'required|in:active,inactive',
            'avatar'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($gtk->avatar && Storage::disk('public')->exists($gtk->avatar)) {
                Storage::disk('public')->delete($gtk->avatar);
            }
            $gtk->avatar = $request->file('avatar')->store('gtk', 'public');
        }

        $info = $gtk->additional_info ?? [];
        $info['jabatan'] = $validated['position'];

        $gtk->name              = $validated['name'];
        $gtk->nip_nuptk         = $validated['nip_nuptk'];
        $gtk->nuptk             = $validated['nuptk'];
        $gtk->gender            = $validated['gender'];
        $gtk->role              = $validated['role'];
        $gtk->employment_status = $validated['employment_status'];
        $gtk->position          = $validated['position'];
        $gtk->phone             = $validated['phone'];
        $gtk->email             = $validated['email'];
        $gtk->status            = $validated['status'];
        $gtk->additional_info   = $info;
        $gtk->save();

        ActivityLog::record(
            'UPDATE_GTK',
            'Master GTK',
            "Memperbarui data GTK: {$gtk->name} ({$gtk->position})"
        );

        return redirect()->route('gtk.index')
            ->with('success', "Data GTK {$gtk->name} berhasil diperbarui!");
    }

    /**
     * Hapus GTK atau nonaktifkan
     */
    public function destroy(User $gtk)
    {
        // Proteksi: jangan hapus jika sedang login
        if (auth()->id() === $gtk->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        // Lepaskan penugasan rombel jika user adalah wali kelas
        if ($gtk->homeroomClassroom) {
            $class = $gtk->homeroomClassroom;
            $class->update([
                'homeroom_teacher_id' => null,
                'homeroom_teacher'    => null,
                'homeroom_nip'        => null,
            ]);
        }

        $name = $gtk->name;

        // Hapus foto jika ada
        if ($gtk->avatar && Storage::disk('public')->exists($gtk->avatar)) {
            Storage::disk('public')->delete($gtk->avatar);
        }

        $gtk->delete();

        ActivityLog::record(
            'DELETE_GTK',
            'Master GTK',
            "Menghapus data GTK: {$name}"
        );

        return redirect()->route('gtk.index')
            ->with('success', "Data GTK {$name} berhasil dihapus dari sistem!");
    }

    /**
     * Reset Password GTK ke default NIP/NUPTK
     */
    public function resetPassword(User $gtk)
    {
        $defaultPassword = $gtk->nip_nuptk ?: ($gtk->nuptk ?: '12345678');
        $gtk->password = Hash::make($defaultPassword);
        $gtk->save();

        ActivityLog::record(
            'RESET_PASSWORD',
            'Master GTK',
            "Mereset password GTK: {$gtk->name} ke default ({$defaultPassword})"
        );

        return back()->with('success', "Password akun {$gtk->name} berhasil direset ke default: '{$defaultPassword}'.");
    }
}

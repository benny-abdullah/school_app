<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Drs. H. Mulyadi M.Pd',
                'username' => 'admin_mulyadi',
                'email' => 'kepsek@sdnkebonagung.sch.id',
                'nip_nuptk' => '197103151998021004',
                'nisn' => null,
                'role' => 'admin',
                'phone' => '081234567001',
                'status' => 'active',
                'password' => Hash::make('197103151998021004'),
                'additional_info' => [
                    'jabatan' => 'Kepala Sekolah',
                    'instansi' => 'SDN Kebonagung Kota Pasuruan',
                    'periode' => '2024 - 2028',
                ],
            ],
            [
                'name' => 'Siti Aminah S.Kom',
                'username' => 'operator_siti',
                'email' => 'operator@sdnkebonagung.sch.id',
                'nip_nuptk' => '198504202008011005',
                'nisn' => null,
                'role' => 'operator',
                'phone' => '081234567002',
                'status' => 'active',
                'password' => Hash::make('198504202008011005'),
                'additional_info' => [
                    'jabatan' => 'Operator Dapodik & Tata Usaha',
                    'instansi' => 'SDN Kebonagung Kota Pasuruan',
                    'tugas' => 'Admin Sistem, Kartu Pelajar, Absensi Gerbang',
                ],
            ],
            [
                'name' => 'Bambang S.Pd',
                'username' => 'guru_bambang',
                'email' => 'bambang@sdnkebonagung.sch.id',
                'nip_nuptk' => '198007152006041012',
                'nisn' => null,
                'role' => 'guru/walikelas',
                'phone' => '081234567003',
                'status' => 'active',
                'password' => Hash::make('198007152006041012'),
                'additional_info' => [
                    'jabatan' => 'Wali Kelas 5A',
                    'classroom' => '5A',
                    'instansi' => 'SDN Kebonagung Kota Pasuruan',
                    'mapel' => 'Guru Kelas',
                ],
            ],
            [
                'name' => 'Bpk. Hendra Gunawan',
                'username' => 'ortu_hendra',
                'email' => 'hendra.gunawan@gmail.com',
                'nip_nuptk' => null,
                'nisn' => '0081234567',
                'role' => 'orangtua',
                'phone' => '081234567004',
                'status' => 'active',
                'password' => Hash::make('0081234567'),
                'additional_info' => [
                    'student_name' => 'Ahmad Rifai',
                    'student_nisn' => '0081234567',
                    'classroom' => '5A',
                    'student_gender' => 'L',
                    'relation' => 'Ayah Kandung',
                    'address' => 'Jl. Kebonagung No. 45 Kota Pasuruan',
                ],
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }
    }
}

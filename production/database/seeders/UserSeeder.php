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
        $coreUsers = [
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

        foreach ($coreUsers as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }

        // Daftar 29 Guru Wali Kelas Resmi SDN Kebonagung
        $teachers = [
            '1A' => ['name' => 'Sri Wahyuni S.Pd.SD', 'nip' => '198205122008012015', 'phone' => '081234561001'],
            '1B' => ['name' => 'Nurul Hidayati S.Pd', 'nip' => '198403192009022008', 'phone' => '081234561002'],
            '1C' => ['name' => 'Dwi Lestari S.Pd', 'nip' => '198901042011012019', 'phone' => '081234561003'],
            '1D' => ['name' => 'Rina Kurniawati S.Pd', 'nip' => '199107232015032004', 'phone' => '081234561004'],
            '2A' => ['name' => 'Endang Sulistyowati S.Pd', 'nip' => '197908142006042011', 'phone' => '081234561005'],
            '2B' => ['name' => 'Ahmad Fauzi S.Pd', 'nip' => '198302112008011009', 'phone' => '081234561006'],
            '2C' => ['name' => 'Siti Khotimah S.Pd', 'nip' => '198511202010012022', 'phone' => '081234561007'],
            '2D' => ['name' => 'Wahyu Hidayat S.Pd', 'nip' => '198709152014021003', 'phone' => '081234561008'],
            '2E' => ['name' => 'Anisa Rahmawati S.Pd', 'nip' => '199204052019032014', 'phone' => '081234561009'],
            '3A' => ['name' => 'Agus Priyanto S.Pd', 'nip' => '197806122005011007', 'phone' => '081234561010'],
            '3B' => ['name' => 'Tri Handayani S.Pd', 'nip' => '198104192007012016', 'phone' => '081234561011'],
            '3C' => ['name' => 'Muhammad Ridwan S.Pd', 'nip' => '198603252011011008', 'phone' => '081234561012'],
            '3D' => ['name' => 'Retno Wulandari S.Pd', 'nip' => '198812102014022011', 'phone' => '081234561013'],
            '3E' => ['name' => 'Farida Ulfa S.Pd', 'nip' => '199009182015032009', 'phone' => '081234561014'],
            '4A' => ['name' => 'Joko Susilo S.Pd', 'nip' => '197605102003121004', 'phone' => '081234561015'],
            '4B' => ['name' => 'Dewi Sartika S.Pd', 'nip' => '198207182008012018', 'phone' => '081234561016'],
            '4C' => ['name' => 'Budi Santoso S.Pd', 'nip' => '198410052009031006', 'phone' => '081234561017'],
            '4D' => ['name' => 'Yuni Astuti S.Pd', 'nip' => '198703142012022007', 'phone' => '081234561018'],
            '4E' => ['name' => 'Indra Gunawan S.Pd', 'nip' => '199102282017041002', 'phone' => '081234561019'],
            '5A' => ['name' => 'Bambang S.Pd', 'nip' => '198007152006041012', 'phone' => '081234567003'], // Sudah di atas
            '5B' => ['name' => 'Kusuma Wardani S.Pd', 'nip' => '198309192008012014', 'phone' => '081234561021'],
            '5C' => ['name' => 'Hadi Sucipto S.Pd', 'nip' => '198506112010011011', 'phone' => '081234561022'],
            '5D' => ['name' => 'Lilik Suryani S.Pd', 'nip' => '198808202014022013', 'phone' => '081234561023'],
            '5E' => ['name' => 'Fajar Pratama S.Pd', 'nip' => '199211152019031007', 'phone' => '081234561024'],
            '6A' => ['name' => 'Drs. Supardi M.Pd', 'nip' => '197204121997031005', 'phone' => '081234561025'],
            '6B' => ['name' => 'Herni Sukesi S.Pd', 'nip' => '197509172002122003', 'phone' => '081234561026'],
            '6C' => ['name' => 'Sugeng Riyadi S.Pd', 'nip' => '198101222006041008', 'phone' => '081234561027'],
            '6D' => ['name' => 'Titik Rahayu S.Pd', 'nip' => '198405082009022010', 'phone' => '081234561028'],
            '6E' => ['name' => 'Arif Rahman Hakim S.Pd', 'nip' => '198906302014031005', 'phone' => '081234561029'],
        ];

        foreach ($teachers as $code => $t) {
            $username = 'guru_' . strtolower($code);
            User::updateOrCreate(
                ['nip_nuptk' => $t['nip']],
                [
                    'name' => $t['name'],
                    'username' => $username,
                    'email' => strtolower($code) . '@sdnkebonagung.sch.id',
                    'nip_nuptk' => $t['nip'],
                    'role' => 'guru/walikelas',
                    'phone' => $t['phone'],
                    'status' => 'active',
                    'password' => Hash::make($t['nip']), // Password sama dengan NIP
                    'additional_info' => [
                        'jabatan' => 'Wali Kelas ' . $code,
                        'classroom' => $code,
                        'instansi' => 'SDN Kebonagung Kota Pasuruan',
                        'mapel' => 'Guru Kelas',
                    ],
                ]
            );
        }
    }
}

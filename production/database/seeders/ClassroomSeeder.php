<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classDefinitions = [
            1 => ['A', 'B', 'C', 'D'],
            2 => ['A', 'B', 'C', 'D', 'E'],
            3 => ['A', 'B', 'C', 'D', 'E'],
            4 => ['A', 'B', 'C', 'D', 'E'],
            5 => ['A', 'B', 'C', 'D', 'E'],
            6 => ['A', 'B', 'C', 'D', 'E'],
        ];

        // Sample realistic homeroom teachers for SDN Kebonagung Kota Pasuruan
        $teachers = [
            '1A' => ['name' => 'Sri Wahyuni S.Pd.SD', 'nip' => '198205122008012015', 'room' => 'Gedung A (Utara) Lt. 1 - R. 101'],
            '1B' => ['name' => 'Nurul Hidayati S.Pd', 'nip' => '198403192009022008', 'room' => 'Gedung A (Utara) Lt. 1 - R. 102'],
            '1C' => ['name' => 'Dwi Lestari S.Pd', 'nip' => '198901042011012019', 'room' => 'Gedung A (Utara) Lt. 1 - R. 103'],
            '1D' => ['name' => 'Rina Kurniawati S.Pd', 'nip' => '199107232015032004', 'room' => 'Gedung A (Utara) Lt. 1 - R. 104'],
            '2A' => ['name' => 'Endang Sulistyowati S.Pd', 'nip' => '197908142006042011', 'room' => 'Gedung A (Utara) Lt. 2 - R. 201'],
            '2B' => ['name' => 'Ahmad Fauzi S.Pd', 'nip' => '198302112008011009', 'room' => 'Gedung A (Utara) Lt. 2 - R. 202'],
            '2C' => ['name' => 'Siti Khotimah S.Pd', 'nip' => '198511202010012022', 'room' => 'Gedung A (Utara) Lt. 2 - R. 203'],
            '2D' => ['name' => 'Wahyu Hidayat S.Pd', 'nip' => '198709152014021003', 'room' => 'Gedung A (Utara) Lt. 2 - R. 204'],
            '2E' => ['name' => 'Anisa Rahmawati S.Pd', 'nip' => '199204052019032014', 'room' => 'Gedung A (Utara) Lt. 2 - R. 205'],
            '3A' => ['name' => 'Agus Priyanto S.Pd', 'nip' => '197806122005011007', 'room' => 'Gedung B (Barat) Lt. 1 - R. 101'],
            '3B' => ['name' => 'Tri Handayani S.Pd', 'nip' => '198104192007012016', 'room' => 'Gedung B (Barat) Lt. 1 - R. 102'],
            '3C' => ['name' => 'Muhammad Ridwan S.Pd', 'nip' => '198603252011011008', 'room' => 'Gedung B (Barat) Lt. 1 - R. 103'],
            '3D' => ['name' => 'Retno Wulandari S.Pd', 'nip' => '198812102014022011', 'room' => 'Gedung B (Barat) Lt. 1 - R. 104'],
            '3E' => ['name' => 'Farida Ulfa S.Pd', 'nip' => '199009182015032009', 'room' => 'Gedung B (Barat) Lt. 1 - R. 105'],
            '4A' => ['name' => 'Joko Susilo S.Pd', 'nip' => '197605102003121004', 'room' => 'Gedung B (Barat) Lt. 2 - R. 201'],
            '4B' => ['name' => 'Dewi Sartika S.Pd', 'nip' => '198207182008012018', 'room' => 'Gedung B (Barat) Lt. 2 - R. 202'],
            '4C' => ['name' => 'Budi Santoso S.Pd', 'nip' => '198410052009031006', 'room' => 'Gedung B (Barat) Lt. 2 - R. 203'],
            '4D' => ['name' => 'Yuni Astuti S.Pd', 'nip' => '198703142012022007', 'room' => 'Gedung B (Barat) Lt. 2 - R. 204'],
            '4E' => ['name' => 'Indra Gunawan S.Pd', 'nip' => '199102282017041002', 'room' => 'Gedung B (Barat) Lt. 2 - R. 205'],
            '5A' => ['name' => 'Bambang S.Pd', 'nip' => '198007152006041012', 'room' => 'Gedung C (Timur) Lt. 1 - R. 101'], // Wali Kelas Demo Guru
            '5B' => ['name' => 'Kusuma Wardani S.Pd', 'nip' => '198309192008012014', 'room' => 'Gedung C (Timur) Lt. 1 - R. 102'],
            '5C' => ['name' => 'Hadi Sucipto S.Pd', 'nip' => '198506112010011011', 'room' => 'Gedung C (Timur) Lt. 1 - R. 103'],
            '5D' => ['name' => 'Lilik Suryani S.Pd', 'nip' => '198808202014022013', 'room' => 'Gedung C (Timur) Lt. 1 - R. 104'],
            '5E' => ['name' => 'Fajar Pratama S.Pd', 'nip' => '199211152019031007', 'room' => 'Gedung C (Timur) Lt. 1 - R. 105'],
            '6A' => ['name' => 'Drs. Supardi M.Pd', 'nip' => '197204121997031005', 'room' => 'Gedung C (Timur) Lt. 2 - R. 201'],
            '6B' => ['name' => 'Herni Sukesi S.Pd', 'nip' => '197509172002122003', 'room' => 'Gedung C (Timur) Lt. 2 - R. 202'],
            '6C' => ['name' => 'Sugeng Riyadi S.Pd', 'nip' => '198101222006041008', 'room' => 'Gedung C (Timur) Lt. 2 - R. 203'],
            '6D' => ['name' => 'Titik Rahayu S.Pd', 'nip' => '198405082009022010', 'room' => 'Gedung C (Timur) Lt. 2 - R. 204'],
            '6E' => ['name' => 'Arif Rahman Hakim S.Pd', 'nip' => '198906302014031005', 'room' => 'Gedung C (Timur) Lt. 2 - R. 205'],
        ];

        foreach ($classDefinitions as $grade => $sections) {
            foreach ($sections as $sec) {
                $code = "{$grade}{$sec}";
                $teacherInfo = $teachers[$code] ?? ['name' => 'Guru Kelas ' . $code, 'nip' => null, 'room' => 'Ruang ' . $code];

                // Temukan User Guru ber-NIP terkait
                $teacherUser = null;
                if (!empty($teacherInfo['nip'])) {
                    $teacherUser = User::where('nip_nuptk', $teacherInfo['nip'])->first();
                }

                Classroom::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => "Kelas {$grade}{$sec}",
                        'grade_level' => $grade,
                        'section' => $sec,
                        'homeroom_teacher_id' => $teacherUser?->id,
                        'homeroom_teacher' => $teacherInfo['name'],
                        'homeroom_nip' => $teacherInfo['nip'],
                        'room_name' => $teacherInfo['room'],
                        'capacity' => 32,
                        'total_students' => rand(28, 32),
                        'academic_year' => '2025/2026',
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class5A = Classroom::where('code', '5A')->first();
        if (!$class5A) {
            return;
        }

        // Siswa Utama Demo (Anak dari Bpk. Hendra Gunawan - Ortu Demo)
        Student::updateOrCreate(
            ['nisn' => '0081234567'],
            [
                'nis' => '202105001',
                'name' => 'Ahmad Rifai',
                'gender' => 'L',
                'classroom_id' => $class5A->id,
                'birth_place' => 'Pasuruan',
                'birth_date' => Carbon::create(2014, 5, 12),
                'address' => 'Jl. Kebonagung No. 45 Kota Pasuruan',
                'guardian_name' => 'Bpk. Hendra Gunawan',
                'guardian_relation' => 'Ayah Kandung',
                'guardian_phone' => '081234567004',
                'status' => 'active',
                'point_balance' => 100,
            ]
        );

        // Siswa Rombel 5A Lainnya
        $students5A = [
            ['nisn' => '0081234568', 'name' => 'Aisyah Putri Maharani', 'gender' => 'P', 'guardian' => 'Bpk. Ridwan', 'phone' => '081234567011'],
            ['nisn' => '0081234569', 'name' => 'Bima Sakti Pratama', 'gender' => 'L', 'guardian' => 'Ibu Ratna', 'phone' => '081234567012'],
            ['nisn' => '0081234570', 'name' => 'Citra Dewi Lestari', 'gender' => 'P', 'guardian' => 'Bpk. Santoso', 'phone' => '081234567013'],
            ['nisn' => '0081234571', 'name' => 'Dewa Raditya Ananta', 'gender' => 'L', 'guardian' => 'Bpk. Agus', 'phone' => '081234567014'],
            ['nisn' => '0081234572', 'name' => 'Eka Nur Fadilah', 'gender' => 'P', 'guardian' => 'Ibu Wati', 'phone' => '081234567015'],
            ['nisn' => '0081234573', 'name' => 'Fathan Al Farisi', 'gender' => 'L', 'guardian' => 'Bpk. Mansyur', 'phone' => '081234567016'],
            ['nisn' => '0081234574', 'name' => 'Gita Kirana Salsabila', 'gender' => 'P', 'guardian' => 'Ibu Maya', 'phone' => '081234567017'],
            ['nisn' => '0081234575', 'name' => 'Haikal Zaidan', 'gender' => 'L', 'guardian' => 'Bpk. Lukman', 'phone' => '081234567018'],
            ['nisn' => '0081234576', 'name' => 'Indah Kusuma Wardani', 'gender' => 'P', 'guardian' => 'Bpk. Joko', 'phone' => '081234567019'],
        ];

        foreach ($students5A as $idx => $s) {
            Student::updateOrCreate(
                ['nisn' => $s['nisn']],
                [
                    'nis' => '2021050' . sprintf('%02d', $idx + 2),
                    'name' => $s['name'],
                    'gender' => $s['gender'],
                    'classroom_id' => $class5A->id,
                    'birth_place' => 'Pasuruan',
                    'birth_date' => Carbon::create(2014, rand(1, 12), rand(1, 28)),
                    'address' => 'Kota Pasuruan, Jawa Timur',
                    'guardian_name' => $s['guardian'],
                    'guardian_relation' => 'Orang Tua',
                    'guardian_phone' => $s['phone'],
                    'status' => 'active',
                    'point_balance' => 100,
                ]
            );
        }

        // Seeding sampel perwakilan untuk rombel lainnya (1A, 2A, 3A, 4A, 6A)
        $sampleClasses = Classroom::whereIn('code', ['1A', '2A', '3A', '4A', '6A'])->get();
        $baseNisn = 81234600;

        foreach ($sampleClasses as $cls) {
            for ($i = 1; $i <= 5; $i++) {
                $currentNisn = '00' . ($baseNisn++);
                $isMale = ($i % 2 === 1);
                $names = $isMale 
                    ? ['Muhammad Alif', 'Rizky Ramadhan', 'Dimas Prasetyo', 'Zaki Pratama', 'Kenzo Danendra']
                    : ['Nabila Azzahra', 'Salwa Nur Azizah', 'Kayla Putri', 'Zahra Amelia', 'Nadine Anindita'];

                Student::updateOrCreate(
                    ['nisn' => $currentNisn],
                    [
                        'nis' => (2026 - $cls->grade_level) . sprintf('%02d', $cls->grade_level) . sprintf('%03d', $i),
                        'name' => $names[$i - 1] . ' ' . $cls->code,
                        'gender' => $isMale ? 'L' : 'P',
                        'classroom_id' => $cls->id,
                        'birth_place' => 'Pasuruan',
                        'birth_date' => Carbon::create(2026 - 6 - $cls->grade_level, rand(1, 12), rand(1, 28)),
                        'address' => 'Kelurahan Kebonagung Kota Pasuruan',
                        'guardian_name' => 'Wali Murid ' . $cls->code,
                        'guardian_relation' => 'Orang Tua',
                        'guardian_phone' => '0812' . rand(10000000, 99999999),
                        'status' => 'active',
                        'point_balance' => 100,
                    ]
                );
            }
        }

        // Update classroom total_students dynamically based on seeded students
        foreach (Classroom::all() as $c) {
            $count = Student::where('classroom_id', $c->id)->where('status', 'active')->count();
            // If count is 0, give standard rombel count (around 28-32)
            $c->update(['total_students' => $count > 0 ? $count : rand(28, 31)]);
        }
    }
}

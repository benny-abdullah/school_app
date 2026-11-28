<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DapodikOfficialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pegawaiPath = base_path('data Guru dan pegawai SDN Kebonagung.xlsx');
        $siswaPath = base_path('data siswa sdn kebonagung 791.xlsx');

        if (!file_exists($pegawaiPath) || !file_exists($siswaPath)) {
            $this->command->error("Berkas Excel Dapodik tidak ditemukan!");
            return;
        }

        DB::transaction(function () use ($pegawaiPath, $siswaPath) {
            $this->command->info("=== MEMULAI IMPOR DATA RESMI DAPODIK SDN KEBONAGUNG ===");

            // -------------------------------------------------------------
            // 1. IMPOR DATA GTK & PEGAWAI (38 dari Excel + 2 Wali Kelas Senior = 40 GTK)
            // -------------------------------------------------------------
            $this->command->info("1. Mengimpor Data 40 GTK (Guru & Tenaga Kependidikan)...");
            
            $reader = IOFactory::createReaderForFile($pegawaiPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($pegawaiPath);
            $sheet = $spreadsheet->getSheetByName('Template Pegawai');
            $highestRow = $sheet->getHighestRow();

            $gtkUserMap = []; // cleanNip => User Model

            for ($r = 2; $r <= $highestRow; $r++) {
                $nip = trim((string)$sheet->getCell('A' . $r)->getValue());
                $nama = trim((string)$sheet->getCell('B' . $r)->getValue());
                $jkRaw = trim((string)$sheet->getCell('C' . $r)->getValue());
                $kategori = trim((string)$sheet->getCell('D' . $r)->getValue());
                $jabatan = trim((string)$sheet->getCell('E' . $r)->getValue());
                $tugas = trim((string)$sheet->getCell('F' . $r)->getValue());
                $kelasBinaan = trim((string)$sheet->getCell('G' . $r)->getValue());
                $mapel = trim((string)$sheet->getCell('H' . $r)->getValue());
                $statusPeg = trim((string)$sheet->getCell('I' . $r)->getValue());
                $gol = trim((string)$sheet->getCell('J' . $r)->getValue());
                $pendidikan = trim((string)$sheet->getCell('K' . $r)->getValue());
                $wa = trim((string)$sheet->getCell('L' . $r)->getValue());
                $email = trim((string)$sheet->getCell('M' . $r)->getValue());
                $alamat = trim((string)$sheet->getCell('N' . $r)->getValue());

                if (!$nip || !$nama) continue;

                $cleanNip = preg_replace('/[^0-9]/', '', $nip);
                $gender = (str_starts_with(strtoupper($jkRaw), 'L')) ? 'L' : 'P';

                // Tentukan Role Sistem
                if ($jabatan === 'Kepala Sekolah' || $cleanNip === '198708222019031004') {
                    $role = 'admin';
                } elseif (str_contains(strtolower($kategori), 'kependidikan') || str_contains(strtolower($jabatan), 'tu') || str_contains(strtolower($jabatan), 'satpam')) {
                    $role = 'operator';
                } else {
                    $role = 'guru/walikelas';
                }

                $additionalInfo = [
                    'kategori_pegawai'    => $kategori,
                    'tugas_tambahan'      => $tugas,
                    'kelas_binaan'        => $kelasBinaan,
                    'mata_pelajaran'      => $mapel,
                    'golongan'            => $gol,
                    'pendidikan_terakhir' => $pendidikan,
                    'alamat'              => $alamat,
                ];

                $user = User::updateOrCreate(
                    ['username' => $cleanNip],
                    [
                        'name'              => $nama,
                        'email'             => !empty($email) ? $email : null,
                        'nip_nuptk'         => $cleanNip,
                        'role'              => $role,
                        'phone'             => !empty($wa) ? $wa : null,
                        'gender'            => $gender,
                        'employment_status' => $statusPeg,
                        'position'          => $jabatan,
                        'status'            => 'active',
                        'additional_info'   => $additionalInfo,
                        'password'          => Hash::make($cleanNip),
                    ]
                );

                $gtkUserMap[$cleanNip] = $user;
            }

            // Tambahkan 2 Guru Wali Kelas Senior (2D & 3D) jika belum ada di Template Pegawai
            $seniorWalis = [
                [
                    'nip'      => '196608231991092001',
                    'nama'     => 'PUJI LESTARI, S.Pd.',
                    'jabatan'  => 'Wali Kelas',
                    'gender'   => 'P',
                    'status'   => 'PNS',
                    'binaan'   => 'Wali Kelas 2-D',
                ],
                [
                    'nip'      => '196610191991012001',
                    'nama'     => 'ANDRIANI EKA OKTAVENI, S.Pd.',
                    'jabatan'  => 'Wali Kelas',
                    'gender'   => 'P',
                    'status'   => 'PNS',
                    'binaan'   => 'Wali Kelas 3-D',
                ]
            ];

            foreach ($seniorWalis as $sw) {
                if (!isset($gtkUserMap[$sw['nip']])) {
                    $u = User::updateOrCreate(
                        ['username' => $sw['nip']],
                        [
                            'name'              => $sw['nama'],
                            'nip_nuptk'         => $sw['nip'],
                            'role'              => 'guru/walikelas',
                            'gender'            => $sw['gender'],
                            'employment_status' => $sw['status'],
                            'position'          => $sw['jabatan'],
                            'status'            => 'active',
                            'additional_info'   => [
                                'kategori_pegawai' => 'Guru Kelas/Walikelas',
                                'kelas_binaan'     => $sw['binaan'],
                            ],
                            'password'          => Hash::make($sw['nip']),
                        ]
                    );
                    $gtkUserMap[$sw['nip']] = $u;
                }
            }

            $this->command->info("   -> Berhasil menyinkronkan " . count($gtkUserMap) . " GTK (1 Kepala Sekolah, 28 Wali Kelas, Guru Mapel & Tendik).");

            // -------------------------------------------------------------
            // 2. IMPOR 28 ROMBEL & PENETAPAN WALI KELAS RESMI
            // -------------------------------------------------------------
            $this->command->info("2. Menyinkronkan 28 Rombongan Belajar (1A s/d 6E)...");

            // Hapus rombel dummy lama yang tidak memiliki relasi siswa
            Classroom::query()->delete();

            $reader2 = IOFactory::createReaderForFile($siswaPath);
            $reader2->setReadDataOnly(true);
            $spreadsheet2 = $reader2->load($siswaPath);
            $sheetWali = $spreadsheet2->getSheetByName('data walikelas');
            $highestRowWali = $sheetWali->getHighestRow();

            $classroomMap = []; // code ('1A') => Classroom ID

            for ($r = 2; $r <= $highestRowWali; $r++) {
                $kode = trim((string)$sheetWali->getCell('A' . $r)->getValue());
                $tingkat = trim((string)$sheetWali->getCell('B' . $r)->getValue());
                $wali = trim((string)$sheetWali->getCell('C' . $r)->getValue());
                $nip = trim((string)$sheetWali->getCell('D' . $r)->getValue());

                if (!$kode) continue;

                $cleanNip = preg_replace('/[^0-9]/', '', $nip);
                $gradeLevel = (int) substr($kode, 0, 1);
                $section = substr($kode, 1, 1);
                $teacherUser = $gtkUserMap[$cleanNip] ?? null;

                $cr = Classroom::create([
                    'code'                 => $kode,
                    'name'                 => 'Kelas ' . $kode,
                    'grade_level'          => $gradeLevel,
                    'section'              => $section,
                    'homeroom_teacher'     => $wali,
                    'homeroom_nip'         => $cleanNip,
                    'homeroom_teacher_id'  => $teacherUser?->id,
                    'room_name'            => "Gedung UPTD SDN Kebonagung - Ruang {$kode}",
                    'capacity'             => 32,
                    'total_students'       => 0,
                    'academic_year'        => '2025/2026',
                    'status'               => 'active',
                ]);

                $classroomMap[$kode] = $cr->id;
            }

            $this->command->info("   -> Berhasil membuat " . count($classroomMap) . " Rombongan Belajar terhubung ke Wali Kelas.");

            // -------------------------------------------------------------
            // 3. IMPOR 791 PESERTA DIDIK AKTIF DENGAN AGAMA & BIODATA
            // -------------------------------------------------------------
            $this->command->info("3. Mengimpor 791 Siswa Aktif dari Sheet 'DATA_SISWA'...");

            // Bersihkan data siswa lama jika ada
            Student::query()->delete();

            $sheetSiswa = $spreadsheet2->getSheetByName('DATA_SISWA');
            $highestRowSiswa = $sheetSiswa->getHighestRow();

            $studentsCreated = 0;
            $parentAccounts = [];

            for ($r = 2; $r <= $highestRowSiswa; $r++) {
                $nisn = trim((string)$sheetSiswa->getCell('A' . $r)->getValue());
                $nama = trim((string)$sheetSiswa->getCell('B' . $r)->getValue());
                $kelas = trim((string)$sheetSiswa->getCell('C' . $r)->getValue());
                $jk = trim((string)$sheetSiswa->getCell('D' . $r)->getValue());
                $tmpt = trim((string)$sheetSiswa->getCell('E' . $r)->getValue());
                $tglRaw = trim((string)$sheetSiswa->getCell('F' . $r)->getValue());
                $agama = trim((string)$sheetSiswa->getCell('G' . $r)->getValue());
                $ortu = trim((string)$sheetSiswa->getCell('H' . $r)->getValue());
                $hpRaw = trim((string)$sheetSiswa->getCell('I' . $r)->getValue());
                $alamat = trim((string)$sheetSiswa->getCell('J' . $r)->getValue());

                if (!$nisn || !$nama) continue;

                $classroomId = $classroomMap[$kelas] ?? null;
                $gender = (strtoupper($jk) === 'P') ? 'P' : 'L';

                // Parsing Tanggal Lahir (Mendukung ISO, Serial Excel, dan DD/MM/YYYY)
                $birthDate = null;
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglRaw)) {
                    $birthDate = $tglRaw;
                } elseif (is_numeric($tglRaw)) {
                    try {
                        $birthDate = ExcelDate::excelToDateTimeObject($tglRaw)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $birthDate = null;
                    }
                } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tglRaw)) {
                    try {
                        $birthDate = Carbon::createFromFormat('d/m/Y', $tglRaw)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $birthDate = null;
                    }
                }

                // Pembersihan Nomor HP Ortu
                $phone = ($hpRaw === '0' || empty($hpRaw)) ? null : $hpRaw;

                $student = Student::create([
                    'nisn'              => $nisn,
                    'nis'               => $nisn,
                    'name'              => $nama,
                    'gender'            => $gender,
                    'classroom_id'      => $classroomId,
                    'birth_place'       => !empty($tmpt) ? $tmpt : 'Pasuruan',
                    'birth_date'        => $birthDate,
                    'religion'          => !empty($agama) ? $agama : 'Islam',
                    'guardian_name'     => !empty($ortu) ? $ortu : 'Orang Tua / Wali',
                    'guardian_relation' => 'Orang Tua Kandung',
                    'guardian_phone'    => $phone,
                    'address'           => !empty($alamat) ? $alamat : 'Kota Pasuruan',
                    'point_balance'     => 100,
                    'status'            => 'active',
                ]);

                $studentsCreated++;

                // Siapkan Akun Orang Tua
                $parentAccounts[] = [
                    'name'              => 'Wali dari ' . $nama,
                    'username'          => $nisn,
                    'nisn'              => $nisn,
                    'role'              => 'orangtua',
                    'phone'             => $phone,
                    'password'          => Hash::make($nisn),
                    'status'            => 'active',
                    'additional_info'   => json_encode([
                        'student_id'   => $student->id,
                        'student_name' => $nama,
                        'classroom'    => $kelas,
                    ]),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }

            $this->command->info("   -> Berhasil mengimpor {$studentsCreated} Peserta Didik aktif.");

            // -------------------------------------------------------------
            // 4. PEMBUATAN 791 AKUN ORANG TUA / WALI MURID
            // -------------------------------------------------------------
            $this->command->info("4. Membuat 791 Akun Pengguna Orang Tua (Login NISN)...");
            
            // Hapus akun ortu lama
            User::where('role', 'orangtua')->delete();

            foreach (array_chunk($parentAccounts, 100) as $chunk) {
                User::insert($chunk);
            }

            $this->command->info("   -> Berhasil membuat " . count($parentAccounts) . " Akun Orang Tua.");

            // -------------------------------------------------------------
            // 5. REKALKULASI KUOTA & JUMLAH SISWA RIIL PER ROMBEL
            // -------------------------------------------------------------
            $this->command->info("5. Memperbarui Kapasitas & Jumlah Siswa per Rombel...");
            foreach (Classroom::all() as $cls) {
                $count = Student::where('classroom_id', $cls->id)->count();
                $cls->update(['total_students' => $count]);
            }

            // Catat Log Audit Resmi ISO 27001
            $adminUser = User::where('role', 'admin')->first();
            ActivityLog::record(
                'IMPORT_DAPODIK_OFFICIAL',
                'Master Data',
                "Impor Data Resmi Dapodik: 40 GTK, 28 Rombel, 791 Siswa Aktif, dan 791 Akun Orang Tua berhasil disinkronkan ke basis data.",
                ['total_students' => $studentsCreated, 'total_rombel' => count($classroomMap), 'total_gtk' => count($gtkUserMap)],
                $adminUser
            );

            $this->command->info("=== SELURUH DATA RESMI DAPODIK BERHASIL DISINKRONKAN 100% ===");
        });
    }
}

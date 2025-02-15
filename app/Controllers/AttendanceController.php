<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AttendanceController extends BaseController
{
    protected $studentModel;
    protected $attendanceModel;
    protected $attendanceLogModel;
    protected $timeSettingModel;
    protected $absenceStatusModel;
    protected $attendanceStatusModel;
    protected $timeVar;

    public function __construct()
    {
        $this->studentModel = new \App\Models\StudentModel();
        $this->attendanceModel = new \App\Models\AttendanceModel();
        $this->attendanceLogModel = new \App\Models\AttendanceLogTapModel();
        $this->timeSettingModel = new \App\Models\TimeSettingModel();
        $this->absenceStatusModel = new \App\Models\AbsenceStatusModel();
        $this->attendanceStatusModel = new \App\Models\AttendanceStatusModel();

        $this->timeVar = [
            'time_start' => '06.40.00',
            'time_end' => '18.00.00',
            'time_limit' => '12.00.00',
            'free_day' => ['Minggu'],
        ];
    }

    public function index()
    {
        $data = [
            'title' => 'Absensi Sekolah',
            'activePage' => 'attendance',
        ];
        $data['attendance'] = $this->attendanceModel->getAttendance();

        return view('attendance/index', $data);
    }

    public function getAttendance()
    {
        $attendance = $this->attendanceModel->getAttendance(['tanggal' => date('Y-m-d')]);
        return $this->response->setStatusCode(200)->setJSON($attendance);
    }

    public function attendance()
    {
        $data['timeVar'] = $this->timeVar;
        return view('attendance', $data);
    }

    public function show()
    {
        $response['attendanceHistory'] = $this->attendanceModel->getAttendance(['tanggal' => date('Y-m-d')]);

        return $this->response->setStatusCode(200)->setJSON($response);
    }

    public function tap()
    {
        $cardCode = $this->request->getJSON()->card_code;

        // Validasi kode kartu
        if (empty($cardCode)) {
            return $this->response->setStatusCode(400, 'Card code is required')->setJSON(['error' => 'Card code is required']);
        }

        // Mendapatkan waktu saat ini
        $currentDateTime = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');
        $currentTime = date('H.i.s');

        try {
            // Cek apakah card code sudah ada di tabel siswa by condition
            $studentData = $this->studentModel->getStudent(['rfid_tag' => $cardCode]);

            if (!$studentData) {
                // Data siswa tidak ditemukan
                return $this->response->setStatusCode(404, 'Student not found')->setJSON(['error' => 'Student not found']);
            }

            // ambil data absensi hari ini
            $attendanceData = $this->attendanceModel->where('siswa_id', $studentData['siswa_id'])->where('tanggal', $currentDate)->first();
            $timeSettingData = $this->timeSettingModel->where('kelas_id', $studentData['kelas_id'])->first();

            //cek apakah ini jam masuk atau jam pulang
            if ($currentTime >= $this->timeVar['time_start'] && $currentTime <= $this->timeVar['time_limit']) {
                // cek apakah sudah absen masuk
                if ($attendanceData) {
                    return $this->response->setStatusCode(400, 'Attendance already recorded')->setJSON(['error' => 'Attendance already recorded']);
                } else {
                    // Belum absen hari ini, catat absensi masuk
                    $this->attendanceLogModel->insert([
                        'log_id' => uniqid(),
                        'siswa_id' => $studentData['siswa_id'],
                        'tanggal' => $currentDate,
                        'waktu_tap' => $currentDateTime,
                        'jenis_tap' => 'in',
                        'rfid_tag' => $cardCode
                    ]);

                    $this->attendanceModel->insert([
                        'siswa_id' => $studentData['siswa_id'],
                        'tanggal' => $currentDate,
                        'waktu_masuk' => $timeSettingData['jam_masuk'],
                        'waktu_pulang' => $timeSettingData['jam_pulang'],
                        'waktu_masuk_aktual' => date('H:i:s'),
                        'status_kehadiran_id' => 1,
                        'status_absensi_masuk_id' => ($currentTime <= $timeSettingData['jam_masuk']) ? 1 : 2,
                        'status_absensi_pulang_id' => 5,
                    ]);
                }
            } else if ($currentTime > $this->timeVar['time_limit'] && $currentTime <= $this->timeVar['time_end']) {
                //cek apakah sudah absen masuk dan belum absen pulang
                if ($attendanceData) {
                    if (!empty($attendanceData['waktu_pulang_aktual'])) {
                        return $this->response->setStatusCode(400, 'Attendance already recorded')->setJSON(['error' => 'Attendance already recorded']);
                    } else {
                        // Catat absensi pulang
                        $this->attendanceLogModel->insert([
                            'log_id' => uniqid(),
                            'siswa_id' => $studentData['siswa_id'],
                            'tanggal' => $currentDate,
                            'waktu_tap' => $currentDateTime,
                            'jenis_tap' => 'out',
                            'rfid_tag' => $cardCode
                        ]);

                        $this->attendanceModel->update($attendanceData['absensi_id'], [
                            'waktu_pulang_aktual' => date('H:i:s'),
                            'status_absensi_pulang_id' => ($currentTime >= $timeSettingData['jam_pulang']) ? 1 : 3,
                        ]);
                    }
                } else {
                    // Belum absen hari ini, catat absensi keluar
                    $this->attendanceLogModel->insert([
                        'log_id' => uniqid(),
                        'siswa_id' => $studentData['siswa_id'],
                        'tanggal' => $currentDate,
                        'waktu_tap' => $currentDateTime,
                        'jenis_tap' => 'out',
                        'rfid_tag' => $cardCode
                    ]);

                    $this->attendanceModel->insert([
                        'siswa_id' => $studentData['siswa_id'],
                        'tanggal' => $currentDate,
                        'waktu_masuk' => $timeSettingData['jam_masuk'],
                        'waktu_pulang' => $timeSettingData['jam_pulang'],
                        'waktu_pulang_aktual' => date('H:i:s'),
                        'status_kehadiran_id' => 1,
                        'status_absensi_masuk_id' => 4,
                        'status_absensi_pulang_id' => ($currentTime >= $timeSettingData['jam_pulang']) ? 1 : 3,
                    ]);
                }
            } else {
                return $this->response->setStatusCode(400, 'Attendance not allowed')->setJSON(['error' => 'Attendance not allowed']);
            }

            $data = [
                'message' => 'Absensi berhasil dicatat',
                'student' => $studentData,
            ];
            return $this->response->setStatusCode(200)->setJSON($data);
        } catch (\Exception $e) {
            // Tangani kesalahan yang tidak terduga
            // return $this->response->setStatusCode(500, 'An error occurred')->setJSON(['error' => $e->getMessage()]);
            return $this->response->setStatusCode(500, 'An error occurred')->setJSON(['error' => $e->getMessage()]);
        }
    }
}

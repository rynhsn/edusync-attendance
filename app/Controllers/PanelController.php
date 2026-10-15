<?php

namespace App\Controllers;

use App\Libraries\RoleAccess;
use App\Models\AuthUserModel;

class PanelController extends BaseController
{
    public function index()
    {
        $user = session()->get('authUser');
        $role = (string) ($user['role'] ?? 'user');
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('-6 days'));
        $db = db_connect();
        $todayStats = $db->table('absensi_absensi_kelas_siswa aks')
            ->select('COUNT(*) AS total, SUM(aks.status = "hadir") AS hadir, SUM(aks.status = "sakit") AS sakit, SUM(aks.status = "izin") AS izin, SUM(aks.status = "alpa") AS alpa')
            ->join('absensi_absensi_kelas ak', 'ak.absensi_kelas_id = aks.absensi_kelas_id')
            ->where('ak.tanggal', $today)
            ->get()
            ->getRowArray();
        $weeklyAttendance = $db->table('absensi_absensi_kelas_siswa aks')
            ->select('ak.tanggal, COUNT(*) AS total, SUM(aks.status = "hadir") AS hadir')
            ->join('absensi_absensi_kelas ak', 'ak.absensi_kelas_id = aks.absensi_kelas_id')
            ->where('ak.tanggal >=', $weekStart)
            ->where('ak.tanggal <=', $today)
            ->groupBy('ak.tanggal')
            ->orderBy('ak.tanggal')
            ->get()
            ->getResultArray();
        $weeklyByDate = [];
        foreach ($weeklyAttendance as $day) {
            $weeklyByDate[$day['tanggal']] = $day;
        }
        $weeklyAttendance = [];
        for ($offset = 6; $offset >= 0; $offset--) {
            $date = date('Y-m-d', strtotime('-' . $offset . ' days'));
            $weeklyAttendance[] = [
                'date' => $date,
                'label' => date('D', strtotime($date)),
                'total' => (int) ($weeklyByDate[$date]['total'] ?? 0),
                'hadir' => (int) ($weeklyByDate[$date]['hadir'] ?? 0),
            ];
        }
        $recentSessions = $db->table('absensi_absensi_kelas ak')
            ->select('ak.absensi_kelas_id, ak.tanggal, mk.kelas_nama, mm.mapel_nama, mg.guru_nama, COUNT(aks.absensi_kelas_siswa_id) AS jumlah_siswa, SUM(aks.status = "hadir") AS jumlah_hadir')
            ->join('master_kelas mk', 'mk.kelas_id = ak.kelas_id')
            ->join('master_mapel mm', 'mm.mapel_id = ak.mapel_id')
            ->join('master_guru mg', 'mg.guru_id = ak.guru_id')
            ->join('absensi_absensi_kelas_siswa aks', 'aks.absensi_kelas_id = ak.absensi_kelas_id', 'left')
            ->groupBy('ak.absensi_kelas_id')
            ->orderBy('ak.tanggal', 'DESC')
            ->orderBy('ak.absensi_kelas_id', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();
        $stats = [
            'students' => $db->table('master_siswa')->where('status', 'aktif')->where('deleted_at', null)->countAllResults(),
            'classes' => $db->table('master_kelas')->countAllResults(),
            'users' => (new AuthUserModel())->countAll(),
            'teachers' => $db->table('master_guru')->countAllResults(),
            'sessions' => $db->table('absensi_absensi_kelas')->where('tanggal', $today)->countAllResults(),
            'total' => (int) ($todayStats['total'] ?? 0),
            'hadir' => (int) ($todayStats['hadir'] ?? 0),
            'sakit' => (int) ($todayStats['sakit'] ?? 0),
            'izin' => (int) ($todayStats['izin'] ?? 0),
            'alpa' => (int) ($todayStats['alpa'] ?? 0),
        ];

        $cards = [
            ['label' => 'Siswa Aktif', 'value' => $stats['students'], 'icon' => 'bi-people', 'tone' => 'primary'],
            ['label' => 'Hadir Hari Ini', 'value' => $stats['hadir'], 'icon' => 'bi-check-circle', 'tone' => 'success'],
            ['label' => 'Tidak Hadir Hari Ini', 'value' => $stats['sakit'] + $stats['izin'] + $stats['alpa'], 'icon' => 'bi-clipboard2-pulse', 'tone' => 'warning'],
            ['label' => 'Sesi Absensi Hari Ini', 'value' => $stats['sessions'], 'icon' => 'bi-journal-check', 'tone' => 'info'],
        ];
        $statusBreakdown = [
            ['label' => 'Hadir', 'value' => $stats['hadir'], 'tone' => 'success'],
            ['label' => 'Sakit', 'value' => $stats['sakit'], 'tone' => 'warning'],
            ['label' => 'Izin', 'value' => $stats['izin'], 'tone' => 'info'],
            ['label' => 'Alpa', 'value' => $stats['alpa'], 'tone' => 'danger'],
        ];

        return view('panel/index', [
            'title' => 'Dashboard',
            'activePage' => 'panel',
            'role' => $role,
            'roleLabel' => RoleAccess::label($role),
            'cards' => $cards,
            'user' => $user,
            'stats' => $stats,
            'statusBreakdown' => $statusBreakdown,
            'weeklyAttendance' => $weeklyAttendance,
            'recentSessions' => $recentSessions,
            'today' => $today,
        ]);
    }
}

<?php
$loggedInStudent = $loggedInStudent ?? null;
$authUser = $authUser ?? (session()->get('authUser') ?? []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?> - EduSync</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon/favicon.ico'); ?>" type="image/x-icon">
    <!-- CSS Metis / Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('metis-1.0.0/dist-modern/assets/main-Kuf3cpUe.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .cbt-header {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 14px 0;
        }
        .cbt-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .exam-item-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .exam-item-card:hover, .exam-item-card.selected {
            border-color: #2563eb;
            background-color: #eff6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <header class="cbt-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary p-2 fs-6 rounded-3"><i class="bi bi-laptop"></i></span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">EduSync CBT</h5>
                    <small class="text-muted">Portal Ujian & Asesmen Online</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-bold text-dark fs-7"><?= esc($loggedInStudent ? $loggedInStudent['nama_siswa'] : ($authUser['display_name'] ?? 'Pengguna')); ?></div>
                    <small class="text-muted"><?= esc($loggedInStudent ? ($loggedInStudent['kelas_nama'] ?? $loggedInStudent['kelas'] ?? 'Siswa') : ucfirst($authUser['role'] ?? '')); ?></small>
                </div>
                <?php if (($authUser['role'] ?? '') !== 'siswa') : ?>
                    <a href="<?= site_url('panel'); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                <?php endif; ?>
                <a href="<?= site_url('logout'); ?>" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container my-auto py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <?php if (! empty($message)) : ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="status">
                        <i class="bi bi-check-circle-fill me-2"></i><?= esc($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (! empty($error)) : ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card cbt-card bg-white p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="badge bg-primary-subtle text-primary px-3 py-2 fs-6 rounded-pill mb-2">
                            <i class="bi bi-mortarboard me-1"></i> Computer Based Test
                        </div>
                        <h2 class="fw-bold text-dark">Selamat Datang di Ujian Online</h2>
                        <p class="text-muted">Silakan pilih ujian yang akan Anda kerjakan, konfirmasi NIS, dan masukkan token ujian dari guru.</p>
                    </div>

                    <form id="startExamForm" method="post" action="">
                        <?= csrf_field(); ?>

                        <!-- Step 1: Pilih Ujian Aktif -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark fs-6 mb-2">
                                1. Pilih Ujian yang Tersedia <span class="text-danger">*</span>
                            </label>

                            <?php if ($activeExams === []) : ?>
                                <div class="alert alert-warning py-3 text-center mb-0">
                                    <i class="bi bi-info-circle me-1"></i> Saat ini belum ada ujian online yang berstatus aktif untuk kelas Anda.
                                </div>
                            <?php else : ?>
                                <div class="d-flex flex-column gap-2" id="examSelectionList">
                                    <?php foreach ($activeExams as $idx => $exam) : ?>
                                        <label class="exam-item-card p-3 d-flex align-items-center gap-3">
                                            <input type="radio" name="ujian_selection" value="<?= $exam['ujian_id']; ?>" 
                                                data-token-required="<?= ! empty($exam['token']) ? '1' : '0'; ?>"
                                                class="form-check-input m-0 flex-shrink-0" <?= $idx === 0 ? 'checked' : ''; ?>>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-dark fs-6"><?= esc($exam['judul']); ?></div>
                                                <div class="small text-muted">
                                                    <i class="bi bi-book me-1"></i><?= esc($exam['mapel_nama'] ?? 'Semua Mapel'); ?> &bull;
                                                    <i class="bi bi-door-open me-1"></i><?= esc($exam['kelas_nama'] ?? 'Semua Kelas'); ?> &bull;
                                                    <i class="bi bi-clock me-1"></i><?= (int) ($exam['durasi_menit'] ?? 0); ?> Menit (<?= (int) ($exam['total_soal'] ?? 0); ?> Soal)
                                                    <?php if (! empty($exam['tahun_ajaran_nama'])) : ?>
                                                        &bull; <i class="bi bi-calendar3 me-1"></i><?= esc($exam['tahun_ajaran_nama']); ?> (Sem <?= esc($exam['tahun_ajaran_semester']); ?>)
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (! empty($exam['peserta_status'])) : ?>
                                                <span class="badge <?= $exam['peserta_status'] === 'selesai' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info'; ?>">
                                                    <?= $exam['peserta_status'] === 'selesai' ? 'Sudah Selesai (Nilai: ' . $exam['nilai_total'] . ')' : 'Sedang Mengerjakan'; ?>
                                                </span>
                                            <?php elseif (! empty($exam['token'])) : ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25">
                                                    Perlu Token
                                                </span>
                                            <?php endif; ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Step 2: Identitas Peserta / Siswa -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark fs-6 mb-2">
                                2. Identitas Peserta / Siswa <span class="text-danger">*</span>
                            </label>
                            
                            <?php if ($loggedInStudent) : ?>
                                <!-- Identitas Otomatis Siswa yang Sedang Login -->
                                <div class="p-3 rounded-3 border bg-light d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.4rem;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark fs-6"><?= esc($loggedInStudent['nama_siswa']); ?></div>
                                        <div class="text-muted small">
                                            <i class="bi bi-door-open me-1"></i>Kelas: <strong><?= esc($loggedInStudent['kelas_nama'] ?? $loggedInStudent['kelas'] ?? '-'); ?></strong> &bull;
                                            <i class="bi bi-person-badge me-1"></i>Username: <strong><?= esc($authUser['username'] ?? '-'); ?></strong>
                                        </div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i> Akun Siswa Aktif
                                    </span>
                                </div>

                                <div class="mb-2">
                                    <label for="inputNis" class="form-label fw-semibold text-dark small">
                                        Masukkan NIS Siswa Anda <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-key-fill text-primary"></i></span>
                                        <input type="text" class="form-control form-control-lg fw-bold" id="inputNis" name="nis" 
                                            placeholder="Ketikkan NIS Siswa Anda di sini" required autocomplete="off">
                                    </div>
                                    <div class="form-text">Ketikkan NIS resmi Anda untuk verifikasi identitas pengerjaan ujian.</div>
                                </div>
                            <?php else : ?>
                                <!-- Mode Admin / Pengawas Testing -->
                                <div class="alert alert-warning py-2 px-3 small d-flex align-items-center mb-3">
                                    <i class="bi bi-shield-lock-fill me-2 fs-5"></i>
                                    <div><strong>Mode Pengawas / Admin:</strong> Anda sedang login dengan akun non-siswa (<?= esc($authUser['display_name'] ?? ''); ?>). Silakan pilih siswa dari daftar untuk pengujian.</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="siswa_id" class="form-label small text-muted">Pilih Nama Siswa dari Daftar:</label>
                                        <select class="form-select" id="siswa_id" name="siswa_id">
                                            <option value="">-- Pilih Nama Siswa --</option>
                                            <?php foreach ($students as $s) : ?>
                                                <option value="<?= $s['siswa_id']; ?>">
                                                    <?= esc($s['nama_siswa']); ?> (<?= esc($s['kelas_nama'] ?? $s['kelas'] ?? '-'); ?> - NIS: <?= esc($s['nis'] ?? '-'); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputNis" class="form-label small text-muted">Atau Masukkan NIS Siswa:</label>
                                        <input type="text" class="form-control" id="inputNis" name="nis" placeholder="Ketikkan NIS Siswa">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Step 3: Token Ujian (Muncul jika ujian butuh token) -->
                        <div class="mb-4" id="tokenFieldContainer">
                            <label for="tokenInput" class="form-label fw-bold text-dark fs-6 mb-1">
                                3. Masukkan Token Ujian
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-ticket-perforated-fill text-warning"></i></span>
                                <input type="text" class="form-control form-control-lg font-monospace text-uppercase fw-bold" 
                                    id="tokenInput" name="token" placeholder="MASUKKAN TOKEN DARI GURU">
                            </div>
                            <div class="form-text">Minta token ujian kepada guru / pengawas yang bertugas.</div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" id="btnSubmitStart" class="btn btn-primary btn-lg fw-bold py-3 shadow-sm" <?= $activeExams === [] ? 'disabled' : ''; ?>>
                                <i class="bi bi-play-circle-fill me-2"></i> Mulai Mengerjakan Ujian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-3 text-muted small">
        &copy; <?= date('Y'); ?> EduSync Attendance & Assessment System
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('startExamForm');
        const tokenContainer = document.getElementById('tokenFieldContainer');
        const tokenInput = document.getElementById('tokenInput');
        const radios = document.querySelectorAll('input[name="ujian_selection"]');

        function updateSelectedExam() {
            const selected = document.querySelector('input[name="ujian_selection"]:checked');
            if (!selected) return;

            const examId = selected.value;
            form.action = '<?= site_url('cbt/start'); ?>/' + examId;

            const needsToken = selected.getAttribute('data-token-required') === '1';
            if (needsToken) {
                tokenContainer.style.display = 'block';
                tokenInput.required = true;
            } else {
                tokenContainer.style.display = 'none';
                tokenInput.required = false;
            }

            document.querySelectorAll('.exam-item-card').forEach(c => c.classList.remove('selected'));
            selected.closest('.exam-item-card')?.classList.add('selected');
        }

        radios.forEach(r => r.addEventListener('change', updateSelectedExam));
        updateSelectedExam();
    });
    </script>
</body>
</html>

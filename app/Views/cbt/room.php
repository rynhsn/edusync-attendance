<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?> - EduSync CBT</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon/favicon.ico'); ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/vendors/dist-modern/assets/main-Kuf3cpUe.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            user-select: none;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .cbt-room-header {
            background: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .timer-badge {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 8px 16px;
            border-radius: 8px;
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .timer-badge.warning-time {
            background: #dc2626;
            color: #ffffff;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }
        .question-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            min-height: 480px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .option-choice-item {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            display: flex;
            align-items: center;
            gap: 14px;
            background: #ffffff;
        }
        .option-choice-item:hover {
            border-color: #93c5fd;
            background: #f8fafc;
        }
        .option-choice-item.selected {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1e40af;
            font-weight: 600;
        }
        .option-key-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            background: #e2e8f0;
            color: #475569;
            flex-shrink: 0;
            transition: all 0.15s;
        }
        .option-choice-item.selected .option-key-circle {
            background: #2563eb;
            color: #ffffff;
        }
        .palette-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            max-height: 340px;
            overflow-y: auto;
            padding: 4px;
        }
        .btn-palette {
            aspect-ratio: 1;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            cursor: pointer;
        }
        .btn-palette:hover {
            border-color: #2563eb;
        }
        .btn-palette.active {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.4);
            border-color: #2563eb;
        }
        .btn-palette.answered {
            background: #10b981 !important;
            color: #ffffff !important;
            border-color: #059669 !important;
        }
        .btn-palette.doubt {
            background: #f59e0b !important;
            color: #ffffff !important;
            border-color: #d97706 !important;
        }
        .autosave-toast {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1050;
            display: none;
        }
    </style>
</head>
<body>
    <!-- Real-time Save Toast -->
    <div id="saveIndicator" class="autosave-toast badge bg-dark text-white px-3 py-2 shadow-lg fs-7">
        <i class="bi bi-cloud-check text-success me-1"></i> Jawaban otomatis tersimpan
    </div>

    <!-- CBT Header -->
    <header class="cbt-room-header">
        <div class="container-fluid px-lg-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary fs-5 p-2 rounded-3"><i class="bi bi-laptop"></i></span>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark"><?= esc($peserta['judul']); ?></h5>
                        <div class="small text-muted">
                            Peserta: <strong><?= esc($peserta['nama_siswa']); ?></strong> 
                            &bull; Kelas: <?= esc($peserta['kelas_nama'] ?: '-'); ?>
                            <?php if (! empty($peserta['nis'])) : ?>
                                &bull; NIS: <?= esc($peserta['nis']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Timer -->
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block">
                        <small class="text-muted d-block">Sisa Waktu Ujian</small>
                    </div>
                    <div class="timer-badge" id="cbtTimer" title="Sisa waktu pengerjaan ujian">
                        <i class="bi bi-stopwatch"></i>
                        <span id="timerDisplay">--:--:--</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Exam Body -->
    <main class="container-fluid px-lg-4 my-4 flex-grow-1">
        <div class="row g-4">
            <!-- Left Area: Active Question Box -->
            <div class="col-lg-8 col-xl-9">
                <div class="question-card">
                    <div>
                        <!-- Question Header -->
                        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary fs-6 px-3 py-2" id="currentQuestionBadge">
                                    Soal No. 1
                                </span>
                                <span class="badge bg-light text-secondary border fs-7" id="currentTypeBadge">
                                    Pilihan Ganda
                                </span>
                            </div>
                            <div>
                                <span class="text-muted small">
                                    Bobot: <strong id="currentWeight">1</strong> poin
                                </span>
                            </div>
                        </div>

                        <!-- Question Text Container -->
                        <div class="fs-5 text-dark mb-4 lh-base" id="questionText" style="white-space: pre-line;">
                            Memuat soal...
                        </div>

                        <!-- Options for Multiple Choice -->
                        <div id="optionsContainer" class="d-flex flex-column gap-2 mb-4">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <!-- Textarea for Essay -->
                        <div id="essayContainer" class="mb-4" style="display: none;">
                            <label for="essayAnswerInput" class="form-label fw-bold">Tuliskan Jawaban Anda:</label>
                            <textarea class="form-control" id="essayAnswerInput" rows="6" placeholder="Ketikkan uraian jawaban Anda di sini..."></textarea>
                            <div class="form-text">Jawaban tersimpan otomatis secara real-time saat Anda mengetik.</div>
                        </div>
                    </div>

                    <!-- Question Navigation Footer -->
                    <div class="pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary px-3" id="btnPrev">
                            <i class="bi bi-arrow-left me-1"></i> Soal Sebelumnya
                        </button>

                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="doubtCheckbox" role="switch">
                            <label class="form-check-label fw-semibold text-warning-emphasis" for="doubtCheckbox">
                                <i class="bi bi-question-circle"></i> Ragu-Ragu
                            </label>
                        </div>

                        <button type="button" class="btn btn-primary px-3" id="btnNext">
                            Soal Berikutnya <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Area: Navigation Palette -->
            <div class="col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 p-md-4">
                    <h6 class="fw-bold mb-3 d-flex justify-content-between align-items-center text-dark">
                        <span><i class="bi bi-grid-3x3-gap me-1 text-primary"></i> Nomor Soal</span>
                        <span class="badge bg-light text-muted border"><?= count($answers); ?> Butir</span>
                    </h6>

                    <!-- Grid Numbers -->
                    <div class="palette-grid mb-4" id="paletteGrid">
                        <?php foreach ($answers as $idx => $ans) : ?>
                            <div class="btn-palette" 
                                id="pal_<?= $idx; ?>" 
                                data-index="<?= $idx; ?>">
                                <?= $idx + 1; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Legend -->
                    <div class="border-top pt-3 small text-muted d-flex flex-column gap-2 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle bg-success" style="width: 14px; height: 14px;"></span>
                            <span>Sudah Dijawab</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle bg-warning" style="width: 14px; height: 14px;"></span>
                            <span>Ragu-Ragu</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle bg-white border" style="width: 14px; height: 14px;"></span>
                            <span>Belum Dijawab</span>
                        </div>
                    </div>

                    <!-- Selesaikan Ujian Button -->
                    <button type="button" class="btn btn-danger w-100 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#finishModal">
                        <i class="bi bi-check2-circle me-1"></i> Selesaikan Ujian
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Konfirmasi Selesai Ujian -->
    <div class="modal fade" id="finishModal" tabindex="-1" aria-labelledby="finishModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="finishModalLabel">
                        <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Selesaikan Ujian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3">Apakah Anda yakin ingin mengakhiri dan mengumpulkan jawaban ujian ini?</p>
                    
                    <div class="border rounded-3 p-3 bg-light mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total Soal:</span>
                            <strong><?= count($answers); ?> butir</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-success">
                            <span>Sudah Dijawab:</span>
                            <strong id="modalAnsweredCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-danger">
                            <span>Belum Dijawab:</span>
                            <strong id="modalUnansweredCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between text-warning-emphasis">
                            <span>Ditandai Ragu-Ragu:</span>
                            <strong id="modalDoubtCount">0</strong>
                        </div>
                    </div>

                    <div class="alert alert-warning py-2 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Setelah selesai, Anda tidak dapat mengubah jawaban lagi dan sistem akan langsung menilai jawaban pilihan ganda Anda.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Lanjutkan Ujian</button>
                    <form method="post" action="<?= site_url('cbt/finish/' . $peserta['peserta_ujian_id']); ?>">
                        <?= csrf_field(); ?>
                        <button type="submit" class="btn btn-danger fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Ya, Selesai Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Automatic Time Expiry Submission -->
    <form id="autoSubmitForm" method="post" action="<?= site_url('cbt/finish/' . $peserta['peserta_ujian_id']); ?>" style="display: none;">
        <?= csrf_field(); ?>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (() => {
        const questionsData = <?= json_encode($answers); ?>;
        const pesertaUjianId = <?= (int) $peserta['peserta_ujian_id']; ?>;
        const csrfTokenName = '<?= csrf_token(); ?>';
        const csrfHash = '<?= csrf_hash(); ?>';
        const saveAnswerUrl = '<?= site_url('cbt/save-answer'); ?>';
        let remainingSeconds = <?= (int) $sisaDetik; ?>;
        let currentIndex = 0;

        // Elements
        const timerDisplay = document.getElementById('timerDisplay');
        const timerBadge = document.getElementById('cbtTimer');
        const currentBadge = document.getElementById('currentQuestionBadge');
        const typeBadge = document.getElementById('currentTypeBadge');
        const weightSpan = document.getElementById('currentWeight');
        const textContainer = document.getElementById('questionText');
        const optionsContainer = document.getElementById('optionsContainer');
        const essayContainer = document.getElementById('essayContainer');
        const essayInput = document.getElementById('essayAnswerInput');
        const doubtCheck = document.getElementById('doubtCheckbox');
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        const saveIndicator = document.getElementById('saveIndicator');

        // Modal elements
        const modalAnswered = document.getElementById('modalAnsweredCount');
        const modalUnanswered = document.getElementById('modalUnansweredCount');
        const modalDoubt = document.getElementById('modalDoubtCount');

        // 1. Timer Logic
        function updateTimer() {
            if (remainingSeconds <= 0) {
                timerDisplay.textContent = '00:00:00';
                alert('Waktu ujian telah habis! Sistem akan mengumpulkan jawaban Anda secara otomatis.');
                document.getElementById('autoSubmitForm').submit();
                return;
            }

            const hours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;

            const pad = (n) => String(n).padStart(2, '0');
            timerDisplay.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

            if (remainingSeconds <= 300) { // 5 menit terakhir
                timerBadge.classList.add('warning-time');
            }

            remainingSeconds--;
        }
        setInterval(updateTimer, 1000);
        updateTimer();

        // 2. Render Current Question
        function renderQuestion(index) {
            if (index < 0 || index >= questionsData.length) return;
            currentIndex = index;

            const q = questionsData[index];
            currentBadge.textContent = `Soal No. ${index + 1}`;
            typeBadge.textContent = q.tipe_soal === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay / Uraian';
            weightSpan.textContent = parseFloat(q.bobot_nilai || 1).toString();
            textContainer.textContent = q.pertanyaan || '';

            doubtCheck.checked = parseInt(q.ragu_ragu) === 1;

            if (q.tipe_soal === 'pilihan_ganda') {
                optionsContainer.style.display = 'flex';
                essayContainer.style.display = 'none';
                renderOptions(q);
            } else {
                optionsContainer.style.display = 'none';
                essayContainer.style.display = 'block';
                essayInput.value = q.jawaban_essay || '';
            }

            btnPrev.disabled = index === 0;
            btnNext.textContent = index === questionsData.length - 1 ? 'Selesai Ujian' : 'Soal Berikutnya';

            updatePaletteHighlights();
            updateModalCounts();
        }

        // 3. Render Choices for Multiple Choice
        function renderOptions(q) {
            optionsContainer.innerHTML = '';
            const opts = [
                { key: 'A', text: q.opsi_a },
                { key: 'B', text: q.opsi_b },
                { key: 'C', text: q.opsi_c },
                { key: 'D', text: q.opsi_d },
                { key: 'E', text: q.opsi_e }
            ];

            const currentChoice = (q.jawaban_pg || '').toUpperCase();

            opts.forEach(opt => {
                if (!opt.text && opt.key === 'E') return;

                const isSelected = currentChoice === opt.key;
                const item = document.createElement('div');
                item.className = `option-choice-item ${isSelected ? 'selected' : ''}`;
                item.innerHTML = `
                    <div class="option-key-circle">${opt.key}</div>
                    <div class="flex-grow-1">${escapeHtml(opt.text || '-')}</div>
                `;

                item.addEventListener('click', () => {
                    q.jawaban_pg = opt.key;
                    document.querySelectorAll('.option-choice-item').forEach(el => el.classList.remove('selected'));
                    item.classList.add('selected');
                    saveAnswerToServer(q.soal_id, opt.key, null, q.ragu_ragu);
                    updatePaletteHighlights();
                });

                optionsContainer.appendChild(item);
            });
        }

        // 4. Handle Essay input with debounce
        let essayDebounceTimeout = null;
        essayInput.addEventListener('input', () => {
            const q = questionsData[currentIndex];
            q.jawaban_essay = essayInput.value;
            clearTimeout(essayDebounceTimeout);
            essayDebounceTimeout = setTimeout(() => {
                saveAnswerToServer(q.soal_id, null, q.jawaban_essay, q.ragu_ragu);
                updatePaletteHighlights();
            }, 600);
        });

        // 5. Handle Doubt checkbox
        doubtCheck.addEventListener('change', () => {
            const q = questionsData[currentIndex];
            q.ragu_ragu = doubtCheck.checked ? 1 : 0;
            saveAnswerToServer(q.soal_id, q.jawaban_pg, q.jawaban_essay, q.ragu_ragu);
            updatePaletteHighlights();
        });

        // 6. Navigation Buttons
        btnPrev.addEventListener('click', () => {
            if (currentIndex > 0) renderQuestion(currentIndex - 1);
        });

        btnNext.addEventListener('click', () => {
            if (currentIndex < questionsData.length - 1) {
                renderQuestion(currentIndex + 1);
            } else {
                const modal = new bootstrap.Modal(document.getElementById('finishModal'));
                modal.show();
            }
        });

        // 7. Palette Grid Clicks
        document.querySelectorAll('.btn-palette').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.getAttribute('data-index'));
                renderQuestion(idx);
            });
        });

        // 8. Update Palette Colors & Active State
        function updatePaletteHighlights() {
            questionsData.forEach((q, idx) => {
                const pal = document.getElementById(`pal_${idx}`);
                if (!pal) return;

                pal.classList.remove('active', 'answered', 'doubt');

                const isAnswered = (q.tipe_soal === 'pilihan_ganda' && q.jawaban_pg && q.jawaban_pg.trim() !== '') ||
                                   (q.tipe_soal === 'essay' && q.jawaban_essay && q.jawaban_essay.trim() !== '');

                const isDoubt = parseInt(q.ragu_ragu) === 1;

                if (isDoubt) {
                    pal.classList.add('doubt');
                } else if (isAnswered) {
                    pal.classList.add('answered');
                }

                if (idx === currentIndex) {
                    pal.classList.add('active');
                }
            });
        }

        // 9. Update Modal Counts
        function updateModalCounts() {
            let answered = 0;
            let doubts = 0;
            questionsData.forEach(q => {
                const isAnswered = (q.tipe_soal === 'pilihan_ganda' && q.jawaban_pg && q.jawaban_pg.trim() !== '') ||
                                   (q.tipe_soal === 'essay' && q.jawaban_essay && q.jawaban_essay.trim() !== '');
                if (isAnswered) answered++;
                if (parseInt(q.ragu_ragu) === 1) doubts++;
            });

            modalAnswered.textContent = `${answered} butir`;
            modalUnanswered.textContent = `${questionsData.length - answered} butir`;
            modalDoubt.textContent = `${doubts} butir`;
        }

        // 10. AJAX Save to Server
        function saveAnswerToServer(soalId, jawabanPg, jawabanEssay, raguRagu) {
            const formData = new FormData();
            formData.append('peserta_ujian_id', pesertaUjianId);
            formData.append('soal_id', soalId);
            if (jawabanPg !== null) formData.append('jawaban_pg', jawabanPg);
            if (jawabanEssay !== null) formData.append('jawaban_essay', jawabanEssay);
            formData.append('ragu_ragu', raguRagu ? 1 : 0);
            formData.append(csrfTokenName, csrfHash);

            showSaveToast();

            fetch(saveAnswerUrl, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            }).catch(err => console.error('Save error:', err));
        }

        let toastTimeout = null;
        function showSaveToast() {
            saveIndicator.style.display = 'block';
            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => {
                saveIndicator.style.display = 'none';
            }, 1500);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initialize First Question
        renderQuestion(0);
    })();
    </script>
</body>
</html>

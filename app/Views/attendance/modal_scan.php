<!-- Modal Absen Scan Barcode & QR Code -->
<div class="modal fade" id="scanAttendanceModal" tabindex="-1" aria-labelledby="scanAttendanceModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden text-dark" style="background: #ffffff;">
            <!-- Header Modal -->
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-1" id="scanAttendanceModalLabel" style="color: #1e293b;">
                        <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                            <i class="ti ti-scan fs-4"></i>
                        </span>
                        Absen Scan (Barcode & QR Code)
                    </h5>
                    <p class="text-muted small mb-0">Arahkan barcode / QR code ID Card ke kamera atau gunakan scanner barcode.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold">
                        <i class="ti ti-clock text-primary me-1"></i>
                        <span id="scanModalLiveClock">--:--:--</span>
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>

            <!-- Body Modal -->
            <div class="modal-body px-4 py-3">
                <!-- Mode Absensi -->
                <div class="bg-light p-2 rounded-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2 border">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small fw-bold text-muted ps-2"><i class="ti ti-settings me-1"></i> Mode:</span>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Mode Absensi">
                            <input type="radio" class="btn-check" name="scanModeOption" id="scanModeAuto" value="auto" checked>
                            <label class="btn btn-outline-primary fw-medium px-3" for="scanModeAuto">
                                <i class="ti ti-sparkles me-1"></i> Otomatis
                            </label>

                            <input type="radio" class="btn-check" name="scanModeOption" id="scanModeIn" value="in">
                            <label class="btn btn-outline-success fw-medium px-3" for="scanModeIn">
                                <i class="ti ti-login me-1"></i> Masuk (Hadir)
                            </label>

                            <input type="radio" class="btn-check" name="scanModeOption" id="scanModeOut" value="out">
                            <label class="btn btn-outline-danger fw-medium px-3" for="scanModeOut">
                                <i class="ti ti-logout me-1"></i> Pulang
                            </label>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-2 small">
                            <i class="ti ti-check me-1"></i> Otomatis Dianggap Hadir
                        </span>
                    </div>
                </div>

                <!-- Live Camera Section -->
                <div class="scanner-card position-relative rounded-4 p-2 mb-3 bg-dark text-white text-center shadow-inner" style="min-height: 260px; overflow: hidden; background: #0f172a !important;">
                    <div id="scanner-reader" style="width: 100%; border-radius: 12px; overflow: hidden;"></div>
                    
                    <!-- Scanner Laser Overlay -->
                    <div id="scanner-laser-line" class="scanner-laser" style="display: none;"></div>
                    
                    <!-- Status Overlay -->
                    <div id="scanner-status-badge" class="position-absolute top-0 start-50 translate-middle-x mt-3 z-3">
                        <span class="badge bg-dark bg-opacity-75 border border-secondary text-light px-3 py-1 rounded-pill small">
                            <i class="ti ti-camera me-1"></i> Menyiapkan kamera scanner...
                        </span>
                    </div>

                    <!-- Camera Controls -->
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2 z-3 d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border border-secondary text-white rounded-pill px-3" id="btn-toggle-camera">
                            <i class="ti ti-camera-rotate me-1"></i> Ganti Kamera
                        </button>
                        <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border border-secondary text-white rounded-pill px-3" id="btn-restart-camera">
                            <i class="ti ti-refresh me-1"></i> Muat Ulang Kamera
                        </button>
                    </div>
                </div>

                <!-- Input Scanner / Manual -->
                <div class="card border rounded-3 p-3 mb-3 bg-white shadow-sm">
                    <label for="scan-barcode-input" class="form-label small fw-bold text-muted mb-2 d-flex justify-content-between">
                        <span><i class="ti ti-barcode me-1"></i> Scan Barcode Scanner USB atau Ketik Kode / NIS / RFID:</span>
                        <span class="badge bg-secondary-subtle text-secondary font-monospace" id="scan-last-timestamp">Jam: --:--:--</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="ti ti-scan text-primary fs-4"></i>
                        </span>
                        <input type="text" id="scan-barcode-input" class="form-control border-start-0 ps-0 fw-bold font-monospace" placeholder="Arahkan scanner atau ketik barcode (contoh: 1356657841)" autocomplete="off">
                        <button class="btn btn-primary px-4 fw-bold d-flex align-items-center gap-1" type="button" id="btn-submit-scan">
                            <i class="ti ti-check fs-5"></i> Absen Hadir
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted"><i class="ti ti-info-circle me-1"></i> Tekan <b>Enter</b> setelah scan / ketik kode kartu untuk absen instan.</small>
                        <small class="text-muted">Status: <span class="text-success fw-semibold">Hadir</span></small>
                    </div>
                </div>

                <!-- Quick Pick / Dropdown Siswa & Guru -->
                <?php if (! empty($students) || ! empty($teachers)) : ?>
                    <div class="card border rounded-3 p-3 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label for="quick-select-person" class="form-label small fw-bold text-muted mb-0">
                                <i class="ti ti-users me-1"></i> Atau Pilih Cepat Siswa / Guru:
                            </label>
                            <span class="badge bg-info-subtle text-info small">Pilih & Langsung Hadir</span>
                        </div>
                        <div class="input-group">
                            <select id="quick-select-person" class="form-select">
                                <option value="">-- Pilih Nama Siswa atau Guru --</option>
                                <?php if (! empty($students)) : ?>
                                    <optgroup label="--- SISWA ---">
                                        <?php foreach ($students as $s) : ?>
                                            <?php
                                            $tagVal = ! empty($s['rfid_tag']) ? $s['rfid_tag'] : (! empty($s['nis']) ? $s['nis'] : $s['siswa_id']);
                                            ?>
                                            <option value="<?= esc($tagVal); ?>" data-name="<?= esc($s['nama_siswa']); ?>" data-role="Siswa (<?= esc($s['kelas'] ?? '-'); ?>)">
                                                <?= esc($s['nama_siswa']); ?> (<?= esc($s['kelas'] ?? 'Siswa'); ?>) - Barcode: <?= esc($tagVal); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                                <?php if (! empty($teachers)) : ?>
                                    <optgroup label="--- GURU ---">
                                        <?php foreach ($teachers as $t) : ?>
                                            <?php
                                            $tagVal = ! empty($t['rfid_tag']) ? $t['rfid_tag'] : (! empty($t['guru_nip']) ? $t['guru_nip'] : $t['guru_id']);
                                            ?>
                                            <option value="<?= esc($tagVal); ?>" data-name="<?= esc($t['guru_nama']); ?>" data-role="Guru">
                                                <?= esc($t['guru_nama']); ?> (Guru) - Barcode: <?= esc($tagVal); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                            </select>
                            <button class="btn btn-outline-primary" type="button" id="btn-quick-submit">
                                <i class="ti ti-check me-1"></i> Absenkan
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Last Result Card -->
                <div id="scan-result-card" class="card border border-success-subtle bg-success-subtle bg-opacity-25 rounded-3 p-3 mt-3 shadow-sm" style="display: none;">
                    <div class="d-flex align-items-center gap-3">
                        <img id="scan-result-photo" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" alt="Foto Profil" class="rounded-circle border border-2 border-white shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold text-dark" id="scan-result-name">-</h6>
                                <span class="badge bg-success text-white px-2 py-1 rounded-pill fw-semibold" id="scan-result-badge">
                                    <i class="ti ti-check me-1"></i> HADIR
                                </span>
                            </div>
                            <p class="mb-0 small text-muted" id="scan-result-role">-</p>
                            <div class="d-flex align-items-center gap-3 mt-1 small">
                                <span class="text-success fw-medium"><i class="ti ti-clock me-1"></i> Jam: <b id="scan-result-time">--:--:--</b></span>
                                <span class="text-muted"><i class="ti ti-calendar me-1"></i> Tanggal: <span id="scan-result-date">--</span></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Modal -->
            <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between align-items-center">
                <span class="small text-muted">
                    <i class="ti ti-device-laptop me-1"></i> Edusync Attendance Scanner
                </span>
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Scanner Camera Viewport */
    #scanner-reader video {
        border-radius: 12px;
        object-fit: cover;
        width: 100% !important;
        max-height: 280px;
    }
    #scanner-reader {
        position: relative;
    }
    /* Animated laser line for scanning effect */
    .scanner-laser {
        position: absolute;
        top: 20%;
        left: 10%;
        width: 80%;
        height: 2px;
        background: #ef4444;
        box-shadow: 0 0 12px 2px #ef4444;
        animation: scanAnimation 2s infinite ease-in-out alternate;
        z-index: 2;
        pointer-events: none;
    }
    @keyframes scanAnimation {
        0% { top: 20%; opacity: 0.8; }
        50% { opacity: 1; }
        100% { top: 80%; opacity: 0.8; }
    }
</style>

<!-- Load html5-qrcode library -->
<script src="<?= base_url('assets/js/html5-qrcode.min.js'); ?>"></script>

<script>
    (function() {
        let html5Scanner = null;
        let isProcessingScan = false;
        let availableCameras = [];
        let currentCameraIndex = 0;
        let clockTimer = null;

        // Audio Beep generator using Web Audio API
        function playBeepSound(isSuccess = true) {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (isSuccess) {
                    osc.frequency.setValueAtTime(880, ctx.currentTime); // 880Hz
                    gain.gain.setValueAtTime(0.35, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.18);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.18);
                } else {
                    osc.frequency.setValueAtTime(320, ctx.currentTime);
                    gain.gain.setValueAtTime(0.4, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.3);
                }
            } catch (err) {
                // AudioContext blocked or unsupported
            }
        }

        // Update live clock in modal
        function updateModalClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const clockEl = document.getElementById('scanModalLiveClock');
            if (clockEl) clockEl.textContent = timeStr;
            const tsEl = document.getElementById('scan-last-timestamp');
            if (tsEl) tsEl.textContent = 'Jam: ' + timeStr;
        }

        // Initialize camera scanner
        function initCameraScanner() {
            const readerEl = document.getElementById('scanner-reader');
            const statusBadge = document.getElementById('scanner-status-badge');
            const laserLine = document.getElementById('scanner-laser-line');
            if (!readerEl || typeof Html5Qrcode === 'undefined') return;

            if (html5Scanner) {
                try {
                    html5Scanner.stop().catch(() => {}).then(() => {
                        html5Scanner = null;
                        startScannerInstance();
                    });
                    return;
                } catch (e) {
                    html5Scanner = null;
                }
            }
            startScannerInstance();

            function startScannerInstance() {
                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length) {
                        availableCameras = devices;
                        const selectedCam = devices[currentCameraIndex % devices.length];

                        html5Scanner = new Html5Qrcode("scanner-reader");
                        const config = {
                            fps: 15,
                            qrbox: { width: 280, height: 160 },
                            aspectRatio: 1.5,
                            formatsToSupport: [
                                Html5QrcodeSupportedFormats.CODE_128,
                                Html5QrcodeSupportedFormats.QR_CODE,
                                Html5QrcodeSupportedFormats.CODE_39,
                                Html5QrcodeSupportedFormats.EAN_13,
                                Html5QrcodeSupportedFormats.EAN_8,
                                Html5QrcodeSupportedFormats.UPC_A
                            ]
                        };

                        html5Scanner.start(
                            selectedCam.id,
                            config,
                            (decodedText, decodedResult) => {
                                // Scanned code!
                                if (!isProcessingScan && decodedText) {
                                    handleCardScan(decodedText);
                                }
                            },
                            (errorMessage) => {
                                // Frame error, skip
                            }
                        ).then(() => {
                            if (statusBadge) {
                                statusBadge.innerHTML = '<span class="badge bg-success bg-opacity-75 text-light px-3 py-1 rounded-pill small"><i class="ti ti-camera me-1"></i> Kamera Aktif (' + selectedCam.label.slice(0, 20) + ')</span>';
                            }
                            if (laserLine) laserLine.style.display = 'block';
                        }).catch(err => {
                            console.warn("Camera start failed:", err);
                            if (statusBadge) {
                                statusBadge.innerHTML = '<span class="badge bg-warning bg-opacity-75 text-dark px-3 py-1 rounded-pill small"><i class="ti ti-alert-triangle me-1"></i> Kamera tidak aktif. Silakan gunakan scanner barcode di bawah.</span>';
                            }
                            if (laserLine) laserLine.style.display = 'none';
                        });
                    } else {
                        if (statusBadge) {
                            statusBadge.innerHTML = '<span class="badge bg-secondary text-light px-3 py-1 rounded-pill small"><i class="ti ti-camera-off me-1"></i> Tidak ada kamera terdeteksi. Gunakan input barcode di bawah.</span>';
                        }
                    }
                }).catch(err => {
                    console.warn("Camera permission error:", err);
                    if (statusBadge) {
                        statusBadge.innerHTML = '<span class="badge bg-secondary text-light px-3 py-1 rounded-pill small"><i class="ti ti-camera-off me-1"></i> Izin kamera belum diberikan. Gunakan input barcode di bawah.</span>';
                    }
                });
            }
        }

        // Stop camera scanner
        function stopCameraScanner() {
            const laserLine = document.getElementById('scanner-laser-line');
            if (laserLine) laserLine.style.display = 'none';
            if (html5Scanner) {
                html5Scanner.stop().then(() => {
                    html5Scanner.clear();
                    html5Scanner = null;
                }).catch(() => {
                    html5Scanner = null;
                });
            }
        }

        // Handle scan / attendance submission
        function handleCardScan(code) {
            code = String(code).trim();
            if (!code || isProcessingScan) return;

            isProcessingScan = true;
            const modeOption = document.querySelector('input[name="scanModeOption"]:checked')?.value || 'auto';
            const inputField = document.getElementById('scan-barcode-input');
            const submitBtn = document.getElementById('btn-submit-scan');
            if (inputField) inputField.value = code;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
            }

            $.ajax({
                url: '<?= base_url('attendance/tap'); ?>',
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    card_code: code,
                    scan_type: modeOption
                }),
                success: function(res) {
                    playBeepSound(true);

                    const personName = res.student?.nama_siswa || res.person?.nama || 'Siswa / Guru';
                    const roleLabel = res.role === 'teacher' ? 'Guru / Pengajar' : ('Kelas: ' + (res.student?.kelas || '-'));
                    const photo = res.photo_url || 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';
                    const actTime = res.waktu_aktual || new Date().toLocaleTimeString('id-ID');
                    const actDate = res.tanggal || new Date().toLocaleDateString('id-ID');
                    const statusText = res.status_kehadiran || 'Hadir';

                    // Update Result Card
                    const resultCard = document.getElementById('scan-result-card');
                    if (resultCard) {
                        document.getElementById('scan-result-photo').src = photo;
                        document.getElementById('scan-result-name').textContent = personName;
                        document.getElementById('scan-result-role').textContent = roleLabel;
                        document.getElementById('scan-result-time').textContent = actTime;
                        document.getElementById('scan-result-date').textContent = actDate;
                        document.getElementById('scan-result-badge').innerHTML = '<i class="ti ti-check me-1"></i> ' + statusText.toUpperCase();
                        resultCard.style.display = 'block';
                    }

                    // SweetAlert Feedback
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '<span class="text-success fw-bold"><i class="ti ti-check-circle me-1"></i> Hadir!</span>',
                            html: `
                                <div class="text-center my-2">
                                    <img src="${photo}" class="rounded-circle shadow-sm border border-2 border-success mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                    <h5 class="fw-bold text-dark mb-0">${personName}</h5>
                                    <span class="badge bg-primary-subtle text-primary mb-2">${roleLabel}</span>
                                    <div class="alert alert-success py-2 px-3 small mb-1">
                                        <i class="ti ti-clock me-1"></i> <b>${res.message || 'Absensi berhasil dicatat'}</b><br>
                                        <small class="text-muted">Waktu Generate: <b>${actTime}</b> | Status: <b>${statusText}</b></small>
                                    </div>
                                    <small class="text-muted">${res.whatsapp || ''}</small>
                                </div>
                            `,
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true
                        });
                    }

                    // Refresh kiosk history marquee if available
                    if (typeof getHistory === 'function') {
                        getHistory();
                    }

                    // If table exists (on attendance/manage panel), reload or append
                    if (window.location.pathname.includes('/attendance/manage')) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
                    }

                    // Clear input for next scan
                    if (inputField) {
                        inputField.value = '';
                        inputField.focus();
                    }
                },
                error: function(err) {
                    playBeepSound(false);
                    const errorMsg = err.responseJSON?.error || 'Gagal memproses absensi barcode.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Absensi Gagal',
                            text: errorMsg,
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Gagal: ' + errorMsg);
                    }
                    if (inputField) {
                        inputField.select();
                        inputField.focus();
                    }
                },
                complete: function() {
                    setTimeout(() => {
                        isProcessingScan = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="ti ti-check fs-5"></i> Absen Hadir';
                        }
                    }, 1200);
                }
            });
        }

        // Setup DOM event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const scanModalEl = document.getElementById('scanAttendanceModal');
            if (!scanModalEl) return;

            // When modal opens
            scanModalEl.addEventListener('shown.bs.modal', function() {
                initCameraScanner();
                updateModalClock();
                if (!clockTimer) {
                    clockTimer = setInterval(updateModalClock, 1000);
                }
                const inputField = document.getElementById('scan-barcode-input');
                if (inputField) {
                    inputField.value = '';
                    inputField.focus();
                }
            });

            // When modal closes
            scanModalEl.addEventListener('hidden.bs.modal', function() {
                stopCameraScanner();
                if (clockTimer) {
                    clearInterval(clockTimer);
                    clockTimer = null;
                }
                const resultCard = document.getElementById('scan-result-card');
                if (resultCard) resultCard.style.display = 'none';
            });

            // Submit barcode input on Enter key
            const barcodeInput = document.getElementById('scan-barcode-input');
            if (barcodeInput) {
                barcodeInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const val = this.value.trim();
                        if (val) handleCardScan(val);
                    }
                });
            }

            // Submit button click
            const submitBtn = document.getElementById('btn-submit-scan');
            if (submitBtn) {
                submitBtn.addEventListener('click', function() {
                    const val = document.getElementById('scan-barcode-input')?.value.trim();
                    if (val) {
                        handleCardScan(val);
                    } else {
                        document.getElementById('scan-barcode-input')?.focus();
                    }
                });
            }

            // Quick select submit
            const quickBtn = document.getElementById('btn-quick-submit');
            if (quickBtn) {
                quickBtn.addEventListener('click', function() {
                    const sel = document.getElementById('quick-select-person');
                    const val = sel?.value;
                    if (val) {
                        handleCardScan(val);
                    } else {
                        alert('Silakan pilih salah satu siswa atau guru.');
                    }
                });
            }

            // Camera control buttons
            const toggleCamBtn = document.getElementById('btn-toggle-camera');
            if (toggleCamBtn) {
                toggleCamBtn.addEventListener('click', function() {
                    if (availableCameras.length > 1) {
                        currentCameraIndex++;
                        initCameraScanner();
                    } else {
                        alert('Hanya satu kamera yang terdeteksi pada perangkat ini.');
                    }
                });
            }

            const restartCamBtn = document.getElementById('btn-restart-camera');
            if (restartCamBtn) {
                restartCamBtn.addEventListener('click', function() {
                    initCameraScanner();
                });
            }
        });
    })();
</script>

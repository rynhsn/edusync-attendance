<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1 d-flex align-items-center gap-2">
                <i class="ti ti-scan text-primary"></i>
                Absen Scan Barcode & QR Code
            </h1>
            <p class="text-muted mb-0">Pemindaian absensi langsung menggunakan kamera web / barcode scanner ID Card.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('attendance/manage'); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i> Data Absensi
            </a>
            <a href="<?= base_url('attendance'); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="ti ti-device-desktop me-1"></i> Buka Kiosk Layar Penuh
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Scanner Kamera & Input -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="ti ti-camera text-primary"></i>
                        Kamera Pemindai Live
                    </h5>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                        <i class="ti ti-circle-filled me-1 small"></i> Otomatis Hadir
                    </span>
                </div>
                <div class="card-body p-4">
                    <!-- Scanner Box -->
                    <div class="position-relative rounded-4 p-2 bg-dark text-white text-center shadow-inner mb-3" style="min-height: 280px; overflow: hidden; background: #0f172a !important;">
                        <div id="dedicated-scanner-reader" style="width: 100%; border-radius: 12px; overflow: hidden;"></div>
                        <div id="dedicated-scanner-laser" class="scanner-laser" style="display: none;"></div>
                        
                        <div id="dedicated-camera-status" class="position-absolute top-0 start-50 translate-middle-x mt-3 z-3">
                            <span class="badge bg-dark bg-opacity-75 border border-secondary text-light px-3 py-1 rounded-pill small">
                                <i class="ti ti-camera me-1"></i> Menyiapkan kamera...
                            </span>
                        </div>

                        <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2 z-3 d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border border-secondary text-white rounded-pill px-3" id="btn-dedicated-switch-cam">
                                <i class="ti ti-camera-rotate me-1"></i> Ganti Kamera
                            </button>
                            <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border border-secondary text-white rounded-pill px-3" id="btn-dedicated-restart-cam">
                                <i class="ti ti-refresh me-1"></i> Muat Ulang
                            </button>
                        </div>
                    </div>

                    <!-- Input Manual / Hardware Barcode -->
                    <div class="mb-3">
                        <label for="dedicated-barcode-input" class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span><i class="ti ti-barcode me-1 text-primary"></i> Scan Alat Barcode / Masukkan Kode:</span>
                            <span class="badge bg-primary-subtle text-primary font-monospace" id="dedicated-live-clock">--:--:--</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ti ti-scan text-primary fs-3"></i>
                            </span>
                            <input type="text" id="dedicated-barcode-input" class="form-control border-start-0 ps-0 fw-bold font-monospace" placeholder="Arahkan barcode scanner atau ketik RFID / NIS..." autofocus autocomplete="off">
                            <button class="btn btn-primary px-4 fw-bold" type="button" id="btn-dedicated-submit">
                                <i class="ti ti-check me-1"></i> Absen Hadir
                            </button>
                        </div>
                    </div>

                    <!-- Pilihan Cepat -->
                    <?php if (! empty($students) || ! empty($teachers)) : ?>
                        <div class="p-3 bg-light rounded-3 border">
                            <label for="dedicated-quick-select" class="form-label small fw-bold text-muted mb-2">
                                <i class="ti ti-user-check me-1"></i> Atau Pilih Langsung dari Daftar:
                            </label>
                            <div class="input-group">
                                <select id="dedicated-quick-select" class="form-select">
                                    <option value="">-- Pilih Siswa atau Guru --</option>
                                    <?php if (! empty($students)) : ?>
                                        <optgroup label="--- SISWA ---">
                                            <?php foreach ($students as $s) : ?>
                                                <?php
                                                $tagVal = ! empty($s['rfid_tag']) ? $s['rfid_tag'] : (! empty($s['nis']) ? $s['nis'] : $s['siswa_id']);
                                                ?>
                                                <option value="<?= esc($tagVal); ?>">
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
                                                <option value="<?= esc($tagVal); ?>">
                                                    <?= esc($t['guru_nama']); ?> (Guru) - Barcode: <?= esc($tagVal); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                </select>
                                <button class="btn btn-outline-primary" type="button" id="btn-dedicated-quick-submit">
                                    <i class="ti ti-check me-1"></i> Absenkan
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Status & Riwayat Terakhir -->
        <div class="col-lg-5">
            <!-- Hasil Terakhir -->
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="ti ti-user-check text-success"></i>
                        Status Hasil Scan Terakhir
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div id="dedicated-result-placeholder" class="py-4 text-muted">
                        <i class="ti ti-qrcode fs-1 opacity-25 d-block mb-2"></i>
                        <p class="mb-0">Belum ada scan pada sesi ini.<br>Arahkan barcode atau scan ID Card untuk mulai.</p>
                    </div>

                    <div id="dedicated-result-card" style="display: none;">
                        <img id="dedicated-result-photo" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" alt="Foto Profil" class="rounded-circle shadow border border-3 border-success mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <h4 class="fw-bold mb-1 text-dark" id="dedicated-result-name">-</h4>
                        <span class="badge bg-primary-subtle text-primary mb-3 fs-6 px-3 py-1 rounded-pill" id="dedicated-result-role">-</span>
                        
                        <div class="alert alert-success text-start py-3 px-3 rounded-3 shadow-sm mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-success"><i class="ti ti-check-circle me-1"></i> <span id="dedicated-result-status">HADIR</span></span>
                                <span class="badge bg-success text-white" id="dedicated-result-time">--:--:--</span>
                            </div>
                            <small class="text-muted d-block" id="dedicated-result-message">Absensi berhasil dicatat</small>
                            <small class="text-muted d-block mt-1" id="dedicated-result-wa"></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan Singkat -->
            <div class="card shadow-sm border-0 rounded-4 p-4 bg-primary bg-opacity-10 border-primary border-opacity-25">
                <h6 class="fw-bold text-primary mb-2 d-flex align-items-center gap-1">
                    <i class="ti ti-info-circle"></i> Petunjuk Absen Scan:
                </h6>
                <ul class="small text-muted ps-3 mb-0">
                    <li class="mb-1">Barcode atau QR code yang dicetak di ID Card dapat langsung diarahkan ke kotak kamera pemindai.</li>
                    <li class="mb-1">Jika menggunakan alat barcode scanner USB, pastikan kursor aktif di kotak input dan tembakkan scanner.</li>
                    <li class="mb-1">Kehadiran otomatis dicatat dengan jam aktual saat digenerate / discan.</li>
                    <li>Siswa atau Guru yang berhasil dipindai akan otomatis berstatus <b>Hadir</b>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    #dedicated-scanner-reader video {
        border-radius: 12px;
        object-fit: cover;
        width: 100% !important;
        max-height: 280px;
    }
    .scanner-laser {
        position: absolute;
        top: 20%;
        left: 10%;
        width: 80%;
        height: 2px;
        background: #ef4444;
        box-shadow: 0 0 12px 2px #ef4444;
        animation: dedicatedScanAnim 2s infinite ease-in-out alternate;
        z-index: 2;
        pointer-events: none;
    }
    @keyframes dedicatedScanAnim {
        0% { top: 20%; opacity: 0.8; }
        50% { opacity: 1; }
        100% { top: 80%; opacity: 0.8; }
    }
</style>

<script src="<?= base_url('assets/js/html5-qrcode.min.js'); ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let dedicatedScanner = null;
        let isProcessing = false;
        let camList = [];
        let activeCamIndex = 0;

        function playBeep(success = true) {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                if (success) {
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
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
            } catch (e) {}
        }

        function updateClock() {
            const timeStr = new Date().toLocaleTimeString('id-ID', { hour12: false });
            const clockEl = document.getElementById('dedicated-live-clock');
            if (clockEl) clockEl.textContent = 'Jam: ' + timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        function startScanner() {
            const statusBadge = document.getElementById('dedicated-camera-status');
            const laser = document.getElementById('dedicated-scanner-laser');
            if (typeof Html5Qrcode === 'undefined') return;

            if (dedicatedScanner) {
                dedicatedScanner.stop().catch(() => {}).then(() => {
                    dedicatedScanner = null;
                    beginScanner();
                });
                return;
            }
            beginScanner();

            function beginScanner() {
                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length) {
                        camList = devices;
                        const cam = devices[activeCamIndex % devices.length];
                        dedicatedScanner = new Html5Qrcode("dedicated-scanner-reader");
                        const config = {
                            fps: 15,
                            qrbox: { width: 300, height: 170 },
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

                        dedicatedScanner.start(
                            cam.id,
                            config,
                            (decodedText) => {
                                if (!isProcessing && decodedText) {
                                    submitCode(decodedText);
                                }
                            },
                            () => {}
                        ).then(() => {
                            if (statusBadge) {
                                statusBadge.innerHTML = '<span class="badge bg-success bg-opacity-75 text-light px-3 py-1 rounded-pill small"><i class="ti ti-camera me-1"></i> Kamera Aktif (' + cam.label.slice(0, 20) + ')</span>';
                            }
                            if (laser) laser.style.display = 'block';
                        }).catch(err => {
                            console.warn("Camera start failed:", err);
                            if (statusBadge) {
                                statusBadge.innerHTML = '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill small"><i class="ti ti-alert-triangle me-1"></i> Kamera tidak aktif. Gunakan scanner barcode di bawah.</span>';
                            }
                        });
                    } else {
                        if (statusBadge) {
                            statusBadge.innerHTML = '<span class="badge bg-secondary text-light px-3 py-1 rounded-pill small"><i class="ti ti-camera-off me-1"></i> Tidak ada kamera terdeteksi. Gunakan scanner barcode.</span>';
                        }
                    }
                }).catch(() => {
                    if (statusBadge) {
                        statusBadge.innerHTML = '<span class="badge bg-secondary text-light px-3 py-1 rounded-pill small"><i class="ti ti-camera-off me-1"></i> Izin kamera belum diberikan.</span>';
                    }
                });
            }
        }

        startScanner();

        function submitCode(code) {
            code = String(code).trim();
            if (!code || isProcessing) return;

            isProcessing = true;
            const inputEl = document.getElementById('dedicated-barcode-input');
            const submitBtn = document.getElementById('btn-dedicated-submit');
            if (inputEl) inputEl.value = code;
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
                    scan_type: 'auto'
                }),
                success: function(res) {
                    playBeep(true);
                    const personName = res.student?.nama_siswa || res.person?.nama || 'Siswa / Guru';
                    const roleLabel = res.role === 'teacher' ? 'Guru / Pengajar' : ('Kelas: ' + (res.student?.kelas || '-'));
                    const photo = res.photo_url || 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';
                    const actTime = res.waktu_aktual || new Date().toLocaleTimeString('id-ID');
                    const statusText = res.status_kehadiran || 'Hadir';

                    // Update UI Card
                    const placeholder = document.getElementById('dedicated-result-placeholder');
                    const resCard = document.getElementById('dedicated-result-card');
                    if (placeholder) placeholder.style.display = 'none';
                    if (resCard) {
                        document.getElementById('dedicated-result-photo').src = photo;
                        document.getElementById('dedicated-result-name').textContent = personName;
                        document.getElementById('dedicated-result-role').textContent = roleLabel;
                        document.getElementById('dedicated-result-status').textContent = statusText.toUpperCase();
                        document.getElementById('dedicated-result-time').textContent = actTime;
                        document.getElementById('dedicated-result-message').textContent = res.message || 'Absensi berhasil dicatat';
                        document.getElementById('dedicated-result-wa').textContent = res.whatsapp || '';
                        resCard.style.display = 'block';
                    }

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
                                        <small class="text-muted">Jam: <b>${actTime}</b> | Status: <b>${statusText}</b></small>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true
                        });
                    }

                    if (inputEl) {
                        inputEl.value = '';
                        inputEl.focus();
                    }
                },
                error: function(err) {
                    playBeep(false);
                    const msg = err.responseJSON?.error || 'Gagal memproses barcode absensi.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Absensi Gagal',
                            text: msg,
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    } else {
                        alert(msg);
                    }
                    if (inputEl) {
                        inputEl.select();
                        inputEl.focus();
                    }
                },
                complete: function() {
                    setTimeout(() => {
                        isProcessing = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="ti ti-check me-1"></i> Absen Hadir';
                        }
                    }, 1200);
                }
            });
        }

        // Input enter key
        const inputEl = document.getElementById('dedicated-barcode-input');
        if (inputEl) {
            inputEl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitCode(this.value.trim());
                }
            });
        }

        // Submit button
        const submitBtn = document.getElementById('btn-dedicated-submit');
        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                const val = document.getElementById('dedicated-barcode-input')?.value.trim();
                if (val) submitCode(val);
                else document.getElementById('dedicated-barcode-input')?.focus();
            });
        }

        // Quick select
        const quickBtn = document.getElementById('btn-dedicated-quick-submit');
        if (quickBtn) {
            quickBtn.addEventListener('click', function() {
                const val = document.getElementById('dedicated-quick-select')?.value;
                if (val) submitCode(val);
                else alert('Silakan pilih nama siswa atau guru terlebih dahulu.');
            });
        }

        // Camera buttons
        const switchBtn = document.getElementById('btn-dedicated-switch-cam');
        if (switchBtn) {
            switchBtn.addEventListener('click', function() {
                if (camList.length > 1) {
                    activeCamIndex++;
                    startScanner();
                } else {
                    alert('Hanya satu kamera yang terdeteksi.');
                }
            });
        }

        const restartBtn = document.getElementById('btn-dedicated-restart-cam');
        if (restartBtn) {
            restartBtn.addEventListener('click', function() {
                startScanner();
            });
        }
    });
</script>
<?= $this->endSection(); ?>

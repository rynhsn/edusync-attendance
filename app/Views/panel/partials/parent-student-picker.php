<?php
$pickerId = $pickerId ?? 'studentPicker';
$selectedStudentIds = $selectedStudentIds ?? [];
?>
<div class="col-12">
    <label class="form-label" for="<?= esc($pickerId); ?>Search">Nama Siswa</label>
    <input class="form-control mb-2" id="<?= esc($pickerId); ?>Search" type="search" placeholder="Cari nama siswa, NIS, atau kelas...">
    <div class="border rounded p-2" id="<?= esc($pickerId); ?>Options" style="max-height: 260px; overflow-y: auto;">
        <?php foreach ($students as $student) : ?>
            <?php
            $studentId = (int) $student['siswa_id'];
            $searchText = mb_strtolower(implode(' ', [
                $student['nama_siswa'],
                $student['nis'] ?? '',
                $student['kelas_nama'] ?? '',
            ]));
            ?>
            <label class="form-check d-flex gap-2 py-1 mb-0" data-student-option data-search="<?= esc($searchText); ?>">
                <input class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="student_ids[]" value="<?= $studentId; ?>" <?= in_array($studentId, $selectedStudentIds, true) ? 'checked' : ''; ?>>
                <span class="form-check-label">
                    <span data-no-translate><?= esc($student['nama_siswa']); ?></span>
                    <span class="text-muted small" data-no-translate>
                        <?= ! empty($student['nis']) ? '· NIS ' . esc($student['nis']) : ''; ?>
                        <?= ! empty($student['kelas_nama']) ? '· ' . esc($student['kelas_nama']) : ''; ?>
                    </span>
                    <?php if (! empty($student['orang_tua_id'])) : ?>
                        <span class="badge bg-secondary-subtle text-secondary ms-1">Terhubung ke <span data-no-translate><?= esc($student['nama_orang_tua'] ?: 'orang tua lain'); ?></span></span>
                    <?php endif; ?>
                </span>
            </label>
        <?php endforeach; ?>
        <?php if ($students === []) : ?>
            <p class="text-muted small mb-0">Belum ada data siswa untuk dipilih.</p>
        <?php endif; ?>
        <p class="text-muted small mb-0 d-none" data-student-empty>Tidak ada siswa yang cocok dengan pencarian.</p>
    </div>
    <div class="form-text">Pilih satu atau beberapa siswa. Jika siswa yang dipilih masih terhubung ke orang tua lain, relasinya akan dipindahkan ke orang tua ini.</div>
</div>
<script {csp-script-nonce}>
(() => {
    const search = document.getElementById(<?= json_encode($pickerId . 'Search'); ?>);
    const options = document.getElementById(<?= json_encode($pickerId . 'Options'); ?>);
    if (!search || !options) return;
    const rows = [...options.querySelectorAll('[data-student-option]')];
    const empty = options.querySelector('[data-student-empty]');
    search.addEventListener('input', () => {
        const query = search.value.trim().toLocaleLowerCase();
        let visible = 0;
        rows.forEach((row) => {
            const matches = row.dataset.search.includes(query);
            row.classList.toggle('d-none', !matches);
            if (matches) visible++;
        });
        if (empty) empty.classList.toggle('d-none', visible > 0);
    });
})();
</script>

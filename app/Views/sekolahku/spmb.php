<?= $this->include('layouts/sekolahku/header') ?>

<section class="page-banner" id="main-content">
  <div class="page-banner-shape page-banner-shape--1"></div>
  <div class="page-banner-shape page-banner-shape--2"></div>
  <div class="container page-banner-content">
    <span class="page-banner-badge"><i class="fas fa-user-plus"></i> Pendaftaran Peserta Didik Baru</span>
    <h1>SPMB <span class="text-gradient">Online</span></h1>
    <p>Lengkapi data calon siswa dan unggah dokumen persyaratan untuk mendaftar.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (! empty($error)) : ?>
      <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>
    <?php if (! empty($errors)) : ?>
      <div class="alert alert-warning" role="alert">
        <strong>Masih ada data yang perlu diperiksa:</strong>
        <ul class="mb-0 mt-2">
          <?php foreach ($errors as $message) : ?><li><?= esc($message); ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="<?= site_url('news/spmb/submit'); ?>" method="post" enctype="multipart/form-data" class="contact-form" id="spmb-form">
      <?= csrf_field(); ?>
      <div style="position:absolute;left:-9999px" aria-hidden="true">
        <label for="spmb-website">Jangan isi kolom ini</label>
        <input id="spmb-website" type="text" name="website" tabindex="-1" autocomplete="off">
      </div>

      <fieldset class="spmb-section">
        <div class="spmb-section-heading">
          <span class="spmb-section-number">01</span>
          <div><legend>Akun dan Kontak</legend><p>Email dan nomor aktif digunakan panitia untuk menghubungi pendaftar.</p></div>
        </div>
        <div class="spmb-grid">
          <div class="form-row spmb-field-wide">
            <label for="contact-email">Email aktif</label>
            <input id="contact-email" name="contact_email" type="email" maxlength="254" autocomplete="email" placeholder="nama@email.com" required value="<?= esc($old['contact_email'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="contact-phone">Nomor HP / WhatsApp calon siswa</label>
            <input id="contact-phone" name="contact_phone" type="tel" maxlength="25" autocomplete="tel" placeholder="Contoh: 081234567890" required value="<?= esc($old['contact_phone'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="parent-phone">Nomor HP orang tua / wali</label>
            <input id="parent-phone" name="parent_phone" type="tel" maxlength="25" placeholder="Contoh: 081234567890" required value="<?= esc($old['parent_phone'] ?? ''); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset class="spmb-section">
        <div class="spmb-section-heading">
          <span class="spmb-section-number">02</span>
          <div><legend>Data Pribadi Calon Siswa</legend><p>Isi sesuai dokumen identitas resmi calon siswa.</p></div>
        </div>
        <div class="spmb-grid">
          <div class="form-row">
            <label for="student-nik">NIK (16 digit)</label>
            <input id="student-nik" name="student_nik" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" placeholder="Masukkan 16 digit NIK" required value="<?= esc($old['student_nik'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="student-nisn">NISN (10 digit)</label>
            <input id="student-nisn" name="student_nisn" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" placeholder="Masukkan 10 digit NISN" required value="<?= esc($old['student_nisn'] ?? ''); ?>">
          </div>
          <div class="form-row spmb-field-wide">
            <label for="student-name">Nama lengkap sesuai dokumen</label>
            <input id="student-name" name="student_name" maxlength="150" autocomplete="name" placeholder="Nama lengkap calon siswa" required value="<?= esc($old['student_name'] ?? ''); ?>">
          </div>
          <div class="form-row spmb-field-wide">
            <span class="spmb-field-label" id="gender-label">Jenis kelamin</span>
            <div class="spmb-gender-options" role="radiogroup" aria-labelledby="gender-label">
              <label class="spmb-choice">
                <input type="radio" name="gender" value="L" required <?= ($old['gender'] ?? '') === 'L' ? 'checked' : ''; ?>>
                <span><i class="fas fa-mars" aria-hidden="true"></i> Laki-laki</span>
              </label>
              <label class="spmb-choice">
                <input type="radio" name="gender" value="P" required <?= ($old['gender'] ?? '') === 'P' ? 'checked' : ''; ?>>
                <span><i class="fas fa-venus" aria-hidden="true"></i> Perempuan</span>
              </label>
            </div>
          </div>
          <div class="form-row">
            <label for="birth-place">Tempat lahir</label>
            <input id="birth-place" name="birth_place" maxlength="100" placeholder="Kota/kabupaten kelahiran" required value="<?= esc($old['birth_place'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="birth-date">Tanggal lahir</label>
            <input id="birth-date" name="birth_date" type="date" max="<?= date('Y-m-d'); ?>" required value="<?= esc($old['birth_date'] ?? ''); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset class="spmb-section">
        <div class="spmb-section-heading">
          <span class="spmb-section-number">03</span>
          <div><legend>Alamat dan Domisili</legend><p>Masukkan alamat tempat tinggal saat ini dengan lengkap.</p></div>
        </div>
        <div class="spmb-grid">
          <div class="form-row spmb-field-wide">
            <label for="address">Alamat lengkap rumah</label>
            <textarea id="address" name="address" rows="3" maxlength="1000" autocomplete="street-address" placeholder="Nama jalan, nomor rumah, dan patokan" required><?= esc($old['address'] ?? ''); ?></textarea>
          </div>
          <div class="form-row">
            <label for="rt">RT</label>
            <input id="rt" name="rt" inputmode="numeric" maxlength="3" placeholder="Contoh: 001" required value="<?= esc($old['rt'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="rw">RW</label>
            <input id="rw" name="rw" inputmode="numeric" maxlength="3" placeholder="Contoh: 002" required value="<?= esc($old['rw'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="postal-code">Kode pos</label>
            <input id="postal-code" name="postal_code" inputmode="numeric" pattern="[0-9]{5,6}" maxlength="6" placeholder="5–6 digit" required value="<?= esc($old['postal_code'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="province">Provinsi</label>
            <input id="province" name="province" maxlength="100" required value="<?= esc($old['province'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="regency">Kabupaten / Kota</label>
            <input id="regency" name="regency" maxlength="100" required value="<?= esc($old['regency'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="district">Kecamatan</label>
            <input id="district" name="district" maxlength="100" required value="<?= esc($old['district'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="village">Kelurahan / Desa</label>
            <input id="village" name="village" maxlength="100" required value="<?= esc($old['village'] ?? ''); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset class="spmb-section">
        <div class="spmb-section-heading">
          <span class="spmb-section-number">04</span>
          <div><legend>Data Sekolah Asal</legend><p>Informasi pendidikan terakhir calon siswa.</p></div>
        </div>
        <div class="spmb-grid">
          <div class="form-row spmb-field-wide">
            <label for="previous-school">Nama sekolah asal</label>
            <input id="previous-school" name="previous_school" maxlength="200" required value="<?= esc($old['previous_school'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="graduation-year">Tahun lulus</label>
            <input id="graduation-year" name="graduation_year" type="number" min="1990" max="<?= date('Y') + 1; ?>" required value="<?= esc($old['graduation_year'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="education-level">Jenjang pendidikan sekolah asal</label>
            <select id="education-level" name="education_level" required>
              <option value="">Pilih jenjang</option>
              <?php foreach (['SMP','SMK'] as $level) : ?>
                <option value="<?= esc($level); ?>" <?= ($old['education_level'] ?? '') === $level ? 'selected' : ''; ?>><?= esc($level); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset class="spmb-section">
        <div class="spmb-section-heading">
          <span class="spmb-section-number">05</span>
          <div><legend>Data Orang Tua / Wali</legend><p>Data penanggung jawab calon siswa.</p></div>
        </div>
        <div class="spmb-grid">
          <div class="form-row spmb-field-wide">
            <label for="parent-name">Nama lengkap orang tua / wali</label>
            <input id="parent-name" name="parent_name" maxlength="150" required value="<?= esc($old['parent_name'] ?? ''); ?>">
          </div>
          <div class="form-row">
            <label for="parent-relationship">Hubungan dengan calon siswa</label>
            <select id="parent-relationship" name="parent_relationship" required>
              <option value="">Pilih hubungan</option>
              <?php foreach (['ayah' => 'Ayah', 'ibu' => 'Ibu', 'wali' => 'Wali'] as $value => $label) : ?>
                <option value="<?= esc($value); ?>" <?= ($old['parent_relationship'] ?? '') === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-row">
            <label for="parent-job">Pekerjaan</label>
            <input id="parent-job" name="parent_job" maxlength="150" required value="<?= esc($old['parent_job'] ?? ''); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset class="spmb-section">
        <div class="spmb-section-heading">
          <span class="spmb-section-number">06</span>
          <div><legend>Dokumen Persyaratan</legend><p>Unggah scan/foto yang jelas dalam format PDF, JPEG, atau PNG. Maksimal 2 MB per berkas.</p></div>
        </div>
        <div class="spmb-grid">
          <?php foreach ($documentFields as $key => $document) : ?>
            <div class="form-row spmb-document" data-document="<?= esc($key); ?>">
              <label for="file-<?= esc($key); ?>"><?= esc($document['label']); ?><?= $key === 'recent_photo' ? ' (latar merah)' : ''; ?><?= $document['required'] ? ' *' : ''; ?></label>
              <input id="file-<?= esc($key); ?>" name="<?= esc($key); ?>" type="file" accept="<?= $document['image_only'] ? '.jpg,.jpeg,.png,image/jpeg,image/png' : '.pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png'; ?>" <?= $document['required'] ? 'required' : ''; ?>>
            </div>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <div class="spmb-declaration">
        <input id="declaration" type="checkbox" name="declaration" value="1" required <?= ($old['declaration'] ?? '') === '1' ? 'checked' : ''; ?>>
        <label for="declaration">Saya menyatakan semua data dan dokumen yang diberikan benar, serta bersedia mengikuti proses verifikasi panitia.</label>
      </div>
      <div class="spmb-submit">
        <p>Setelah pendaftaran berhasil disimpan, bukti pendaftaran akan diunduh dalam bentuk PDF. Simpan nomor pendaftaran untuk tindak lanjut.</p>
        <button type="submit" class="btn btn-primary btn-block" id="submit-spmb">Kirim Pendaftaran <i class="fas fa-paper-plane"></i></button>
      </div>
    </form>
  </div>
</section>

<?= $this->include('layouts/sekolahku/footer') ?>

<script>
(() => {
  const form = document.getElementById('spmb-form');
  const fileInputs = Object.fromEntries(Array.from(document.querySelectorAll('[data-document] input[type="file"]')).map(input => [input.name, input]));

  form.addEventListener('submit', event => {
    for (const input of Object.values(fileInputs)) {
      if (input.files[0] && input.files[0].size > 2 * 1024 * 1024) {
        event.preventDefault();
        input.focus();
        alert('Ukuran maksimal setiap dokumen adalah 2 MB.');
        return;
      }
    }

    window.setTimeout(() => form.reset(), 0);
  });

})();
</script>

<!-- <link rel="stylesheet" href="../../assets/vendors/fonts/fontawesome.css" /> -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi NFC</title>
    <!-- //font poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url() ?>assets/vendors/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendors/fonts/tabler-icons.css" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" /> -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendors/libs/sweetalert2/sweetalert2.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendors/libs/animate-css/animate.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6e7dff, #9e2fff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
            overflow: hidden;
        }

        /* Styling untuk box kiri dan kanan */
        .carousel-box {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 200px;
            /* Further increased width */
            height: 550px;
            /* Further increased height */
            background-color: rgba(255, 255, 255, 0.1);
            /* Adjusted transparency */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            border: 5px solid transparent;
        }

        .left-box {
            left: 0;
            /* Touching the side */
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-top-right-radius: 50px;
            /* Match main box */
            border-bottom-right-radius: 50px;
            /* Match main box */
        }

        .right-box {
            right: 0;
            /* Touching the side */
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-top-left-radius: 50px;
            /* Match main box */
            border-bottom-left-radius: 50px;
            /* Match main box */
        }

        .marquee-container {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: relative;
            /* background: rgba(0, 0, 0, 0.1); */
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-top-left-radius: 45px;
            border-bottom-left-radius: 45px;
            margin: 0;
            /* Remove margin */
            padding: 0;
            /* Remove padding */
        }

        .marquee-content {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .marquee-item {
            display: flex;
            justify-content: left;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            color: #ffffff;
            padding: 5px;
            margin-bottom: 5px;
            height: calc(100% / 10);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .marquee-item.in {
            background: linear-gradient(135deg, #56ab2f, #a8e063);
        }

        .marquee-item.out {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
        }

        /* Styling untuk kontainer waktu */
        .time-container {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 60px;
            text-align: center;
            width: 1000px;
            /* Extended width */
            backdrop-filter: blur(10px);
            animation: fadeIn 1s ease-in-out;
            position: relative;
            z-index: 3;
        }

        .time-container h1 {
            font-size: 4rem;
            font-weight: 700;
            margin: 0 0 30px 0;
            letter-spacing: 1px;
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .time-container h2 {
            font-size: 4rem;
            margin: 15px 0;
            font-weight: 600;
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 3;
        }

        .time-container p {
            font-size: 2rem;
            margin: 15px 0;
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 3;
        }

        .status-container {
            margin-top: 40px;
        }

        .status-message {
            font-size: 2rem;
            font-weight: 600;
            color: #ffeb3b;
            margin-bottom: 20px;
        }

        .icon {
            font-size: 80px;
            color: #ffffff;
            /* Updated to white */
        }

        .footer {
            position: absolute;
            bottom: 20px;
            font-size: 1.2rem;
            color: #fff;
        }

        .time-card {
            background-color: rgba(182, 182, 182, 0.1);
            box-shadow: 8px 8px 15px rgba(0, 0, 0, 0.17), -8px -8px 15px rgba(255, 255, 255, 0.15);
            padding: 15px 60px;
            border-radius: 20px;
            display: inline-block;
            margin: 10px;
        }

        .time-card.in {

            background: rgba(0, 255, 94, 0.3);
        }

        .time-card.out {
            background: rgba(255, 0, 0, 0.3);
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Abstract shapes behind the welcome box */
        .shape {
            position: absolute;
            backdrop-filter: blur(10px);
            border-radius: 50%;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            /* Increased shadow for 3D effect */
        }

        .shape1 {
            width: 200px;
            height: 200px;
            top: 30%;
            left: 20%;
            background: linear-gradient(135deg, rgba(255, 82, 47, 0.8), rgba(255, 193, 59, 0.8));
            /* Subtle Orange to Yellow gradient */
        }

        .shape2 {
            width: 300px;
            height: 300px;
            bottom: 30%;
            right: 20%;
            background: linear-gradient(135deg, rgba(255, 85, 47, 0.8), rgba(255, 219, 59, 0.8));
            /* Subtle Orange to Yellow gradient */
        }
    </style>
</head>

<body>
    <!-- Abstract shapes -->
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>

    <!-- Box Kiri -->
    <div class="carousel-box left-box"></div>

    <!-- Box Kanan -->
    <div class="carousel-box right-box">
        <div class="marquee-container">
            <div class="marquee-content" id="marqueeContent">
                <!-- Data marquee akan ditambahkan di sini -->
            </div>
        </div>
    </div>

    <div class="time-container">
        <h1 id="attendanceTitle">Selamat Datang</h1>

        <!-- Card for Time and Date -->
        <div class="time-card">
            <h2 id="currentTime">Loading...</h2>
            <p id="currentDate"></p>
        </div>

        <div class="status-container">
            <p class="status-message" id="attendanceStatus">Silakan tap NFC untuk absen</p>
            <i class="tf-icons ti ti-qrcode icon"></i>
        </div>
    </div>
    <footer class="footer">
        <p>&copy; <span id="currentYear"></span>, Edusync Ecosystem - Attendance</p>
    </footer>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="<?= base_url() ?>assets/vendors/libs/jquery/jquery.js"></script>
    <script src="<?= base_url() ?>assets/vendors/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/libs/sweetalert2/sweetalert2.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.marquee@1.6.1/jquery.marquee.min.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> -->


    <script>
        $(document).ready(function() {

            let now, hours, day, date, timeStart, timeEnd, currentTime;
            $('#currentYear').text(new Date().getFullYear());

            let rfid_tap = ''; // Tempat menyimpan kode kartu
            $(document).keypress(function(e) {
                if (e.key !== 'Enter') {
                    // Menambahkan karakter ke kode kartu
                    rfid_tap += e.key;
                } else {
                    // Ketika Enter ditekan, anggap input selesai
                    console.log('Kode Kartu:', rfid_tap); // Debugging: tampilkan kode kartu

                    // Kirim kode kartu ke server menggunakan ajax
                    // if (currentTime < timeStart || currentTime > timeEnd || freeDay.includes(day)) {
                    //     rfid_tap = '';
                    //     return;
                    // }

                    $.ajax({
                        url: '<?= base_url() ?>attendance/tap',
                        type: 'POST',
                        dataType: 'json',
                        data: JSON.stringify({
                            card_code: rfid_tap
                        }),
                        success: function(data) {
                            // console.log('Success:', data.message);
                            // Tampilkan pesan sukses menggunakan SweetAlert dengan durasi 2 detik gunakan countdown dengan isi foto, nama 

                            Swal.fire({
                                title: 'Berhasil',
                                html: `Absen berhasil untuk <b>${data.student.nama_siswa}</b>, kelas <b>${data.student.kelas}</b>`,
                                imageUrl: `<?= base_url() ?>assets/img/avatars/${data.student.foto}`,
                                imageWidth: 200,
                                imageAlt: "Custom image",
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                willOpen: function() {
                                    Swal.showLoading();
                                }
                            });

                            getHistory(); // Panggil getHistory saat berhasil absen
                        },
                        error: function(error) {
                            // console.error('Error:', error);
                            // Tampilkan pesan error menggunakan SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: error.responseJSON.error,
                                showConfirmButton: false,
                                timer: 1000,
                                timerProgressBar: true
                            });
                        }
                    });

                    // Reset cardCode after sending
                    rfid_tap = '';
                }
            });

            function initializeDateTime() {
                now = new Date();
                hours = now.getHours();
                const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                day = days[now.getDay()];
                date = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                currentTime = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                // Mengatur jam masuk dan jam keluar jam, menit, dan detik format time
                timeStart = '<?= $timeVar['time_start'] ?>';
                timeEnd = '<?= $timeVar['time_end'] ?>';
                timeLimit = '<?= $timeVar['time_limit'] ?>';
                freeDay = <?= json_encode($timeVar['free_day']) ?>;
            }

            function updateTime() {
                initializeDateTime(); // Update the global variables with the current date and time
                // console.log(timeStart, timeEnd, timeLimit, freeDay);

                // Mengambil jam, menit, dan detik
                let currentHours = now.getHours();
                let currentMinutes = now.getMinutes();
                let currentSeconds = now.getSeconds();

                // Menambahkan 0 jika angka kurang dari 10
                currentHours = currentHours < 10 ? '0' + currentHours : currentHours;
                currentMinutes = currentMinutes < 10 ? '0' + currentMinutes : currentMinutes;
                currentSeconds = currentSeconds < 10 ? '0' + currentSeconds : currentSeconds;

                // Menyusun waktu dalam format HH:MM:SS
                const time = `${currentHours}:${currentMinutes}:${currentSeconds}`;

                $('#currentTime').text(time);
                $('#currentDate').text(date);

                const titleElement = $('#attendanceTitle');
                const timeElement = $('.time-card');

                // cek hari jika tidak ada di array freeDay maka tampilkan pesan
                if (currentTime > timeStart && currentTime < timeLimit && !freeDay.includes(day)) {
                    // console.log('masuk', currentTime, timeStart, timeLimit, freeDay, day);
                    titleElement.text('Selamat Datang');
                    timeElement.removeClass('out').addClass('in');
                    $('#attendanceStatus').text('Silakan tap NFC untuk absen masuk');
                    $('.icon').removeClass('ti-qrcode-off').addClass('ti-qrcode');
                } else if (currentTime < timeEnd && currentTime > timeLimit && !freeDay.includes(day)) {
                    // console.log('pulang', currentTime, timeStart, timeLimit, freeDay, day);
                    titleElement.text('Selamat Jalan');
                    timeElement.removeClass('in').addClass('out');
                } else if (currentTime < timeStart || currentTime > timeEnd || freeDay.includes(day)) {
                    // console.log('libur', currentTime, timeStart, timeLimit, freeDay, day);
                    titleElement.text('Diluar Jam Operasional');
                    timeElement.removeClass('in').removeClass('out');
                    $('#attendanceStatus').text('Tidak ada absensi diluar jam operasional');
                    $('.icon').removeClass('ti-qrcode').addClass('ti-qrcode-off');
                }
            }

            function getHistory() {
                $.ajax({
                    url: '<?= base_url() ?>attendance/show',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        const marqueeContent = $('#marqueeContent');
                        marqueeContent.empty(); // Kosongkan konten marquee sebelum menambahkan data baru

                        data.attendanceHistory.forEach(item => {
                            const marqueeItem = `<div class="marquee-item ${item.waktu_pulang_aktual === null ? 'in' : 'out'}">` +
                                `<img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" alt="Foto Profil" style="width: 30px; border-radius: 50%; margin-right: 10px;">` +
                                `${item.nama_siswa}` +
                                `</div>`;
                            marqueeContent.append(marqueeItem);
                        });

                        // Inisialisasi marquee
                        $('#marqueeContent').marquee({
                            duration: 30000, // Durasi scroll dalam milidetik
                            gap: 0,
                            delayBeforeStart: 0,
                            direction: 'up',
                            duplicated: false
                        });
                    },
                    error: function(error) {
                        console.error('Error fetching history data:', error);
                    }
                });
            }

            setInterval(updateTime, 1000);
            initializeDateTime(); // Initialize the global variables when the DOM is loaded
            getHistory(); // Panggil getHistory saat halaman dimuat

            // Tambahkan event listener untuk tombol F2
            $(document).keydown(function(e) {
                if (e.key === 'F2') {
                    window.location.href = '<?= base_url('panel') ?>';
                }
            });

        });
    </script>
</body>

</html>
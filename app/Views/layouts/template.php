<!doctype html>

<html
    lang="en"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="<?= base_url(); ?>assets/"
    data-template="vertical-menu-template-no-customizer-starter">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= $title ?></title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url(); ?>assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/fonts/tabler-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/demo.css" />

    <!-- jquery CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.css" /> -->

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/flatpickr/flatpickr.css" />

    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/sweetalert2/sweetalert2.css">
    <!-- <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/animate-css/animate.css"> -->
    <!-- Row Group CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <!-- Form Validation -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/libs/@form-validation/form-validation.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="<?= base_url(); ?>assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= base_url(); ?>assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?= $this->include('layouts/menu'); ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav
                    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti ti-menu-2 ti-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?= base_url(); ?>assets/img/avatars/1.png" alt class="h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?= base_url(); ?>assets/img/avatars/1.png" alt class="h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-medium d-block">John Doe</span>
                                                    <small class="text-muted">Admin</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="ti ti-user-check me-2 ti-sm"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="ti ti-settings me-2 ti-sm"></i>
                                            <span class="align-middle">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <span class="d-flex align-items-center align-middle">
                                                <i class="flex-shrink-0 ti ti-credit-card me-2 ti-sm"></i>
                                                <span class="flex-grow-1 align-middle">Billing</span>
                                                <span class="flex-shrink-0 badge badge-center rounded-pill bg-label-danger w-px-20 h-px-20">2</span>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="ti ti-logout me-2 ti-sm"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                    <?= $this->renderSection('content') ?>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                                <div>
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    , made with ❤️ by
                                    <a href="https://github.com/rynhsn" target="_blank" class="footer-link text-primary fw-medium">
                                        Edusync
                                    </a> Ecosystem - Attendance
                                </div>
                                <div class="d-none d-lg-inline-block">
                                    <a
                                        href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation/"
                                        target="_blank"
                                        class="footer-link me-4">Documentation</a>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <!-- datatables -->
    <script src="<?= base_url(); ?>assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/popper/popper.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/js/bootstrap.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/hammer/hammer.js"></script>

    <script src="<?= base_url(); ?>assets/vendor/libs/i18n/i18n.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="<?= base_url(); ?>assets/vendor/js/menu.js"></script>


    <!-- Flat Picker -->
    <script src="<?= base_url(); ?>assets/vendor/libs/moment/moment.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/flatpickr/flatpickr.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="<?= base_url(); ?>assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?= base_url(); ?>assets/js/cards-actions.js"></script>


    <!-- Form Validation -->
    <script src="<?= base_url(); ?>assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/@form-validation/auto-focus.js"></script>

    <!-- Main JS -->
    <script src="<?= base_url(); ?>assets/js/main.js"></script>

    <!-- Page JS -->
    <!-- sweet alert -->
    <script src="<?= base_url(); ?>assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <?php if ($activePage == 'class'): ?>
        <script src="<?= base_url(); ?>assets/main/tables-datatables-kelas.js"></script>
    <?php elseif ($activePage == 'major'): ?>
        <script src="<?= base_url(); ?>assets/main/tables-datatables-jurusan.js"></script>
    <?php elseif ($activePage == 'timesetting'): ?>
        <script src="<?= base_url(); ?>assets/main/tables-konfigurasi-waktu.js"></script>
    <?php elseif ($activePage == 'student'): ?>
        <script src="<?= base_url(); ?>assets/main/tables-datatables-siswa.js"></script>
    <?php endif; ?>

    <script>
        // Tambahkan event listener untuk tombol F2
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F2') {
                window.location.href = '<?= base_url('') ?>';
            }
        });
    </script>
</body>

</html>
<?= $this->include('layouts/panel/header'); ?>
<?= $this->include('layouts/panel/topbar'); ?>
<?= $this->include('layouts/panel/sidebar'); ?>

<main id="main-content" class="admin-main">
    <?= $this->renderSection('content'); ?>
</main>

<?= $this->include('layouts/panel/footer'); ?>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="toast-container"></div>
    </div>

    <?= $this->renderSection('modals'); ?>
    <?= $this->renderSection('scripts'); ?>
    <script src="<?= base_url('assets/main/panel-table-pagination.js'); ?>?v=<?= filemtime(FCPATH . 'assets/main/panel-table-pagination.js'); ?>" defer></script>
    <script src="<?= base_url('assets/main/panel-sidebar.js'); ?>?v=<?= filemtime(FCPATH . 'assets/main/panel-sidebar.js'); ?>" defer></script>
</body>
</html>
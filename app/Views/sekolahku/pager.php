<?php

use CodeIgniter\Pager\PagerRenderer;

/** @var PagerRenderer $pager */
$pager->setSurroundCount(2);
?>

<?php if ($pager->links() !== []): ?>
<div class="news-pagination">
  <?php if ($pager->hasPrevious()): ?>
    <a class="np-btn" href="<?= $pager->getFirst() ?>" aria-label="Pertama">&laquo;</a>
    <a class="np-btn" href="<?= $pager->getPrevious() ?>" aria-label="Sebelumnya">&lsaquo;</a>
  <?php endif ?>

  <?php foreach ($pager->links() as $link): ?>
    <a class="np-btn <?= $link['active'] ? 'active' : '' ?>" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
  <?php endforeach ?>

  <?php if ($pager->hasNext()): ?>
    <a class="np-btn" href="<?= $pager->getNext() ?>" aria-label="Selanjutnya">&rsaquo;</a>
    <a class="np-btn" href="<?= $pager->getLast() ?>" aria-label="Terakhir">&raquo;</a>
  <?php endif ?>
</div>
<?php endif ?>

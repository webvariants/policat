<?php
/* @var $pager policatPager */
if ($pager->getLastPage() > 1):
  $ajax = $pager->getAjax();
  if ($ajax)
    $ajax = 'ajax_link';
  ?>
  <nav ><?php $last = 0; ?>
    <ul class="pagination justify-content-center">
      <?php if (isset($title)): ?><li class="page-item disabled"><span class="page-link"><?php echo $title ?></span></li><?php endif ?>
      <?php if ($pager->getPrev()): ?>
        <li class="page-item"><a class="page-link <?php echo $ajax ?>" title="previous" href="<?php echo $pager->getUrl($pager->getPrev()) ?>">&laquo;</a></li>
      <?php else: ?>
        <li class="page-item disabled"><a class="page-link">&laquo;</a></li>
      <?php endif ?>
      <?php foreach ($pager->getPageNumbers() as $number): ?>
        <?php if ($last + 1 != $number): ?><li class="disabled"><a class="page-link">&hellip;</a></li><?php endif ?>
        <?php if ($pager->getPage() == $number): ?>
          <li class="page-item active"><a class="page-link "><?php echo $number ?></a></li>
        <?php else: ?>
          <li class="page-item"><a class="page-link <?php echo $ajax ?>" href="<?php echo $pager->getUrl($number) ?>"><?php echo $number ?></a></li>
        <?php endif ?>
        <?php $last = $number ?>
      <?php endforeach; ?>
      <?php if ($pager->getNext()): ?>
        <li class="page-item"><a class="page-link <?php echo $ajax ?>" title="next" href="<?php echo $pager->getUrl($pager->getNext()) ?>">&raquo;</a></li>
      <?php else: ?>
        <li class="page-item disabled"><a class="page-link">&raquo;</a></li>
      <?php endif ?>
      <?php if (isset($show_count)): ?><li class="page-item disabled"><span class="page-link"><?php echo $pager->count() ?> items</span></li><?php endif ?>
    </ul>
  </nav>
  <?php
 endif ?>

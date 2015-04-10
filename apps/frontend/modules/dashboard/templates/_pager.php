<?php
/* @var $pager policatPager */
if ($pager->getLastPage() > 1):
  $ajax = $pager->getAjax();
  if ($ajax)
    $ajax = 'ajax_link';
  ?>
  <div class="pagination"><?php $last = 0; ?>
    <ul>
      <?php if (isset($title)): ?><span class="title"><?php echo $title ?></span><?php endif ?>
      <?php if ($pager->getPrev()): ?>
        <li><a class="<?php echo $ajax ?>" title="previous" href="<?php echo $pager->getUrl($pager->getPrev()) ?>">&laquo;</a></li>
      <?php else: ?>
        <li class="disabled"><a>&laquo;</a></li>
      <?php endif ?>
      <?php foreach ($pager->getPageNumbers() as $number): ?>
        <?php if ($last + 1 != $number): ?><li class="disabled"><a>&hellip;</a></li><?php endif ?>
        <?php if ($pager->getPage() == $number): ?>
          <li class="active"><a><?php echo $number ?></a></li>
        <?php else: ?>
          <li><a class="<?php echo $ajax ?>" href="<?php echo $pager->getUrl($number) ?>"><?php echo $number ?></a></li>
        <?php endif ?>
        <?php $last = $number ?>
      <?php endforeach; ?>
      <?php if ($pager->getNext()): ?>
        <li><a class="<?php echo $ajax ?>" title="next" href="<?php echo $pager->getUrl($pager->getNext()) ?>">&raquo;</a></li>
      <?php else: ?>
        <li class="disabled"><a>&raquo;</a></li>
      <?php endif ?>
      <?php if (isset($show_count)): ?><span class="">(<?php echo $pager->count() ?> items)</span><?php endif ?>
    </ul>
  </div>
  <?php

 endif ?>
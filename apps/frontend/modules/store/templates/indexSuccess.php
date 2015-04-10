<?php include_partial('dashboard/admin_tabs', array('active' => 'store')) ?>
<div class="row">
  <div class="span2">
    <ul class="nav nav-tabs nav-stacked">
      <?php foreach ($list as $key => $entry): ?>
        <?php if (isset($entry['i18n'])): ?>
          <li><a href="<?php echo url_for('store_language', array('key' => $key)) ?>"><?php echo $entry['name'] ?></a></li>
        <?php else: ?>
          <li><a href="<?php echo url_for('store_edit', array('key' => $key)) ?>"><?php echo $entry['name'] ?></a></li>
        <?php endif ?>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
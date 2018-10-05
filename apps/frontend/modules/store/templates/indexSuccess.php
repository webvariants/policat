<?php include_partial('dashboard/admin_tabs', array('active' => 'store')) ?>
<div class="row">
  <div class="col-2">
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <?php foreach ($list as $key => $entry): ?>
        <?php if (isset($entry['i18n'])): ?>
          <a class="nav-link" href="<?php echo url_for('store_language', array('key' => $key)) ?>"><?php echo $entry['name'] ?></a>
        <?php else: ?>
          <a class="nav-link" href="<?php echo url_for('store_edit', array('key' => $key)) ?>"><?php echo $entry['name'] ?></a>
        <?php endif ?>
      <?php endforeach; ?>
    </div>
  </div>
</div>

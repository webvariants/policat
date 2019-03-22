<?php /* @var $form StoreForm */ ?>
<?php include_partial('dashboard/admin_tabs', array('active' => 'store', 'extra_title' => $title)) ?>
<div class="tabbable tabs-left row">
  <div class="col-md-2">
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <?php foreach ($list as $key_i => $entry): ?>
        <?php if (isset($entry['i18n'])): ?>
          <a class="nav-link <?php if ($key == $key_i): ?>active<?php endif ?>" href="<?php echo url_for('store_language', array('key' => $key_i)) ?>"><?php echo $entry['name'] ?></a>
        <?php else: ?>
          <a class="nav-link <?php if ($key == $key_i): ?>active<?php endif ?>" href="<?php echo url_for('store_edit', array('key' => $key_i)) ?>"><?php echo $entry['name'] ?></a>
        <?php endif ?>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="col-md-10">
    <h3>Select a language</h3>
    <ul class="nav nav-tabs">
      <?php foreach ($languages as $language): /* @var $language Language */ ?>
        <li class="nav-item">
          <?php if ($language->getId() == 'en'): ?><b><?php endif ?>
          <a class="nav-link" href="<?php echo url_for('store_edit_language', array('key' => $key, 'language' => $language->getId())) ?>"><?php echo $language->getName() ?></a>
          <?php if ($language->getId() == 'en'): ?></b><?php endif ?>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
</div>

<?php /* @var $form StoreForm */ ?>
<?php include_partial('dashboard/admin_tabs', array('active' => 'store', 'extra_title' => $title)) ?>
<div class="tabbable tabs-left row">
  <div class="span2">
    <ul class="nav nav-tabs" style="width:100%">
      <?php foreach ($list as $key_i => $entry): ?>
        <?php if (isset($entry['i18n'])): ?>
          <li<?php if ($key == $key_i): ?> class="active"<?php endif ?>><a href="<?php echo url_for('store_language', array('key' => $key_i)) ?>"><?php echo $entry['name'] ?></a></li>
        <?php else: ?>
          <li<?php if ($key == $key_i): ?> class="active"<?php endif ?>><a href="<?php echo url_for('store_edit', array('key' => $key_i)) ?>"><?php echo $entry['name'] ?></a></li>
        <?php endif ?>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="span10">
    <h2>Settings</h2>
    <h3>Select a language</h3>
    <ul>
      <?php foreach ($languages as $language): /* @var $language Language */ ?>
        <li>
          <?php if ($language->getId() == 'en'): ?><b><?php endif ?>
          <a href="<?php echo url_for('store_edit_language', array('key' => $key, 'language' => $language->getId())) ?>"><?php echo $language->getName() ?></a>
          <?php if ($language->getId() == 'en'): ?></b><?php endif ?>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
</div>

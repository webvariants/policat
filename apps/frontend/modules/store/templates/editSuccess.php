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
    <?php if (isset($language)): ?><h3>Language: <?php echo $language->getName() ?></h3><?php endif ?>
    <?php $action = isset($language) ? url_for('store_edit_language', array('key' => $key, 'language' => $language->getId())) : url_for('store_edit', array('key' => $key)) ?>
    <form class="ajax_form form-horizontal" action="<?php echo $action ?>" method="post"<?php if ($form->isMultipart()): ?> enctype="multipart/form-data"<?php endif ?>>
      <?php echo $form ?>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn" href="<?php echo isset($language) ? url_for('store_language', array('key' => $key)) : url_for('store') ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>
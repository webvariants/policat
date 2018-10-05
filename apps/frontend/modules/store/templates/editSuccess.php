<?php /* @var $form StoreForm */ ?>
<?php include_partial('dashboard/admin_tabs', array('active' => 'store', 'extra_title' => $title)) ?>
<div class="tabbable tabs-left row">
  <div class="col-2">
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
  <div class="col-10">
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

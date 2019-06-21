<?php include_partial('dashboard/admin_tabs', array('active' => 'languages')) ?>
<form class="ajax_form form-horizontal" action="<?php echo $form->getObject()->isNew() ? url_for('language_new') : url_for('language_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
  <?php echo $form ?>
  <div class="form-actions">
    <button class="btn btn-primary">Save</button>
    <a class="btn btn-secondary" href="<?php echo url_for('language_index') ?>" >Cancel</a>
  </div>
</form>
<?php if ($download || $csrf_token): ?>
  <h2>Language files</h2>
  <div class="row">
    <?php if ($download): ?>
      <div class="col-md-4">
        <div class="card bg-light mb-3">
          <div class="card-body">
            <a class="btn btn-primary" href="<?php echo url_for('language_download', array('id' => $form->getObject()->getId())) ?>">Download</a>
          </div>
        </div>
      </div>
    <?php endif ?>
    <?php if ($csrf_token): ?>
      <div class="col-md-4">
        <div class="card bg-light mb-3" id="upload">
          <div class="card-body">
          <form class="form-inline ajax_form" method="post" action="<?php echo url_for('language_upload', array('id' => $form->getObject()->getId())) ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>" />
            <input type="file" name="file" />
            <br />
            <button class="mt-2 btn btn-secondary" type="submit">Upload</button>
          </form>
          </div>
        </div>
      </div>
    <?php endif ?>
  </div>
<?php endif ?>

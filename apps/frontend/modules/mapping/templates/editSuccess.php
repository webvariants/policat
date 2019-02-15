<?php include_partial('dashboard/admin_tabs', array('active' => 'mappings')) ?>
<h2>Settings</h2>
<form class="ajax_form form-horizontal" action="<?php echo $form->getObject()->isNew() ? url_for('mapping_new') : url_for('mapping_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
  <?php echo $form ?>
  <div class="form-actions">
    <button class="btn btn-primary">Save</button>
    <a class="btn btn-secondary" href="<?php echo url_for('mapping_index') ?>" >Cancel</a>
  </div>
</form>
<?php
if (isset($pairs))
  include_partial('pairs', array('pairs' => $pairs, 'mapping' => $mapping));
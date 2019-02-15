<?php include_partial('dashboard/admin_tabs', array('active' => 'country')) ?>
<form class="ajax_form form-horizontal" action="<?php echo $form->getObject()->isNew() ? url_for('country_new') : url_for('country_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
    <?php echo $form ?>
    <div class="form-actions">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-secondary" href="<?php echo url_for('country_index') ?>" >Cancel</a>
    </div>
</form>
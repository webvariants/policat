<?php include_partial('dashboard/admin_tabs', array('active' => 'tax')) ?>
<h2><?php if ($form->getObject()->isNew()): ?>New<?php else: ?>Edit<?php endif ?> note</h2>
<form class="ajax_form form-horizontal" action="<?php echo $form->getObject()->isNew() ? url_for('tax_note_new') : url_for('tax_note_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
    <?php echo $form ?>
    <div class="form-actions">
        <button class="btn btn-primary">Save</button>
        <a class="btn" href="<?php echo url_for('tax_list') ?>">Cancel</a>
    </div>
</form>
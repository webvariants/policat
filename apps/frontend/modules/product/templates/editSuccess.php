<?php include_partial('dashboard/admin_tabs', array('active' => 'product')) ?>
<h2><?php if ($form->getObject()->isNew()): ?>New<?php else: ?>Edit<?php endif ?> product</h2>
<form class="ajax_form form-horizontal" action="<?php echo $form->getObject()->isNew() ? url_for('product_new') : url_for('product_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
    <?php echo $form ?>
    <div class="form-actions">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-danger" href="<?php echo url_for('product_index') ?>" >Cancel</a>
    </div>
</form>

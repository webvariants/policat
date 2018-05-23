<?php $petition = $form->getObject() ?>
<form id="petition_token_addnum_form" class="ajax_form form-horizontal" action="<?php echo url_for('petition_token_addnum', array('id' => $form->getObject()->getId())) ?>" method="post">
    <fieldset>
        <?php echo $form->renderRows('addnum', 'target_num', '*addnum_email_counter', '*target_num_email_counter') ?>
    </fieldset>
    <?php echo $form->renderHiddenFields() ?>
    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
    </div>
</form>

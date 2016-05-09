<form id="widget_edit_form" class="ajax_form form-horizontal" action="<?php echo $form->getObject()->isNew() ? url_for('widget_create', array('id' => $petition->getId())) : url_for('widget_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
  <?php if (isset($lang)): ?><input type="hidden" name="lang" value="<?php echo $lang ?>"/><?php endif ?>
  <?php echo $form->renderHiddenFields() ?>
  <?php echo $form->renderRows('status', '*title', '*target') ?>
  <?php
  if ($petition->isEmailKind()) {
    if ($petition->getKind() != Petition::KIND_PLEDGE) {
      echo $form->renderRows('*email_subject', '*email_body');
    }
  } else {
    echo $form->renderRows('*intro', '*footer');
  }
  echo $form->renderRows('*background');
  ?>
  <div class="row">
    <div class="span6"><?php echo $form->renderRows('styling_type', 'styling_width', '*styling_title_color', '*styling_body_color') ?></div>
    <div class="span6"><?php echo $form->renderRows('*styling_button_color', '*styling_bg_left_color', '*styling_bg_right_color', '*styling_form_title_color') ?></div>
  </div>
  <?php echo $form->renderRows('paypal_email', '*donate_url', '*donate_text', 'landing_url') ?>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Save</button>
    <button class="btn submit" data-submit='{"preview":1}' type="submit">Save &amp; view</button>
    <?php if (!$form->getObject()->isNew()): ?>
    <a class="btn ajax_link" href="<?php echo url_for('widget_view', array('id' => $form->getObject()->getId())) ?>">view</a>
    <?php endif ?>
    <a class="btn" href="<?php echo url_for('petition_widgets', array('id' => $petition->getId())) ?>">Cancel</a>
  </div>
</form>
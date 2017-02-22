<?php /* @var $form EditWidgetForm */ ?>
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
      echo $form->renderRows('*intro');
      ?>
      <div class="control-group">
          <label class="control-label">Main part</label>
          <div class="controls">
              <?php $srcdoc = '<style>body { font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 14px; }</style>' . UtilMarkdown::transformMedia($form->getObject()->getPetitionText()->getBody(), $sf_data->getRaw('petition')) ?>
              <iframe style="width: 100%; height: 300px; border:1px solid #ccc;" srcdoc="<?php echo Util::enc($srcdoc) ?>"></iframe>
          </div>
      </div>
      <?php
      echo $form->renderRows('*footer');
    }
    echo $form->renderRows('*background', 'styling_type', 'styling_width', '*themeId', '*styling_font_family');
    ?>
    <div class="row">
        <div class="span6"><?php echo $form->renderRows('*styling_bg_right_color', '*styling_bg_left_color', '*styling_button_primary_color', '*styling_button_color') ?></div>
        <div class="span6"><?php echo $form->renderRows('*styling_title_color', '*styling_form_title_color', '*styling_body_color', '*styling_label_color') ?></div>
    </div>
    <?php echo $form->renderRows('share', 'paypal_email', '*donate_url', '*donate_text', 'landing_url') ?>
    <?php if (isset($form['target_selector_1'])): ?>
      <legend>Preselect Targets</legend>
      <div class="row">
          <div class="span6"><?php echo $form->renderRows(array('target_selector_1', 'target_selector_2*')) ?></div>
          <div class="span6"><?php UtilTargetSelectorPreselect::printTextPreselection($form->getObject()->getPetitionText(), '<div class="alert alert-info">If you select nothing settings from the translation will be used.<br /> %s</div>') ?></div>
      </div>
    <?php endif ?>
    <div class="form-actions">
        <button accesskey="s" title="[Accesskey] + S" class="btn btn-primary" type="submit">Save</button>
        <button class="btn submit" data-submit='{"preview":1}' type="submit">Save &amp; view</button>
        <?php if (!$form->getObject()->isNew()): ?>
          <a class="btn ajax_link" href="<?php echo url_for('widget_view', array('id' => $form->getObject()->getId())) ?>">view</a>
        <?php endif ?>
        <a class="btn" href="<?php echo url_for('petition_widgets', array('id' => $petition->getId())) ?>">Cancel</a>
    </div>
</form>
<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('petition_translations', array('id' => $petition->getId())) ?>">Translations</a></li><span class="divider">/</span>
  <li class="active"><?php echo $translation->getLanguage() ?></li>
</ul>
<?php include_partial('tabs', array('petition' => $petition, 'active' => 'translations')) ?>
<h3><?php echo $translation->getLanguage() ?></h3>
<form class="ajax_form form-horizontal<?php if ($form->getObject()->isNew()): ?> change_onload<?php endif ?>" action="<?php echo $form->getObject()->isNew() ? url_for('translation_create', array('id' => $petition->getId())) : url_for('translation_edit', array('id' => $translation->getId())) ?>" method="post">
  <fieldset>
    <?php echo $form->renderHiddenFields(); ?>

    <legend>Settings</legend>
    <?php echo $form->renderRows(array('*language_id', 'status', 'landing_url', '*widget_id')) ?>

    <legend>Widget texts</legend>
    <?php echo $form->renderRows(array('*title', '*target', '*background')) ?>
    <?php if ($petition->getKind() != Petition::KIND_PLEDGE): ?>
      <?php echo $form->renderRows(array('*intro')) ?>
    <?php endif ?>
    <?php echo $form->renderRows(array('*body', '*footer')) ?>

    <?php if ($petition->getKind() == Petition::KIND_PLEDGE): ?>
      <legend>Pledge Page</legend>
      <?php echo $form->renderRows(array('*pledge_title', '*intro', '*pledge_comment', '*pledge_explantory_annotation', '*pledge_thank_you')) ?>
      <div id="pledges">
        <?php echo $form->renderRows(array('pledge_*')) ?>
      </div>
    <?php endif ?>

    <legend>Emails to participants</legend>
    <?php echo $form->renderRows(array('*email_subject', '*email_body', '*email_validation_subject', '*email_validation_body', '*email_tellyour_subject', '*email_tellyour_body')) ?>

    <legend>Privacy Policy</legend>
    <?php echo $form->renderRows(array('privacy_policy_body')) ?>

    <?php
    $other_rows = $form->renderOtherRows();
    if ($other_rows):
      ?>
      <legend>Other</legend>
      <?php echo $other_rows ?>
    <?php endif;
    ?>
  </fieldset>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn submit" data-submit='{"go_translation":1}'>Save &amp; and create new widget from this translation</a>
    <a class="btn" href="<?php echo url_for('petition_translations', array('id' => $petition->getId())) ?>">Cancel</a>
  </div>
</form>
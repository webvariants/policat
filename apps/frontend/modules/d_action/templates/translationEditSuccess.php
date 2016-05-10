<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('petition_translations', array('id' => $petition->getId())) ?>">Translations</a></li><span class="divider">/</span>
  <li class="active"><?php echo $translation->getLanguage() ?></li>
</ul>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('tabs', array('petition' => $petition, 'active' => 'translations')) ?>
<h3><?php echo $translation->getLanguage() ?></h3>
<form class="ajax_form form-horizontal<?php if ($form->getObject()->isNew()): ?> change_onload<?php endif ?>" action="<?php echo $form->getObject()->isNew() ? url_for('translation_create', array('id' => $petition->getId())) : url_for('translation_edit', array('id' => $translation->getId())) ?>" method="post">
  <fieldset>
    <?php echo $form->renderHiddenFields(); ?>

    <legend>Settings</legend>
    <?php echo $form->renderRows(array('*language_id', 'status', 'landing_url', '*widget_id')) ?>

    <legend>Widget texts</legend>
    <?php echo $form->renderRows(array('*title', '*target')) ?>
    <?php if ($petition->getKind() != Petition::KIND_PLEDGE): ?>
      <?php echo $form->renderRows(array('*intro')) ?>
    <?php endif ?>
    <?php echo $form->renderRows(array('*body', '*footer', '*email_subject', '*email_body', '*background')) ?>
    <?php echo $form->renderRows(array('*label_extra1', '*placeholder_extra1')) ?>

    <?php if ($petition->getKind() == Petition::KIND_PLEDGE): ?>
      <legend>Pledge Page</legend>
      <?php echo $form->renderRows(array('*pledge_title', '*intro', '*pledge_comment', '*pledge_explantory_annotation', '*pledge_thank_you')) ?>
      <div id="pledges">
        <?php echo $form->renderRows(array('pledge_*')) ?>
      </div>
    <?php endif ?>

    <legend>E-mails to participants</legend>
    <?php echo $form->renderRows(array('*email_validation_subject', '*email_validation_body')) ?>
    <div class="control-group">
      <div class="controls">
        <p class="help-block">Note: #DISCONFIRMATION-URL# adds a link for participants to revoke their participation and delete their data. Make sure you include this, as it is your legal obligation to allow participants to unsubscribe easily, and to allow those who think they didn't participate willingly, to have their data deleted.</p>
      </div>
    </div>
    <?php echo $form->renderRows(array('*email_tellyour_subject', '*email_tellyour_body', '*thank_you_email_subject', '*thank_you_email_body')) ?>
    
    <legend>Privacy Policy</legend>
    <?php echo $form->renderRows(array('privacy_policy_body')) ?>
    
    <?php if ($petition->getDonateUrl()): ?>
    <legend>Donate</legend>
    <div class="control-group">
        <label class="control-label">Action settings</label>
        <div class="controls well">
          Donate link: <?php echo $petition->getDonateUrl() ?>
        </div>
    </div>
    <?php echo $form->renderRows(array('donate_url', 'donate_text')) ?>
    <?php endif ?>

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
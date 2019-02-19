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
        <?php echo $form->renderRows(array('*language_id', 'status', 'read_more_url', 'landing_url')) ?>
        <?php if (isset($topLanguages)): ?>
          <div class="select-order" data-options="<?php
          echo Util::enc(json_encode(array(
              'keys' => $topLanguages->getRawValue(),
              'target' => '#translation_language_id',
              'selectFirst' => true
          )))
          ?>"></div>
             <?php endif ?>

        <legend>Widget texts</legend>
        <?php echo $form->renderRows(array('*form_title', '*title', '*target')) ?>
        <?php if ($petition->getKind() != Petition::KIND_PLEDGE): ?>
          <?php echo $form->renderRows(array('*intro')) ?>
        <?php endif ?>
        <?php echo $form->renderRows(array('*body', '*footer', '*email_subject', '*email_body', '*background')) ?>
        <?php echo $form->renderRows(array('*label_extra1', '*placeholder_extra1', '*subscribe_text', '*social_share_text')) ?>

        <?php if (($petition->getKind() == Petition::KIND_PLEDGE) && $petition->getDigestEnabled()): ?>
          <legend>Digest E-mail</legend>
          <?php echo $form->renderRows(array('digest_subject', 'digest_body_intro', 'digest_body_outro')) ?>
        <?php endif ?>

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
                Note: #DISCONFIRMATION-URL# adds a link for participants to revoke their participation and delete their data. Make sure you include this to allow those who think they didn't participate willingly, to have their data deleted.
            </div>
        </div>
        <?php echo $form->renderRows(array('*email_tellyour_subject', '*email_tellyour_body')) ?>
        <?php if (isset($form['email_tellyour_body'])): ?>
          <div class="control-group">
              <div class="controls">
                  Note: Email text will be limited to approx. 2000 characters.
              </div>
          </div>
        <?php endif ?>
        <?php echo $form->renderRows(array('*thank_you_email_subject', '*thank_you_email_body')) ?>
        <?php if (isset($form['thank_you_email_body'])): ?>
          <div class="control-group">
              <div class="controls">
                  Note: #UNSUBSCRIBE-URL# adds a link for participants to unsubscribe. A click on it will not revoke participation, but you won't be able to access the person's email address. Make sure you include this, as it is your legal obligation to allow participants to unsubscribe easily.
              </div>
          </div>
        <?php endif ?>

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

        <?php if (isset($form['signers_page'])): ?>
          <legend>Signers page</legend>
          <?php if (!$translation->isNew()): ?>
            <div class="control-group">
                <label class="control-label"></label>
                <div class="controls">
                    Link:
                    <a target="_blank" href="<?php echo url_for('signers', array('id' => $petition->getId(), 'text_id' => $translation->getId())) ?>">
                        <?php echo url_for('signers', array('id' => $petition->getId(), 'text_id' => $translation->getId()), true) ?>
                    </a>
                    <br />
                    Embed snippet:
                    <code>&lt;iframe src="<?php echo url_for('signers', array('id' => $petition->getId(), 'text_id' => $translation->getId()), true) ?>" frameborder="0"&gt;&lt;/iframe&gt;</code>
                </div>
            </div>
          <?php endif ?>
          <?php echo $form->renderRows(array('signers_page', 'signers_url')) ?>
        <?php endif ?>

        <?php if (isset($form['target_selector_1'])): ?>
          <legend>Preselect Targets</legend>
          <?php echo $form->renderRows(array('target_selector_1', 'target_selector_2*')) ?>
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
        <button accesskey="s" title="[Accesskey] + S" class="btn btn-primary" type="submit">Save</button>
        <a class="btn submit" data-submit='{"go_widget":1}'>Save &amp; and create new widget from this translation</a>
        <a class="btn" href="<?php echo url_for('petition_translations', array('id' => $petition->getId())) ?>">Cancel</a>
    </div>
</form>
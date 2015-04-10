<?php
/* @var $petition Petition */
$user = $sf_user->getGuardUser()->getRawValue(); /* @var $user sfGuardUser */
$link_petition = $user->isPetitionMember($petition->getRawValue(), true);
$link_campaign = $user->isCampaignMember($petition->getCampaign()->getRawValue());
?>
<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li>
    <?php if ($link_campaign): ?><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php endif ?>
      <?php echo $petition->getCampaign()->getName() ?>
      <?php if ($link_campaign): ?></a><?php endif ?>
  </li><span class="divider">/</span>
  <li>
    <?php if ($link_petition): ?><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php endif ?>
      <?php echo $petition->getName() ?>
      <?php if ($link_petition): ?></a><?php endif ?>
  </li><span class="divider">/</span>
  <li>
    <?php if ($link_petition): ?><a href="<?php echo url_for('petition_widgets', array('id' => $petition->getId())) ?>"><?php endif ?>
      Widgets
      <?php if ($link_petition): ?></a><?php endif ?>
  </li><span class="divider">/</span>
  <li class="active"><?php if ($form->getObject()->isNew()): ?>New<?php else: ?>Edit<?php endif ?></li>
</ul>
<?php include_partial('d_action/tabs', array('petition' => $petition, 'active' => 'widgets')) ?>
<h2>Settings</h2>
<form class="ajax_form form-horizontal" action="<?php echo $form->getObject()->isNew() ? url_for('widget_create', array('id' => $petition->getId())) : url_for('widget_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
  <?php if (isset($lang)): ?><input type="hidden" name="lang" value="<?php echo $lang ?>"/><?php endif ?>
  <?php echo $form->renderHiddenFields() ?>
  <?php echo $form->renderRows('status', '*title', '*target', '*background') ?>
  <?php
  if ($petition->isEmailKind()) {
    if ($petition->getKind() != Petition::KIND_PLEDGE) {
      echo $form->renderRows('*email_subject', '*email_body');
    }
  } else {
    echo $form->renderRows('*intro', '*footer');
  }
  ?>
  <div class="row">
    <div class="span6"><?php echo $form->renderRows('styling_type', 'styling_width', '*styling_title_color', '*styling_body_color') ?></div>
    <div class="span6"><?php echo $form->renderRows('*styling_button_color', '*styling_bg_left_color', '*styling_bg_right_color', '*styling_form_title_color') ?></div>
  </div>
  <?php echo $form->renderRows('paypal_email', 'landing_url') ?>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn" href="<?php echo url_for('petition_widgets', array('id' => $petition->getId())) ?>">Cancel</a>
  </div>
</form>
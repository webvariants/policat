<?php
/* @var $petition Petition */
$user = $sf_user->getGuardUser()->getRawValue(); /* @var $user sfGuardUser */
$link_petition = $user->isPetitionMember($petition->getRawValue(), true);
$link_campaign = $user->isCampaignMember($petition->getCampaign()->getRawValue());
$text_id = $form->getObject()->getPetitionTextId();
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
<?php include_partial('form', array(
    'form' => $form,
    'petition' => $petition,
    'lang' => isset($lang) ? $lang : null
)) ?>

<?php if ($petition->getLastSignings() != PetitionTable::LAST_SIGNINGS_NO && $text_id): ?>
  <h2>Signers page</h2>
  <p>
      Link:
      <a target="_blank" href="<?php echo url_for('signers', array('id' => $petition->getId(), 'text_id' => $text_id)) ?>">
          <?php echo url_for('signers', array('id' => $petition->getId(), 'text_id' => $text_id), true) ?>
      </a>
  </p>
<?php endif ?>
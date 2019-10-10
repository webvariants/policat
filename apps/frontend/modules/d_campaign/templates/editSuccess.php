<?php
/* @var $campaign Campaign */
/* @var $admin int */
/* @var $sf_user myUser */
$user = $sf_user->getGuardUser(); /* @var $user sfGuardUser */
$officer = $campaign->getDataOwnerId() ? $campaign->getDataOwner() : null; /* @var $officer sfGuardUser */
$officer_self = $officer && $officer->getId() == $user->getId();
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active"><?php echo $campaign->getName() ?></li>
  </ol>
</nav>
<?php
if ($billingEnabled) {
  include_component('order', 'notice', array('campaign' => $campaign));
}
?>
<?php include_partial('tabs', array('campaign' => $campaign, 'active' => 'overview')) ?>
<div class="row">
    <div class="col-md-8">
        <h2>E-actions</h2>
        <?php include_component('d_action', 'list', array('campaign' => $campaign)) ?>
        <a class="btn btn-primary" href="<?php echo url_for('petition_new_') . '?campaign=' . $campaign->getId() ?>">Start new e-action</a>
        <?php if ($admin): ?><?php include_component('ticket', 'todo', array('campaign_id' => $campaign->getId())) ?><?php endif ?>
    </div>
    <div class="col-md-4">
        <div class="card bg-light mb-3">
          <div class="card-body">
            <h3>Administration</h3>
            <p>
                <?php if ($officer_self): ?>
                  You are data protection officer.
                  <a class="btn btn-secondary btn-sm ajax_link pull-right" href="<?php echo url_for('campaign_resign_officer', array('id' => $campaign->getId())) ?>">resign</a>
                <?php elseif ($admin): ?>
                  You are <span class="label label-important">admin</span>.
                <?php else: ?>
                  You are member.
                <?php endif ?>
                <?php if (!$officer_self && $campaign->getPublicEnabled() == Campaign::PUBLIC_ENABLED_NO): ?>
                  <a title="Leave this campaign." class="ajax_link btn btn-sm pull-right" href="<?php echo url_for('campaign_leave', array('id' => $campaign->getId())) ?>">Leave campaign</a>
                <?php endif ?>
            </p>
            <?php if ($campaign->getPublicEnabled() == Campaign::PUBLIC_ENABLED_YES): ?>
              <p>This is a community campaign.</p>
            <?php endif ?>
            <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
              <?php if ($campaign->getStatus() != CampaignTable::STATUS_DELETED): ?>
                <a class="btn btn-danger btn-sm ajax_link" href="<?php echo url_for('campaign_delete_', array('id' => $campaign->getId())) ?>">Delete Campaign</a>
              <?php else: ?>
                <a class="btn btn-warning btn-sm ajax_link" href="<?php echo url_for('campaign_undelete', array('id' => $campaign->getId())) ?>">Undelete Campaign</a>
              <?php endif ?>
            <?php endif ?>
            <?php if (!$officer_self): ?>
              <p id="campaign_data_officer">
                  Data protection officer is <?php echo $officer ? $officer->getFullName() : '<b>nobody</b>' ?>.
                  <?php if ($hasResign): /* @var $hasResign Ticket */ ?>(<?php echo $hasResign->getTo()->getFullName() ?>)<?php endif ?>
                  <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_SYSTEM)): ?>
                    <a class="btn btn-secondary btn-sm ajax_link pull-right" href="<?php echo url_for('campaign_resign_officer', array('id' => $campaign->getId())) ?>">resign</a>
                  <?php else: if ($user->isCampaignAdmin($campaign->getRawValue())): ?>
                      <a class="btn btn-secondary btn-sm ajax_link pull-right" href="<?php echo url_for('campaign_call_officer', array('id' => $campaign->getId())) ?>">call</a>
                      <?php
                    endif;
                  endif
                  ?>
              </p>
            <?php endif ?>
            <?php if ($admin): ?>
              <?php include_component('d_campaign', 'editSwitches', array('campaign' => $campaign)) ?>
              <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?><?php include_component('d_campaign', 'editPublic', array('campaign' => $campaign)) ?><?php endif ?>
              <br />
              <a class="btn btn-secondary btn-sm ajax_link mb-1" href="<?php echo url_for('campaign_name', array('id' => $campaign->getId())) ?>">Rename campaign</a>
              <a class="btn btn-secondary btn-sm ajax_link mb-1" href="<?php echo url_for('campaign_privacy', array('id' => $campaign->getId())) ?>">Widget data owner agreement</a>
              <a class="btn btn-secondary btn-sm ajax_link mb-1" href="<?php echo url_for('campaign_address', array('id' => $campaign->getId())) ?>">Address</a>
            <?php endif ?>
          </div>
        </div>
        <?php
        if ($billingEnabled) {
          include_component('order', 'sidebar', array('campaign' => $campaign));
        }
        ?>
        <?php if ($admin): ?>
          <?php include_component('d_campaign', 'members', array('campaign' => $campaign)) ?>
        <?php endif ?>
    </div>
</div>

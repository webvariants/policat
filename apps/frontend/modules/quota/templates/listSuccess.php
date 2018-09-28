<?php
/* @var $campaign Campaign */
/* @var $admin int */
/* @var $sf_user myUser */
$user = $sf_user->getGuardUser(); /* @var $user sfGuardUser */
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a></li>
    <li class="breadcrumb-item active">Billing &amp; Packages</li>
  </ol>
</nav>
<?php
if ($billingEnabled) {
  include_component('order', 'notice', array('campaign' => $campaign));
}
?>
<?php include_partial('d_campaign/tabs', array('campaign' => $campaign, 'active' => 'billing')) ?>
<div class="row">
    <div class="span8">

        <?php include_component('quota', 'list', array('campaign' => $campaign)) ?>
    </div>
    <div class="span4">
        <?php include_component('order', 'editBilling', array('campaign' => $campaign)) ?>
        <?php if ($order): ?>
          <?php if ($orderEdit): ?>
            <a class="btn btn-success bottom10" href="<?php echo url_for('order_show', array('id' => $order->getId())) ?>">Show active order</a>
          <?php else: ?>
            <p>User "<?php echo $order->getUser()->getFullname() ?>" has an active order.</p>
          <?php endif ?>
        <?php else: ?>
          <?php if ($campaign->getBillingEnabled()): ?>
            <a class="btn btn-primary bottom10" href="<?php echo url_for('order_new', array('id' => $campaign->getId())) ?>">Buy package</a>
          <?php endif ?>
        <?php endif ?>
        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
          <br /><a href="<?php echo url_for('quota_new', array('id' => $campaign->getId())) ?>">Create package</a> (admin function)
        <?php endif ?>
    </div>
</div>

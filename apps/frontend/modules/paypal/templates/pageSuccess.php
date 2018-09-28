<?php use_helper('I18N', 'Number', 'Date') ?>
<?php if ($sf_user->isAuthenticated() && isset($campaign)): ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a></li>
      <li  class="breadcrumb-item active">Order</li>
    </ol>
  </nav>
<?php endif ?>
<div class="row">
    <div class="span8">
        <h2><?php echo $title ?></h2>
        <p><?php echo $message ?></p>
        <p>
            <?php if (isset($order)): ?>
              <a class="btn btn-primary" href="<?php echo url_for('order_show', array('id' => $order->getId())) ?>">Back to order</a>
            <?php endif ?>
            <?php if (isset($campaign)): ?>
              <a class="btn" href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>">Back to campaign</a>
            <?php endif ?>
        <p>
    </div>
</div>

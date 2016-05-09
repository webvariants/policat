<?php if ($show): ?>
  <div class="well">
      <h3>Billing &amp; Packages <a class="btn btn-mini pull-right" href="<?php echo url_for('quota_list', array('id' => $campaign->getId())) ?>">more</a></h3>
      <?php include_component('order', 'notice', array('campaign' => $campaign)); ?>
      <?php include_component('order', 'editBilling', array('campaign' => $campaign)) ?>
      <?php if ($order): ?>
        <?php if ($orderEdit): ?>
          <a class="btn btn-mini bottom10" href="<?php echo url_for('order_show', array('id' => $order->getId())) ?>">Show active order</a>
        <?php else: ?>
          <p>User "<?php echo $order->getUser()->getFullname() ?>" has an active order.</p>
        <?php endif ?>
      <?php endif ?>
      <?php if ($quota): ?>
        <div>
            <strong>Active package:</strong> <?php echo $quota->getName() ?><br />
            <div class="progress progress-info bottom0 top5"><div class="bar" style="width: <?php echo $quota->getPercent() ?>%;"></div><div class="title"><?php echo format_number($quota->getEmailsRemaining()) ?> remaining</div></div>
            <p class="top5"><strong>Expiry date:</strong> <?php echo format_date($quota->getEndAt(), 'yyyy-MM-dd') ?></p>
        </div>
      <?php endif ?>
  </div>
<?php endif; ?>
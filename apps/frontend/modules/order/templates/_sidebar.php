<?php if ($show): ?>
  <div class="well">
      <h3>Billing &amp; Packages <a class="btn btn-sm pull-right" href="<?php echo url_for('quota_list', array('id' => $campaign->getId())) ?>">more</a></h3>
      <?php include_component('order', 'notice', array('campaign' => $campaign)); ?>
      <?php include_component('order', 'editBilling', array('campaign' => $campaign)) ?>
      <?php if ($order): ?>
        <?php if ($orderEdit): ?>
          <a class="btn btn-sm bottom10" href="<?php echo url_for('order_show', array('id' => $order->getId())) ?>">Show active order</a>
        <?php else: ?>
          <p>User "<?php echo $order->getUser()->getFullname() ?>" has an active order.</p>
        <?php endif ?>
      <?php endif ?>
      <?php if ($quota):
          if ($quota->getSubscription()):
            ?>
            <div>
              <strong>Active subscription:</strong> <?php echo $quota->getName() ?><br />
              <div class="progress progress-info bottom0 top5"><div class="bar" style="width: <?php echo $quota->getPercent() ?>%;"></div><div class="title"><?php echo format_number($quota->getEmailsRemaining()) ?> remaining</div></div>
              <p class="top5"><strong>Latest renew date:</strong> <?php echo format_date($quota->getEndAt(), 'yyyy-MM-dd') ?></p>
              <?php if ($admin): ?>
              <a class="btn btn-sm btn-danger ajax_link" href="<?php echo url_for('order_cancel_subscription', array('id' => $quota->getId())) ?>">Cancel subscription</a>
                <?php endif ?>
            </div>
            <?php
          else:
            ?>
            <div>
              <strong>Active package:</strong> <?php echo $quota->getName() ?><br />
              <div class="progress progress-info bottom0 top5"><div class="bar" style="width: <?php echo $quota->getPercent() ?>%;"></div><div class="title"><?php echo format_number($quota->getEmailsRemaining()) ?> remaining</div></div>
              <p class="top5"><strong>Expiry date:</strong> <?php echo format_date($quota->getEndAt(), 'yyyy-MM-dd') ?></p>
            </div>
          <?php
  endif;
      endif ?>
  </div>
<?php endif; ?>

<?php if ($showNotice): ?>
  <div class="alert alert-danger">
      <a class="close" data-dismiss="alert">&times;</a>
      <strong>Note:</strong>
      <?php if ($showOrder): ?>
      You ordered a package for this campaign. Your action(s) will (re-)start after receipt of payment.
      <?php else: ?>
      You need to buy a package to (re-)start your action(s) in this campaign.
      <?php endif ?>
      <?php if ($showBuy): ?>
      <a class="btn btn-mini" href="<?php echo url_for('order_new', array('id' => $campaign->getId())) ?>">Buy package</a>
      <?php endif ?>
      <?php if ($showOrder): ?>
      <a class="btn btn-mini" href="<?php echo url_for('order_show', array('id' => $campaign->getOrderId())) ?>">Show active order</a>
      <?php endif ?>
  </div>
  <?php
 endif;
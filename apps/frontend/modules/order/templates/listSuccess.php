<?php use_helper('Number', 'Date') ?>
<?php include_partial('dashboard/admin_tabs', array('active' => 'order')) ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr><th class="span1">ID</th><th>Date</th><th>Campaign</th><th>User</th><th>Organisation</th><th>Net</th><th>Tax</th><th>Gross</th><th>Status</th><th>Paypal</th><th>Invoice</th><th class="span2"></th></tr>
    </thead>
    <tbody>
        <?php
        foreach ($quotas as $quota): /* @var $quota Quota */
          $order = $quota->getOrderId() ? $quota->getOrder() : null;
          /* @var $order Order */
          if ($order):
          $tax = $order->getTax();
          ?>
          <tr>
              <td><a href="<?php echo url_for('order_show', array('id' => $order->getId())) ?>"><?php echo $order->getId() ?></a></td>
              <td><?php echo format_date($order->getCreatedAt(), 'yyyy-MM-dd') ?></td>
              <td>
                  <?php if ($quota->getCampaignId()): ?>
                    <a href="<?php echo url_for('campaign_edit_', array('id' => $quota->getCampaignId())) ?>"><?php echo $quota->getCampaign()->getName() ?></a>
                  <?php else: ?>
                    deleted
                  <?php endif ?>
              </td>
              <td><a href="<?php echo url_for('user_edit', array('id' => $order->getUserId())) ?>"><?php echo $order->getUser()->getFullName() ?></a></td>
              <td><?php echo $order->getUser()->getOrganisation() ?></td>
              <td><?php echo $quota ? format_currency($quota->getPrice(), StoreTable::value(StoreTable::BILLING_CURRENCY)) : null ?></td>
              <td><?php echo $order->getTax() ?></td>
              <td><?php echo $quota && $tax ? format_currency($quota->getPriceBrutto($tax), StoreTable::value(StoreTable::BILLING_CURRENCY)) : null ?></td>
              <td><?php echo OrderTable::$STATUS_SHOW[$order->getStatus()] ?><br /><?php echo format_date($order->getPaidAt(), 'yyyy-MM-dd') ?></td>
              <td><?php echo $order->getPaypalStatus() == OrderTable::PAYPAL_STATUS_PAYMENT_EXECUTED ? 'executed' : '' ?></td>
              <td>
                  <?php if ($order->getStatus() != OrderTable::STATUS_CANCELATION && $order->getBill()->isNew()): ?>
                    <a class="btn btn-sm btn-success ajax_link" data-submit='<?php echo json_encode(array('order_page' => $order_page)) ?>' href="<?php echo url_for('bill_new', array('id' => $order->getId())) ?>">create</a>
                  <?php endif ?>
                  <?php if (!$order->getBill()->isNew()): ?>
                    <a class="btn btn-sm btn-success ajax_link" data-submit='<?php echo json_encode(array('view' => 'popup')) ?>' href="<?php echo url_for('bill_show', array('id' => $order->getBill()->getId())) ?>"><?php echo $order->getBill()->getIdPrefixed() ?></a>
                  <?php endif ?>
              </td>
              <td>
                  <?php if ($order->getStatus() == OrderTable::STATUS_ORDER): ?>
                    <a class="btn btn-sm btn-success ajax_link" data-submit='<?php echo json_encode(array('order_page' => $order_page)) ?>' href="<?php echo url_for('order_paid', array('id' => $order->getId())) ?>">paid</a>
                  <?php endif ?>
                  <a class="btn btn-sm btn-danger ajax_link" data-submit='<?php echo json_encode(array('order_page' => $order_page)) ?>' href="<?php echo url_for('order_delete', array('id' => $order->getId())) ?>">delete</a>
              </td>
          </tr>
          <?php else: ?>
          <tr>
              <td></td>
              <td><?php echo format_date($quota->getCreatedAt(), 'yyyy-MM-dd') ?></td>
              <td>
                  <?php if ($quota->getCampaignId()): ?>
                    <a href="<?php echo url_for('campaign_edit_', array('id' => $quota->getCampaignId())) ?>"><?php echo $quota->getCampaign()->getName() ?></a>
                  <?php else: ?>
                    deleted
                  <?php endif ?>
              </td>
              <td colspan="8">no order</td>
              <td>
                <a class="btn btn-primary btn-sm" href="<?php echo url_for('quota_edit', array('id' => $quota->getId())) ?>">edit</a>
                <?php if ($quota->getCampaignId()): ?>
                <a class="btn btn-sm ajax_link" href="<?php echo url_for('order_manual_user', array('id' => $quota->getId())) ?>">create order</a>
                <?php endif ?>
              </td>
          </tr>
          <?php endif; ?>
        <?php endforeach ?>
    </tbody>
</table>
<?php include_partial('dashboard/pager', array('pager' => $quotas)) ?>

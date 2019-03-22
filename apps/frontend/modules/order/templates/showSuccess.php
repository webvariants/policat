<?php use_helper('I18N', 'Number', 'Date') ?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('quota_list', array('id' => $campaign->getId())) ?>">Billing &amp; Packages</a></li>
    <li class="breadcrumb-item active">Order</li>
  </ol>
</nav>
<div class="row">
    <div class="col-md-12">
        <?php
        if ($order->getStatus() == OrderTable::STATUS_ORDER) {
          echo $sf_data->getRaw('markup');
        }
        ?>
    </div>
    <div class="col-md-6">
        <div class="form-horizontal">
            <fieldset>
                <legend>Order</legend>
                <div class="control-group">
                    <label class="control-label">Order ID</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getId() ?></span>
                    </div>
                    <label class="control-label">Invoice ID</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getBill()->getIdPrefixed() ?>&nbsp;</span>
                    </div>
                    <label class="control-label">Campaign</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $campaign->getName() ?></span>
                    </div>
                    <label class="control-label">Date</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo format_date($order->getCreatedAt(), 'yyyy-MM-dd') ?>&nbsp;</span>
                    </div>
                    <label class="control-label">Status</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo OrderTable::$STATUS_SHOW[$order->getStatus()] ?></span>
                    </div>
                    <label class="control-label">Paid at</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getPaidAt() ? format_date($order->getPaidAt(), 'yyyy-MM-dd') : 'not yet' ?>&nbsp;</span>
                    </div>
                    <label class="control-label">Tax</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getTax() ?>%&nbsp;</span><br />
                        <?php echo $order->getTaxNote() ?>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-horizontal">
            <fieldset>
                <legend>Billing address</legend>
                <div class="control-group">
                    <label class="control-label">First name</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getFirstName() ?>&nbsp;</span>
                    </div>
                    <label class="control-label">Last name</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getLastName() ?>&nbsp;</span>
                    </div>
                    <label class="control-label">Organisation</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getOrganisation() ?>&nbsp;</span>
                    </div>
                    <label class="control-label">Street</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getStreet() ?>&nbsp;</span>
                    </div>
                    <label class="control-label">City</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getCity() ?>&nbsp;</span>
                    </div>
                    <label class="control-label">Post code</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getPostCode() ?>&nbsp;</span>
                    </div>
                    <label class="control-label">Country</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo format_country($order->getCountry()) ?>&nbsp;</span>
                    </div>
                    <label class="control-label">VAT</label>
                    <div class="controls">
                        <span class="widget_text"><?php echo $order->getVat() ?>&nbsp;</span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-horizontal">
            <fieldset>
                <legend>Package</legend>
                <div class="control-group">
                    <?php foreach ($order->getQuotas() as $quota): /* @var $quota Quota */ ?>
                      <label class="control-label">Name</label>
                      <div class="controls">
                          <span class="widget_text"><?php echo $quota->getName() ?></span>
                      </div>
                      <label class="control-label">Price (net)</label>
                      <div class="controls">
                          <span class="widget_text"><?php echo format_currency($quota->getPrice(), StoreTable::value(StoreTable::BILLING_CURRENCY)) ?></span>
                      </div>
                      <?php if ($order->getTax()): ?>
                        <label class="control-label">Price (gross)</label>
                        <div class="controls">
                            <span class="widget_text"><?php echo format_currency($quota->getPriceBrutto($order->getTax()), StoreTable::value(StoreTable::BILLING_CURRENCY)) ?></span>
                        </div>
                      <?php endif ?>
                      <label class="control-label">E-mails / participants</label>
                      <div class="controls">
                          <span class="widget_text"><?php echo $quota->getEmails() ?></span>
                      </div>
                      <label class="control-label">Days</label>
                      <div class="controls">
                          <span class="widget_text"><?php echo $quota->getDays() ?></span>
                      </div>
                      <?php if (StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_ENABLE)): ?>
                      <label class="control-label">Subscription</label>
                      <div class="controls">
                          <span class="widget_text"><?php echo $quota->getSubscription() ? 'yes' : 'no' ?></span>
                      </div>
                    <?php endif ?>
                    <?php endforeach ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-horizontal">
            <fieldset>
                <?php if ($order->getPaypalPaymentId() || $order->getPaypalSaleId()): ?>
                  <legend>Paypal</legend>
                  <div class="control-group">
                      <?php if ($order->getPaypalPaymentId()): ?>
                        <label class="control-label">Payment ID</label>
                        <div class="controls">
                            <span class="widget_text"><?php echo $order->getPaypalPaymentId() ?></span>
                        </div>
                      <?php endif ?>
                      <?php if ($order->getPaypalSaleId()): ?>
                        <label class="control-label">Sale ID</label>
                        <div class="controls">
                            <span class="widget_text"><?php echo $order->getPaypalSaleId() ?></span>
                        </div>
                      <?php endif ?>
                      <?php if ($order->getPaypalStatus() == OrderTable::PAYPAL_STATUS_PAYMENT_EXECUTED): ?>
                        <label class="control-label">Status</label>
                        <div class="controls">
                            <span class="widget_text">payment executed</span>
                        </div>
                      <?php endif ?>
                  </div>
                <?php endif ?>
            </fieldset>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-horizontal">
            <div class="form-actions">
                <?php if ($paypal): ?>
                  <div class="text-next-to-btn">
                      <a class="btn btn-large btn-primary ajax_link" href="<?php echo url_for('paypal_pay', array('id' => $order->getId())) ?>">
                          <strong>Pay now</strong> &nbsp;
                          <?php echo image_tag('pay-mastercard.png', array('size' => '50x31', 'alt' => 'mastercard')) ?>
                          <?php echo image_tag('pay-maestro.png', array('size' => '50x31', 'alt' => 'maestro')) ?>
                          <?php echo image_tag('pay-visa.png', array('size' => '50x31', 'alt' => 'visa')) ?>
                          <?php echo image_tag('pay-paypal.png', array('size' => '50x31', 'alt' => 'paypal')) ?>
                          <?php echo image_tag('pay-direct-debit.png', array('size' => '50x31', 'alt' => 'direct debit')) ?>
                      </a>
                      <div>
                          <strong>to activate the package immediately.</strong><br />
                          You may also transfer the amount to the account stated in the invoice.<br />
                          Your package will then be activated a few days after receipt.
                      </div>
                  </div>
                <?php endif ?>
                <a class="btn <?php if (!$paypal): ?> btn-primary<?php endif ?>" href="<?php echo url_for('quota_list', array('id' => $campaign->getId())) ?>">Back to campaign</a>
                <a class="btn ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('order_bill', array('id' => $order->getId())) ?>">Invoice</a>
                <?php if ($order->deleteable() || $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
                <br />
                <br />
                  <a style="padding-left: 10px; color: red;" class="ajax_link" href="<?php echo url_for('order_delete', array('id' => $order->getId())) ?>">
                      <?php if ($order->deleteable() || ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN) && $order->getStatus() == OrderTable::STATUS_CANCELATION)): ?>
                        Delete order
                      <?php else: ?>
                        Cancel order
                      <?php endif ?>
                  </a>
                <?php endif ?>
            </div>
        </div>
    </div>

</div>

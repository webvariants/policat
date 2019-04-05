<?php use_helper('I18N', 'Number', 'Date') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a
                href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a>
        </li>
        <li class="breadcrumb-item"><a
                href="<?php echo url_for('quota_list', array('id' => $campaign->getId())) ?>">Billing &amp; Packages</a>
        </li>
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
        <legend>Order</legend>
        <dl class="row">
            <dt class="col-md-3">Order ID</dt>
            <dd class="col-md-9"><?php echo $order->getId() ?></dd>
            <dt class="col-md-3">Invoice ID</dt>
            <dd class="col-md-9"><?php echo $order->getBill()->getIdPrefixed() ?>&nbsp;</dd>
            <dt class="col-md-3">Campaign</dt>
            <dd class="col-md-9"><?php echo $campaign->getName() ?></dd>
            <dt class="col-md-3">Date</dt>
            <dd class="col-md-9"><?php echo format_date($order->getCreatedAt(), 'yyyy-MM-dd') ?>&nbsp;</dd>
            <dt class="col-md-3">Status</dt>
            <dd class="col-md-9"><?php echo OrderTable::$STATUS_SHOW[$order->getStatus()] ?></dd>
            <dt class="col-md-3">Paid at</dt>
            <dd class="col-md-9">
                <?php echo $order->getPaidAt() ? format_date($order->getPaidAt(), 'yyyy-MM-dd') : 'not yet' ?>&nbsp;
            </dd>
            <dt class="col-md-3">Tax</dt>
            <dd class="col-md-9"><?php echo $order->getTax() ?>%&nbsp;<?php echo $order->getTaxNote() ?></dd>
        </dl>
    </div>
    <div class="col-md-6">
        <legend>Billing address</legend>
        <dl class="row">
            <dt class="col-md-3">First name</dt>
            <dd class="col-md-9"><?php echo $order->getFirstName() ?>&nbsp;</dd>
            <dt class="col-md-3">Last name</dt>
            <dd class="col-md-9"><?php echo $order->getLastName() ?>&nbsp;</dd>
            <dt class="col-md-3">Organisation</dt>
            <dd class="col-md-9"><?php echo $order->getOrganisation() ?>&nbsp;</dd>
            <dt class="col-md-3">Street</dt>
            <dd class="col-md-9"><?php echo $order->getStreet() ?>&nbsp;</dd>
            <dt class="col-md-3">City</dt>
            <dd class="col-md-9"><?php echo $order->getCity() ?>&nbsp;</dd>
            <dt class="col-md-3">Post code</dt>
            <dd class="col-md-9"><?php echo $order->getPostCode() ?>&nbsp;</dd>
            <dt class="col-md-3">Country</dt>
            <dd class="col-md-9"><?php echo format_country($order->getCountry()) ?>&nbsp;</dd>
            <dt class="col-md-3">VAT</dt>
            <dd class="col-md-9"><?php echo $order->getVat() ?>&nbsp;</dd>
        </dl>
    </div>
    <div class="col-md-6">
        <legend>Package</legend>
        <dl class="row">
            <?php foreach ($order->getQuotas() as $quota): /* @var $quota Quota */ ?>
            <dt class="col-md-3">Name</dt>
            <dd class="col-md-9"><?php echo $quota->getName() ?></dd>
            <dt class="col-md-3">Price (net)</dt>
            <dd class="col-md-9">
                <?php echo format_currency($quota->getPrice(), StoreTable::value(StoreTable::BILLING_CURRENCY)) ?></dd>
            <?php if ($order->getTax()): ?>
            <dt class="col-md-3">Price (gross)</dt>
            <dd class="col-md-9">
                <?php echo format_currency($quota->getPriceBrutto($order->getTax()), StoreTable::value(StoreTable::BILLING_CURRENCY)) ?>
            </dd>
            <?php endif ?>
            <dt class="col-md-3">E-mails / participants</dt>
            <dd class="col-md-9"><?php echo $quota->getEmails() ?></dd>
            <dt class="col-md-3">Days</dt>
            <dd class="col-md-9"><?php echo $quota->getDays() ?></dd>
            <?php if (StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_ENABLE)): ?>
            <dt class="col-md-3">Subscription</dt>
            <dd class="col-md-9"><?php echo $quota->getSubscription() ? 'yes' : 'no' ?></dd>
            <?php endif ?>
            <?php endforeach ?>
        </dl>
    </div>
    <div class="col-md-6">
        <?php if ($order->getPaypalPaymentId() || $order->getPaypalSaleId()): ?>
        <legend>Paypal</legend>
        <dl class="row">
            <?php if ($order->getPaypalPaymentId()): ?>
            <dt class="col-md-3">Payment ID</dt>
            <dd class="col-md-9"><?php echo $order->getPaypalPaymentId() ?></dd>
            <?php endif ?>
            <?php if ($order->getPaypalSaleId()): ?>
            <dt class="col-md-3">Sale ID</dt>
            <dd class="col-md-9"><?php echo $order->getPaypalSaleId() ?></dd>
            <?php endif ?>
            <?php if ($order->getPaypalStatus() == OrderTable::PAYPAL_STATUS_PAYMENT_EXECUTED): ?>
            <dt class="col-md-3">Status</dt>
            <dd class="col-md-9">payment executed</dd>
            <?php endif ?>
        </dl>
        <?php endif ?>
    </div>
    <div class="col-md-12">
        <div class="form-horizontal">
            <div class="form-actions">
                <?php if ($paypal): ?>
                <div class="text-next-to-btn">
                    <a class="btn btn-large btn-primary ajax_link"
                        href="<?php echo url_for('paypal_pay', array('id' => $order->getId())) ?>">
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
                <a class="btn <?php if (!$paypal): ?> btn-primary<?php endif ?>"
                    href="<?php echo url_for('quota_list', array('id' => $campaign->getId())) ?>">Back to campaign</a>
                <a class="btn btn-secondary ajax_link post"
                    data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>'
                    href="<?php echo url_for('order_bill', array('id' => $order->getId())) ?>">Invoice</a>
                <?php if ($order->deleteable() || $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
                <br />
                <br />
                <a style="padding-left: 10px; color: red;" class="ajax_link"
                    href="<?php echo url_for('order_delete', array('id' => $order->getId())) ?>">
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
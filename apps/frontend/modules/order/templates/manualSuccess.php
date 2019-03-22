<?php use_helper('Number') ?>
<?php include_partial('dashboard/admin_tabs', array('active' => 'order')) ?>
<h2>Manual order</h2>
<form class="ajax_form form-horizontal" action="<?php echo url_for('order_manual', array('id' => $quota->getId(), 'user_id' => $user->getId())) ?>" method="post">
    <p>User: <strong><?php echo $user->getFullname() ?></strong></p>
    <p>Campaign: <strong><?php echo $campaign->getName() ?></strong></p>
    <p>Package: <strong><?php echo $quota->getName() ?></strong>, Net: <strong><?php echo format_currency($quota->getPrice(), StoreTable::value(StoreTable::BILLING_CURRENCY)) ?></strong>, Days: <strong><?php echo $quota->getDays() ?></strong></p>
    <legend>Billing address</legend>
    <?php echo $form->renderOtherRows(); echo $form->renderHiddenFields() ?>
    <div class="form-actions">
        <button class="btn btn-primary">Create order</button>
        <a class="btn btn-secondary" href="<?php echo url_for('order_list') ?>">Cancel</a>
    </div>
</form>
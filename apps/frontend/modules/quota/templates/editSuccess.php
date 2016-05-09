<?php if ($campaign->getId()): ?>
<ul class="breadcrumb">
    <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
    <li><a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a></li><span class="divider">/</span>
    <li><a href="<?php echo url_for('quota_list', array('id' => $campaign->getId())) ?>">Billing &amp; Packages</a></li><span class="divider">/</span>
    <li class="active">Package</li>
</ul>
<?php include_partial('d_campaign/tabs', array('campaign' => $campaign, 'active' => 'quota')) ?>
<?php endif ?>
<h2>Package</h2>
<form class="ajax_form form-horizontal" action="<?php echo $form->getObject()->isNew() ? url_for('quota_new', array('id' => $campaign->getId())) : url_for('quota_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
    <?php echo $form ?>
    <div class="form-actions">
        <button class="btn btn-primary">Save</button>
        <?php if ($campaign->getId()): ?>
        <a class="btn" href="<?php echo url_for('quota_list', array('id' => $campaign->getId())) ?>" >Cancel</a>
        <?php else: ?>
        <a class="btn" href="<?php echo url_for('order_list') ?>" >Cancel</a>
        <?php endif ?>
    </div>
</form>
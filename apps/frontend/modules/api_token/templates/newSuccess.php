<?php use_helper('Number') ?>
<ul class="breadcrumb">
    <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
    <li><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li><span class="divider">/</span>
    <li><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li><span class="divider">/</span>
    <li class="active">Overview</li>
</ul>
<?php include_partial('d_action/tabs', array('petition' => $petition, 'active' => 'tokens')) ?>

<form class="ajax_form form-horizontal" action="<?php echo url_for('petition_token_new', array('id' => $petition->getId())) ?>" method="post">
    <?php echo $form ?>
    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn" href="<?php echo url_for('petition_tokens', array('id' => $petition->getId())) ?>">Cancel</a>
    </div>
</form>
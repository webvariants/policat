<?php use_helper('Number') ?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li>
    <li class="breadcrumb-item active">Overview</li>
  </ol>
</div>
<?php include_partial('d_action/tabs', array('petition' => $petition, 'active' => 'tokens')) ?>

<form class="ajax_form form-horizontal" action="<?php echo url_for('petition_token_edit', array('id' => $form->getObject()->getId())) ?>" method="post">
    <?php echo $form ?>
    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn" href="<?php echo url_for('petition_tokens', array('id' => $petition->getId())) ?>">Cancel</a>
    </div>
</form>

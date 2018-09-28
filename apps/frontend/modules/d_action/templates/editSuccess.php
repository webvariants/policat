<?php $petition = $form->getObject() ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('tabs', array('petition' => $petition, 'active' => 'edit')) ?>
<?php include_partial('form', array('form' => $form)) ?>

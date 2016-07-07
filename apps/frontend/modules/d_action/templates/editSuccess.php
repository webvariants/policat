<?php $petition = $form->getObject() ?>
<ul class="breadcrumb">
    <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
    <li><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li><span class="divider">/</span>
    <li><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li><span class="divider">/</span>
    <li class="active">Edit</li>
</ul>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('tabs', array('petition' => $petition, 'active' => 'edit')) ?>
<?php include_partial('form', array('form' => $form)) ?>

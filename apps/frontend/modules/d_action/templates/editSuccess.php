<?php $petition = $form->getObject() ?>
<ul class="breadcrumb">
    <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
    <li><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li><span class="divider">/</span>
    <li><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li><span class="divider">/</span>
    <li class="active">Edit</li>
</ul>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('tabs', array('petition' => $petition, 'active' => 'edit')) ?>
<div class="row">
    <div class="span8">
        <?php include_partial('form', array('form' => $form)) ?>
    </div>
    <div class="span4">
        <?php if ($petition->getStatus() == Petition::STATUS_DELETED && $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
          <div class="well">
          <a class="btn btn-danger btn-mini ajax_link" href="<?php echo url_for('petition_delete_', array('id' => $petition->getId())) ?>">Wipe Action</a>
          </div>
        <?php endif ?>
        <?php include_component('d_action', 'members', array('petition' => $petition)) ?>
        <?php include_component('d_action', 'editFollow', array('petition' => $petition)) ?>
    </div>
</div>
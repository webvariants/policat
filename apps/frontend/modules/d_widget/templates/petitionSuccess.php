<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li><span class="divider">/</span>
  <li class="active">Widgets</li>
</ul>
<?php include_partial('d_action/tabs', array('petition' => $petition, 'active' => 'widgets')) ?>
<?php include_component('d_widget', 'list', array('petition' => $petition)) ?>

<form id="new_widget" class="ajax_form form-inline" action="<?php echo url_for('widget_create', array('id' => $petition->getId())) ?>" method="post">
  <?php echo $form ?>
  <button class="btn btn-small">New widget</button>
</form>
<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('petition_widgets', array('id' => $petition->getId())) ?>">Widgets</a></li><span class="divider">/</span>
  <li class="active">Signings of Widget <?php echo $widget->getId() ?></li>
</ul>
<?php include_partial('d_action/tabs', array('petition' => $petition)) ?>
<?php include_component('data', 'list', array('widget' => $widget, 'subscriptions' => $subscriptions)) ?>
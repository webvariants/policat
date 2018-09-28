<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('petition_widgets', array('id' => $petition->getId())) ?>">Widgets</a></li>
    <li  class="breadcrumb-item active">Signings of Widget <?php echo $widget->getId() ?></li>
  </ol>
</nav>
<?php include_partial('d_action/tabs', array('petition' => $petition)) ?>
<?php include_component('data', 'list', array('widget' => $widget, 'subscriptions' => $subscriptions)) ?>

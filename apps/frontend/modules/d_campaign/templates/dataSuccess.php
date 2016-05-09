<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a></li><span class="divider">/</span>
  <li class="active">Signings</li>
</ul>
<?php include_partial('tabs', array('campaign' => $campaign, 'active' => $subscriptions ? 'dataSubscriptions' : 'data')) ?>
<?php include_component('data', 'list', array('campaign' => $campaign, 'subscriptions' => $subscriptions)) ?>
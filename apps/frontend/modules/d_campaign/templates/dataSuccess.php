<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a></li>
    <li class="breadcrumb-item active">Signings</li>
  </ol>
</nav>
<?php include_partial('tabs', array('campaign' => $campaign, 'active' => $subscriptions ? 'dataSubscriptions' : 'data')) ?>
<?php include_component('data', 'list', array('campaign' => $campaign, 'subscriptions' => $subscriptions)) ?>

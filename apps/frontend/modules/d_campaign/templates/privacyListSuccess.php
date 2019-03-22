<?php
/* @var $campaign Campaign */
/* @var $admin int */
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active"><?php echo $campaign->getName() ?></li>
  </ol>
</nav>
<?php include_partial('tabs', array('campaign' => $campaign, 'active' => 'privacy')) ?>
<div class="row">
  <div class="col-md-2">
    <div class="nav flex-column nav-pills">
      <?php foreach ($languages as $language): /* @var $language Language */ ?>
      <a class="nav-link" href="<?php echo url_for('campaign_privacy_edit', array('key' => CampaignStoreTable::KEY_PRIVACY_POLICY, 'id' => $campaign->getId(), 'lang' => $language->getId())) ?>"><?php echo $language->getName() ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

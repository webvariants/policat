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
<div class="tabbable tabs-left row">
  <div class="col-md-2">
    <ul class="nav flex-column nav-pills">
      <?php foreach ($languages as $language_i): /* @var $language_i Language */ ?>
        <a class="nav-link <?php if ($language->getId() == $language_i->getId()): ?>active<?php endif ?>" href="<?php echo url_for('campaign_privacy_edit', array('key' => CampaignStoreTable::KEY_PRIVACY_POLICY, 'id' => $campaign->getId(), 'lang' => $language_i->getId())) ?>"><?php echo $language_i->getName() ?></a>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="col-md-10">
    <?php if ($form->getObject()->isNew()): ?>
    <div id="no_text" class="alert alert-danger" style="clear:left">
      <a class="close" data-dismiss="alert">&times;</a>
      No text defined yet. The field below is filled with default data. Please save it.
    </div>
    <?php endif ?>
    <?php $action = url_for('campaign_privacy_edit', array('id' => $campaign->getId(), 'key' => CampaignStoreTable::KEY_PRIVACY_POLICY, 'lang' => $language->getId())) ?>
    <form id="campaign_privacy_form" class="ajax_form form-horizontal" action="<?php echo $action ?>" method="post">
      <?php echo $form ?>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-secondary" href="<?php echo url_for('campaign_privacy_list', array('id' => $campaign->getId())) ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

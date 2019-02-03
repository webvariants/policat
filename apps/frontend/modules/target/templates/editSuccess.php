<?php
/* @var $campaign Campaign */
$hide_edit = false;
?>
<?php if (isset($petition)): ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li>
      <li class="breadcrumb-item"><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol>
  </nav>
  <?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
  <?php include_partial('d_action/tabs', array('petition' => $petition, 'active' => 'targets')) ?>
  <div class="row">
    <div class="span8">
      <h2>Recipient(s) of the e-mail action (your campaign targets)</h2>
      <form class="ajax_form form-horizontal" action="<?php echo url_for('petition_edit_target', array('id' => $petition->getId())) ?>" method="post">
        <?php echo $target_form->renderHiddenFields() ?>
        <fieldset>
          <div class="global_error">
            <span id="new_petition_customise"></span>
          </div>
          <?php echo $target_form ?>
        </fieldset>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Save</button>
          <a id="edit-btn-save"class="btn submit btn-info hide" data-submit='{"edit_target":1}'>Save &amp; edit target list</a>
          <?php if ($petition->getKind() == Petition::KIND_PLEDGE): ?>
            <a class="btn submit" data-submit='{"go_pledge":1}'>Save &amp; define pledges</a>
          <?php else: ?>
            <a class="btn submit" data-submit='{"go_translation":1}'>Save &amp; go to actions texts and translations</a>
          <?php endif ?>
        </div>
      </form>
    </div>
  </div>
  <?php if (isset($target_list)): ?>
    <hr />
    <?php
    if (!$open_edit):
      $hide_edit = true;
      ?>
      <a id="edit-btn" class="btn" href="javascript:(function(){$('#edit').show();$('#edit-btn').addClass('hide');})();">Edit target-list</a>
    <?php endif ?>
  <?php endif ?>
<?php elseif (isset($campaign)): ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a></li>
      <li class="breadcrumb-item"><a href="<?php echo url_for('target_index', array('id' => $campaign->getId())) ?>">Target-lists</a></li>
      <li class="breadcrumb-item active"><?php echo $target_list->getName() ?></li>
    </ol>
  </nav>
  <?php include_partial('d_campaign/tabs', array('campaign' => $campaign, 'active' => 'targets')) ?>
<?php else: ?>
  <?php include_partial('dashboard/admin_tabs', array('active' => 'target')) ?>
<?php endif ?>
<?php if (isset($target_list)): ?>
  <div id="edit" class="row<?php if ($hide_edit): ?> hide<?php endif ?>">
    <div class="span8">
      <?php if ($target_list->isNew()): ?>
        <h2>Create Target-list</h2>
      <?php else: ?>
        <h2>Target-list: <?php echo $target_list->getName() ?></h2>
      <?php endif ?>
      <?php include_partial('form', array('form' => $form, 'csrf_token' => $csrf_token, 'petition_id' => isset($petition) ? $petition->getId() : '')) ?>
    </div>
    <div class="span4">
      <?php if (!$target_list->isNew()) include_component('target', 'members', array('target_list' => $target_list)) ?>
    </div>
    <?php if (!$target_list->isNew()): ?>
      <div class="span12">
        <h3>Meta fields</h3>
        <?php include_partial('metas', array('metas' => $metas)) ?>
        <a class="ajax_link btn btn-sm" href="<?php echo url_for('target_meta_choice', array('id' => $form->getObject()->getId())) ?>">New selector</a>
        <a class="ajax_link btn btn-sm" href="<?php echo url_for('target_meta_free', array('id' => $form->getObject()->getId())) ?>">New free text</a>
        <a class="ajax_link btn btn-sm" href="<?php echo url_for('target_meta_mapping', array('id' => $form->getObject()->getId())) ?>">New mapping</a>
        <h3>Contacts</h3>
        <?php include_component('target', 'contacts', array('target_list' => $target_list)) ?>
        <a class="ajax_link btn btn-sm" href="<?php echo url_for('target_contact_new', array('id' => $form->getObject()->getId())) ?>">New contact</a>
        <a class="ajax_link btn btn-sm" href="<?php echo url_for('target_upload', array('id' => $form->getObject()->getId())) ?>">Upload contacts</a>
        <a class="ajax_link btn btn-sm" href="<?php echo url_for('target_truncate', array('id' => $form->getObject()->getId())) ?>">Flush all data</a>
        <div id="upload_form"></div>
      </div>
    <?php endif ?>
  </div>
<?php endif; ?>

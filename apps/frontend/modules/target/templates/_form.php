<?php
/* @var $form MailingListForm */
$target_list = $form->getObject();
$action = $target_list->isNew() 
  ? url_for('target_new', array('id' => $target_list->getCampaignId())) 
  : url_for('target_edit', array('id' => $target_list->getId()));

if (!isset($petition_id)) {
  $petition_id = '';
}
?>
<form id="form" class="ajax_form form-horizontal" action="<?php echo $action ?>" method="post">
  <div class="control-group">
    <label class="control-label">Status</label>
    <div class="controls" >
      <span class="widget_text"><?php echo $target_list->getStatusName() ?></span>
      <?php if (!$target_list->isNew() && $target_list->getStatus() != MailingListTable::STATUS_ACTIVE): ?>
      <a class="btn btn-sm ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token, 'id' => $target_list->getId(), 'petition_id' => $petition_id)) ?>' href="<?php echo url_for('target_activate') ?>">activate</a>
      <?php endif ?>
      <?php if (!$target_list->isNew() && $target_list->getStatus() == MailingListTable::STATUS_ACTIVE && !$target_list->getCampaignId() && $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
      <a class="btn btn-sm ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token, 'id' => $target_list->getId())) ?>' href="<?php echo url_for('target_deactivate') ?>">deactivate</a>
      <?php endif ?>
      <?php if (!$target_list->isNew() && $target_list->getStatus() != MailingListTable::STATUS_DELETED && $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN) && !$target_list->countActions()): ?>
        <a class="btn btn-danger btn-sm ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token, 'id' => $target_list->getId())) ?>' href="<?php echo url_for('target_delete') ?>">delete</a>
      <?php endif ?>
      <?php if (!$target_list->isNew()): ?>
        <p class="">
          Used by <?php echo $target_list->countActions() ?> 
          actions<?php if ($target_list->countActionsDeleted()): ?> and <?php echo $target_list->countActionsDeleted() ?> deleted actions<?php endif ?>.
        </p>
      <?php endif ?>
    </div>
  </div>
<?php echo $form ?>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Save</button>
  </div>
</form>
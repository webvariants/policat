<div class="modal hide hidden_remove" id="contact_truncate_modal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Alert</h3>
  </div>
  <div class="modal-body">
    <p>Are you sure to <strong>irrevocably delete</strong> all contacts of the target list "<b><?php echo $name ?></b>"?</p>
    <p>Note: in case this target list is used by a pledge action, deleting the contacts will also delete all pledges received!</p>
  </div>
  <div class="modal-footer">
    <a class="btn btn-danger ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('target_truncate', array('id' => $id)) ?>">Yes. Delete all contact records</a>
    <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
  </div>
</div>
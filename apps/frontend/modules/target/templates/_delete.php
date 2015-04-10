<div class="modal hide hidden_remove" id="contact_delete_modal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Alert</h3>
  </div>
  <div class="modal-body">
    <p>Are you sure to <strong>irrevocably delete</strong> the contact record of "<b><?php echo $name ?></b>"?</p>
  </div>
  <div class="modal-footer">
    <a class="btn btn-danger ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('target_contact_delete', array('id' => $id)) ?>">Yes. Delete contact record</a>
    <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
  </div>
</div>
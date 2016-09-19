<div class="modal hide hidden_remove" id="media_file_delete_modal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Alert</h3>
  </div>
  <div class="modal-body">
    <p>Do you really want to delete the media file "<b><?php echo $name ?></b>"? This cannot be undone!</p>
  </div>
  <div class="modal-footer">
    <a class="btn btn-danger ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('media_files_delete', array('id' => $id)) ?>">Yes</a>
    <a class="btn btn-primary" data-dismiss="modal">No</a>
  </div>
</div>
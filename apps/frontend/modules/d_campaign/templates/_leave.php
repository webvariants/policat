<div class="modal hide hidden_remove" id="campaign_leave_modal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Leave camapign "<b><?php echo $name ?></b>"</h3>
  </div>
  <div class="modal-body">
    <p>Attention: If you leave now, you will lose access to any action within this campaign!</p>
  </div>
  <div class="modal-footer">
    <a class="btn btn-danger ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('campaign_leave', array('id' => $id)) ?>">Leave</a>
    <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
  </div>
</div>
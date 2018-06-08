<div class="modal hide hidden_remove" id="petition_delete_modal">
  <form class="ajax_form" action="<?php echo url_for('petition_delete_', array('id' => $id)) ?>" method="post">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Confirm deletion</h3>
  </div>
  <div class="modal-body">
    <p>
        Warning: This will delete the action "<b><?php echo $name ?></b>" including texts, widgets, counters and all activist data.
        This will include any data owned by widget-owners. Make sure to export/download your data first.
        Do you want to proceed and delete this action?
    </p>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>"/>
    <div class="control-group">
      <label for="delete_action_name" class="control-label">
        Enter the action title to confirm.
      </label>
      <div class="controls">
        <input id="delete_action_name" type="text" name="name" />
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-danger">Delete action</button>
    <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
  </div>
  </form>
</div>

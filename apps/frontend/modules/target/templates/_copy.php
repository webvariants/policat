<div class="modal hide hidden_remove" id="target_copy_modal">
  <form class="ajax_form" action="<?php echo url_for('target_copy', array('id' => $id)) ?>" method="post">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">&times;</a>
      <h3>Target-list copy</h3>
    </div>
    <div class="modal-body">
        <?php echo $form ?>
    </div>
    <div class="modal-footer">
      <a class="btn btn-secondary" data-dismiss="modal">Close</a>
      <button class="btn btn-primary" type="submit">Copy</button>
    </div>
  </form>
</div>
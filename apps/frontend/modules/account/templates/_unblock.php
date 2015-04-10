<div class="modal hide hidden_remove" id="unblock_modal">
  <form class="ajax_form" action="<?php echo url_for('unblock') ?>" method="post">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">&times;</a>
      <h3>Request unblock</h3>
    </div>
    <div class="modal-body">
        <?php echo $form ?>
    </div>
    <div class="modal-footer">
      <a class="btn" data-dismiss="modal">Close</a>
      <button class="btn btn-primary" type="submit">Send</button>
    </div>
  </form>
</div>
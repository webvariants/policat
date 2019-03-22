<div class="modal hide hidden_remove" id="resign_data_officer_modal">
  <form class="ajax_form" action="<?php echo url_for('campaign_resign_officer', array('id' => $campaign->getId())) ?>" method="post">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">&times;</a>
      <h3>Resign from Data protection officer</h3>
    </div>
    <div class="modal-body">
        <?php echo $form ?>
    </div>
    <div class="modal-footer">
      <a class="btn btn-secondary" data-dismiss="modal">Close</a>
      <button class="btn btn-primary" type="submit">Submit</button>
    </div>
  </form>
</div>
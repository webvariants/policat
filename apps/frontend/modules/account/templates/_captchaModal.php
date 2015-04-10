<div class="modal hide hidden_remove" id="captcha_modal">
  <form class="ajax_form captcha_modal" action="" method="post">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">&times;</a>
      <h3>Please verify</h3>
    </div>
    <div class="modal-body">
        <?php include_partial('account/captcha') ?>
    </div>
    <div class="modal-footer">
      <a class="btn" data-dismiss="modal">Close</a>
      <button class="btn btn-primary" type="submit">OK</button>
    </div>
  </form>
</div>
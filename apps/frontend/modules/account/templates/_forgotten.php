<?php
use_helper('I18N');
/* @var $sf_context sfContext */
?>

<div class="modal hide" id="forgotten_modal">
  <form class="ajax_form add_href" action="<?php echo url_for('password_forgotten') ?>" method="post">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">&times;</a>
      <h3>Password request</h3>
    </div>
    <div class="modal-body">
      <p>Enter the e-mail address you used when you set up your account. We will then e-mail you a link to a secure page where you can create a new password.</p>
      <?php echo $form ?>
    </div>
    <div class="modal-footer">
      <a class="btn" data-dismiss="modal">Close</a>
      <button class="btn btn-primary" type="submit">Request</button>
    </div>
  </form>
</div>l
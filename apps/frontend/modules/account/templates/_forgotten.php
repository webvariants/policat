<?php
use_helper('I18N');
/* @var $sf_context sfContext */
?>

<div class="modal fade" id="forgotten_modal" tabindex="-1" role="dialog" aria-labelledby="fogotten_modal_label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form class="ajax_form add_href" action="<?php echo url_for('password_forgotten') ?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="fogotten_modal_label">Password request</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
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
    </div>
  </div>
</div>

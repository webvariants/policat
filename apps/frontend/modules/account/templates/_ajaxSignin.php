<?php if (isset($form)): ?>
  <?php use_helper('I18N'); ?>
  <div id="login_modal" class="modal" tabindex="-1" role="dialog" data-backdrop="">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
      <form id="login_form" class="ajax_form add_href" action="<?php echo url_for('ajax_signin') ?>" method="post">
          <div class="modal-header">
            <h5 class="modal-title">Login<?php if (isset($registerForm)): ?> | Join<?php endif ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <?php if ($sf_context->getModuleName() == 'd_home' && $sf_context->getActionName() == 'index'): ?>
                <input type="hidden" name="target" value="dashboard" />
              <?php endif ?>
              <?php echo $form ?>
              <small><a class="ajax_link" href="<?php echo url_for('password_forgotten') ?>">Forgot password?</a></small>
          </div>
          <div class="modal-footer" style="flex-wrap: wrap-reverse">
              <?php if (isset($registerForm)): ?>
                <a class="btn btn-link login-register-switch" href="javascript:;">Register new account</a>
              <?php endif ?>
              <div>
                  <a class="btn btn-link" data-dismiss="modal" href="javascript:;">Cancel</a>
                  <button class="btn btn-primary" type="submit">Login</button>
              </div>
          </div>
      </form>
      <?php if (isset($registerForm)): ?>
        <form style="display: none;" id="register_form" class=" hide ajax_form register-form" action="<?php echo url_for('register') ?>" method="post" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">Login | Join</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo $registerForm ?>
                <fieldset><div class="control-group"><div class="controls"><?php include_partial('account/captcha') ?></div></div></fieldset>
                <div class="register-success"></div>
            </div>
            <div class="modal-footer">
                <a class="btn login-register-switch">Cancel</a>
                <button class="btn btn-primary disable-on-captcha" type="submit">Register</button>
            </div>
        </form>
      <?php endif ?>
  </div>
</div>
</div>
<?php endif; ?>

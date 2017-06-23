<?php if (isset($form)): ?>
  <?php use_helper('I18N'); ?>
  <div class="modal hide" id="login_modal">
      <form id="login_form" class="ajax_form add_href" action="<?php echo url_for('ajax_signin') ?>" method="post">
          <div class="modal-header">
              <a class="close" data-dismiss="modal">&times;</a>
              <h3>Login<?php if (isset($registerForm)): ?> | Join<?php endif ?></h3>
              <?php if ($sf_context->getModuleName() == 'd_home' && $sf_context->getActionName() == 'index'): ?>
                <input type="hidden" name="target" value="dashboard" />
              <?php endif ?>
          </div>
          <div class="modal-body">
              <?php echo $form ?>
              <br /><small><a class="ajax_link" href="<?php echo url_for('password_forgotten') ?>">Forgot password?</a></small>
              <?php if (isset($registerForm)): ?>
                <br /><a class="btn btn-mini btn-info top10 login-register-switch">Register new account</a>
              <?php endif ?>
          </div>
          <div class="modal-footer">
              <a class="btn" data-dismiss="modal">Cancel</a>
              <button class="btn btn-primary" type="submit">Login</button>
          </div>
      </form>
      <?php if (isset($registerForm)): ?>
        <form id="register_form" class=" hide ajax_form register-form" action="<?php echo url_for('register') ?>" method="post" autocomplete="off">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">&times;</a>
                <h3>Login | Join</h3>
            </div>
            <div class="modal-body">
                <div class="register-form-row">
                    <div class="register-form-side">
                        <?php echo $registerForm->renderHiddenFields() ?>
                        <?php echo $registerForm->renderRows('email_address', 'password', 'password_again') ?>
                    </div>
                    <div class="register-form-side">
                        <?php echo $registerForm->renderRows('first_name', 'last_name', 'organisation') ?>
                    </div>
                </div>
                <?php echo $registerForm['terms']->renderRow() ?>
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
<?php endif; ?>
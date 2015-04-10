<?php if (!$sf_user->human()): ?>
  <div class="recaptcha" data-public_key="<?php echo sfConfig::get('app_recaptcha_public') ?>" data-url="<?php echo url_for('captcha') ?>">please wait</div>
<?php endif ?>
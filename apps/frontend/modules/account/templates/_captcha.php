<?php if (!$sf_user->human()): ?>
  <div id="recaptcha" class="<?php if (isset($onLoad) && $onLoad): ?>captcha-onload<?php endif ?>" data-sitekey="<?php echo sfConfig::get('app_recaptcha_public') ?>" data-url="<?php echo url_for('captcha') ?>"></div>
  <?php
 endif;
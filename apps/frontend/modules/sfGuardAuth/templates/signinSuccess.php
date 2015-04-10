<?php use_helper('I18N') ?>

<div class="page-header">
  <h1>Login</h1>
</div>
<form action="<?php echo url_for('sf_guard_signin') ?>" method="post">
  <?php echo $form ?>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit"><?php echo __('Signin', null, 'sf_guard') ?></button>
    <a class="btn" href="<?php echo url_for('dashboard') ?>">Cancel</a>
  </div>
</form>
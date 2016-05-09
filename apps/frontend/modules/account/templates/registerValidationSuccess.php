<?php
/* @var $user sfGuardUser */
?>
<div class="page-header">
  <h1>Create new account</h1>
</div>
<?php if (isset($user)): ?>
<p>User <?php echo $user->getFullName() ?> validated. You may <a data-toggle="modal" href="#login_modal" href="<?php echo url_for('ajax_signin') ?>">login</a> now.</p>
<?php if (isset($widgets_connected) && $widgets_connected): ?>
<p><?php echo $widgets_connected ?> existing widget(s) have been connected with your account.</p>
<?php endif ?>
<?php else:?>
<p>Validation failed.</p>
<?php endif; ?>

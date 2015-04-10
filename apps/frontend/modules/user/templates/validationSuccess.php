<?php /* @var $user sfGuardUser */ ?>
<div class="page-header">
  <h1>User activation</h1>
</div>
<?php if (isset($user)): ?>
  <p>
    To activate "<?php echo $user->getFullName() ?>" with Login "<?php echo $user->getEmailAddress() ?>" please enter
    a password.
  </p>
  <form id="password_form" class="ajax_form form-horizontal" action="<?php echo url_for('user_validation', array('id' => $user->getId(), 'code' => $user->getValidationCode())) ?>" method="post" autocomplete="off">
    <?php echo $form ?>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Save</button>
    </div>
  </form>
<?php else: ?>
  <p>User not found.</p>
<?php endif ?>
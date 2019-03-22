<?php include_partial('dashboard/admin_tabs', array('active' => 'users')) ?>
<div id="user_form">
  <?php
  $is_new = $form->getObject()->isNew();
  $action = $is_new ? url_for('user_new') : url_for('user_edit', array('id' => $form->getObject()->getId()))
  ?>
  <form class="ajax_form form-horizontal" action="<?php echo $action ?>" method="post" autocomplete="off">
    <fieldset>
      <legend class="pull-left">Profile</legend>
      <div class="row">
        <div class="col-md-6">
          <?php echo $form->renderHiddenFields() ?>
          <?php
          echo $form->renderRows('email_address');
          if (!$is_new) echo $form->renderRows('password', 'password_again');
          echo $form->renderRows('first_name', 'last_name', 'phone', 'language_id') ?>
          <?php if ($is_new): ?>
          <p>An E-mail will be sent to the user to activate and set the password.</p>
          <?php endif ?>
        </div>
        <div class="col-md-6">
          <?php echo $form->renderRows('organisation', 'website', 'street', 'post_code', 'city', 'country', 'vat', 'mobile') ?>
        </div>
      </div>
    </fieldset>
    <fieldset>
      <legend class="pull-left">Admin only settings</legend>
      <div class="row">
        <div class="col-md-6">
          <?php if (!$is_new) echo $form->renderRows('is_active') ?>
        </div>
        <div class="col-md-6">
          <?php echo $form->renderRows('groups_list') ?>
        </div>
      </div>
    </fieldset>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Save</button>
      <a class="btn btn-danger" href="<?php echo url_for('user_idx') ?>">Cancel</a>
      <?php if (!$is_new): $user = $form->getObject(); /* @var $user sfGuardUser */?>
        <?php if (!$user->hasPermission(myUser::CREDENTIAL_ADMIN)): ?>
          <?php if ($user->hasPermission(myUser::CREDENTIAL_USER)): ?>
            <a class="btn btn-danger ajax_link" href="<?php echo url_for('user_block', array('id' => $user->getId())) ?>">Block</a>
          <?php else: ?>
            <a class="btn btn-secondary ajax_link" href="<?php echo url_for('user_unblock', array('id' => $user->getId())) ?>">Unblock</a>
          <?php endif ?>
        <?php endif ?>
        <a class="btn btn-danger ajax_link" href="<?php echo url_for('user_delete', array('id' => $form->getObject()->getId())) ?>">Delete</a>
      <?php endif ?>
    </div>
  </form>
</div>
<?php if (isset($user)) {
  include_component('account', 'membership', array('user' => $user));
} ?>
<p><a class="btn btn-secondary" href="<?php echo url_for('user_idx') ?>">Back</a></p>

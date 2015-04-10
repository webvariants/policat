<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li class="active">Profile</li>
</ul>
<div class="page-header">
  <h1>Your profile</h1>
</div>
<div id="profile_form">
  <form class="ajax_form form-horizontal" action="<?php echo url_for('profile') ?>" method="post" autocomplete="off">
    <div class="row">
      <div class="span6">
        <?php echo $form->renderHiddenFields() ?>
        <?php echo $form->renderRows('email_address', 'password', 'password_again', 'first_name', 'last_name', 'phone', 'language_id') ?>
      </div>
      <div class="span6">
        <?php echo $form->renderRows('organisation', 'website', 'street', 'post_code', 'city', 'country', 'mobile') ?>
      </div>
    </div>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Save</button>
      <a class="btn" href="<?php echo url_for('homepage') ?>">Cancel</a>
    </div>
  </form>
</div>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Profile</li>
  </ol>
</nav>
<div class="page-header">
    <h1>Your profile</h1>
</div>
<div id="profile_form">
    <form class="ajax_form form-horizontal" action="<?php echo url_for('profile') ?>" method="post" autocomplete="off">
        <div class="row">
            <div class="col-md-6">
                <?php echo $form->renderHiddenFields() ?>
                <?php echo $form->renderRows('email_address', 'password', 'password_again', 'first_name', 'last_name', 'phone', 'mobile', 'language_id') ?>
            </div>
            <div class="col-md-6">
                <?php echo $form->renderRows('organisation', 'vat', 'website', 'street', 'post_code', 'city', 'country') ?>
            </div>
            <div class="col-md-12">
              I have read and accepted the <a target="_blank" href="<?php echo url_for('terms') ?>">terms of service</a>. I will handle any activist data in accordance with the privacy policy, as defined in my campaigns and actions.
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-secondary" href="<?php echo url_for('homepage') ?>">Cancel</a>
        </div>
    </form>
</div>
<?php include_component('account', 'membership', array('user' => $form->getObject(), 'join' => true)) ?>

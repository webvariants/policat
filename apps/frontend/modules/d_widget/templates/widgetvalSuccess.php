<?php /* @var $sf_user myUser  */ ?>
<div class="page-header">
  <h1>Widget Validation and Ownership</h1>
</div>
<?php if (isset($idcode)): ?>
  <?php if ($sf_user->isAuthenticated()): $user = $sf_user->getGuardUser() ?>
    <p>You are authenticated as <?php echo $user->getFullName() ?>.</p>
    <?php if (isset($csrf_token)): ?>
    <div id="connect">
      <p>To connect the Widget (ID <?php echo $id ?>) with your account press the button below</p>
      <div class="well">
        <a class="btn btn-primary btn-large ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('widgetval', array('code' => $idcode)) ?>">Connect widget with account</a>
      </div>
    </div>
    <?php endif ?>
  <?php else: ?>
    <p>
      To connect your widget with an account you have to 
      <a rel="nofollow" data-toggle="modal" href="#login_modal" href="<?php echo url_for('ajax_signin') ?>">login</a> or 
      <a href="<?php echo url_for('register') ?>?widgetval=1">register</a> first.
    </p>
  <?php endif ?>
<?php else: ?>
  <p>Wrong code</p>
<?php endif ?>

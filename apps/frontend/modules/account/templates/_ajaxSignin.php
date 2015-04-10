<?php use_helper('I18N');
if (!isset($form)) {
  $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin');
  $form = new $class();
}
/* @var $sf_context sfContext */
?>

<div class="modal hide" id="login_modal">
  <form class="ajax_form add_href" action="<?php echo url_for('ajax_signin') ?>" method="post">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">&times;</a>
      <h3>Login</h3>
      <?php if ($sf_context->getModuleName() == 'd_home' && $sf_context->getActionName() == 'index'): ?>
      <input type="hidden" name="target" value="dashboard" />
      <?php endif ?>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="span3">
        <?php echo $form ?>
          <br /><small><a class="ajax_link" href="<?php echo url_for('password_forgotten') ?>">I have forgotten my password.</a></small>
        </div>
        <?php if (StoreTable::value(StoreTable::MENU_JOIN) && StoreTable::value(StoreTable::REGISTER_ON)): ?>
        <div class="span2">
          or <a href="<?php echo url_for('register') ?>">join</a>
        </div>
        <?php endif ?>
      </div>
    </div>
    <div class="modal-footer">
      <a class="btn" data-dismiss="modal">Close</a>
      <button class="btn btn-primary" type="submit"><?php echo __('Signin', null, 'sf_guard') ?></button>
    </div>
  </form>
</div>
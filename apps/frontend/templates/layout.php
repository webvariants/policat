<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="<?php echo public_path('favicon.ico') ?>" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body id="body_policat_portal">
    <div id="main_menu">
      <a href="<?php echo url_for('main')?>">Home</a>&bull;
      <?php if ($sf_user->isAuthenticated()): ?>
        <a href="<?php echo url_for('sf_guard_signout') ?>">logout</a> (<?php echo $sf_user->getUsername() ?>)
      <?php else: ?>
        <a href="<?php echo url_for('sf_guard_signin') ?>">login</a>
      <?php endif ?>
        &bull; <?php echo (sfConfig::get('sf_environment')) ?>
    </div>
    <?php echo $sf_content ?>
  </body>
</html>

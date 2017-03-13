<?php
/* @var $sf_content string */
/* @var $sf_user myUser */
?><!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    $portal_name = StoreTable::value(StoreTable::PORTAL_NAME);
    $title = $sf_response->getTitle();
    $sf_response->setTitle(($title ? $title . ' - ' : '') . $portal_name);
    $sf_response->addMeta('description', StoreTable::value(StoreTable::PORTAL_META_DESCRIPTION));
    $sf_response->addMeta('keywords', StoreTable::value(StoreTable::PORTAL_META_KEYWORDS));
    include_http_metas();
    include_metas();
    include_title() ?>
    <link rel="shortcut icon" href="<?php echo public_path('favicon.ico') ?>" />
    <?php include_stylesheets(); include_javascripts() ?>
  </head>
  <body class="container">
    <header class="row">
      <div class="span3">
        <a href="<?php echo url_for('homepage') ?>"><img src="<?php echo image_path('store/' . StoreTable::value(StoreTable::PORTAL_LOGO)) ?>?<?php echo StoreTable::version(StoreTable::PORTAL_LOGO) ?>" alt="<?php echo $portal_name ?>" /></a>
      </div>
      <nav class="navbar span9"><?php include_component('d_home', 'menu', array('a' => $sf_user->isAuthenticated() ? 1 : 0, 'b' => $sf_user->hasCredential('homepage') ? 1 : 0)) ?></nav>
    </header>
    <?php echo $sf_content ?>
    <?php include_component('d_home', 'footer') ?>
    <?php include_component('account', 'ajaxSignin', array('a' => $sf_user->isAuthenticated() ? 1 : 0)) ?>
    <div id="waiting"><b></b><i></i><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div>
  </body>
</html>
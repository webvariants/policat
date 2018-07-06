<?php
/* @var $sf_content string */
/* @var $sf_user myUser */
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
    <?php include_stylesheets() ?>
  </head>
  <body>
    <?php include_component('d_home', 'menuB4', array('a' => $sf_user->isAuthenticated() ? 1 : 0, 'b' => $sf_user->hasCredential('homepage') ? 1 : 0)) ?>
    <?php echo $sf_content ?>
    <?php include_component('d_home', 'footerB4') ?>
    <?php include_component('account', 'ajaxSignin', array('a' => $sf_user->isAuthenticated() ? 1 : 0)) ?>
    <?php include_javascripts() ?>
  </body>
</html>

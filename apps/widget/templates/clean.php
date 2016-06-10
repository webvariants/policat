<?php
/* @var $sf_content string */
?><!DOCTYPE html>
<html>
  <head>
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
    <?php echo $sf_content ?>
    <div id="waiting"><b></b><i></i><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div>
  </body>
</html>
<?php
/* @var $sf_content string */
/* @var $sf_user myUser */
?><!DOCTYPE html>
<html>
  <head>
    <?php
    use_helper('I18N');
    $portal_name = StoreTable::value(StoreTable::PORTAL_NAME);
    $title = $sf_response->getTitle();
    $sf_response->setTitle(($title ? $title . ' - ' : '') . $portal_name);
    $sf_response->addMeta('description', StoreTable::value(StoreTable::PORTAL_META_DESCRIPTION));
    $sf_response->addMeta('keywords', StoreTable::value(StoreTable::PORTAL_META_KEYWORDS));
    include_http_metas();
    include_metas();
    include_title()
    ?>
    <link rel="shortcut icon" href="<?php echo public_path('favicon.ico') ?>" />
    <?php
    include_stylesheets();
//    include_javascripts();
    ?>
  </head>
  <body class="container">
    <div class="modal">
      <div class="modal-header"><?php echo __('Your email is being validated, please wait!') ?></div>
      <div class="modal-body">
        <div class="progress progress-striped active">
          <div id="bar" class="bar" style="width: 0%;"></div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      var bar = document.getElementById('bar');
      var i = 0;
      var interval = window.setInterval(function() {
        i += 4;
        bar.style.width = i + '%';
        if (i >= 100) {
          window.clearInterval(interval);
          window.location.replace(<?php echo json_encode(strtr($landing_url, array('>' => '', '<' => ''))) ?>);
        }
      }, 70);
    </script>
    <div id="waiting"><b></b><i></i><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div>
  </body>
</html>
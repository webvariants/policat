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
    <div class="container<?php if (get_slot('fluid', false)): ?>-fluid<?php endif ?>">
        <?php echo $sf_content ?>
    </div>
    <?php include_component('d_home', 'footerB4') ?>
    <?php include_component('account', 'ajaxSignin', array('a' => $sf_user->isAuthenticated() ? 1 : 0)) ?>
    <?php include_javascripts() ?>
    <?php if(StoreTable::value(StoreTable::INSTANT_CHAT_ENABLE)) { ?>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/<?php echo StoreTable::value(StoreTable::INSTANT_CHAT_SITE_ID) ?>/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    <?php if($sf_user->isAuthenticated()) { ?>
      var user = document.getElementById('tawk-user');
      if (user) {
        Tawk_API.onLoad = function(){
          Tawk_API.setAttributes({
            name : JSON.parse(user.dataset.tawk).name,
            email: JSON.parse(user.dataset.tawk).email,
            hash : JSON.parse(user.dataset.tawk).hash
          }, function(error){});
        };
      }
		<?php } ?>
		</script>
		<!--End of Tawk.to Script-->
	<?php } ?>
  </body>
</html>

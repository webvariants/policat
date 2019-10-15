<?php
/* @var $sf_content string */
/* @var $sf_user myUser */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
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

<body>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark no-print">
        <?php $logo = StoreTable::value(StoreTable::PORTAL_LOGO); ?>
        <?php if ($logo): ?>
        <a class="navbar-brand navbar-brand-img" href="<?php echo url_for('homepage') ?>">
            <img src="<?php echo image_path('store/' . $logo) ?>?<?php echo StoreTable::version(StoreTable::PORTAL_LOGO) ?>"
                alt="<?php echo Util::enc(StoreTable::value(StoreTable::PORTAL_NAME)) ?>" />
        </a>
        <?php else: ?>
        <a class="navbar-brand" href="<?php echo url_for('homepage') ?>">
            <?php echo Util::enc(StoreTable::value(StoreTable::PORTAL_NAME)) ?>
        </a>
        <?php endif ?>
        <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link"href="<?php echo url_for('homepage') ?>">Home</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <?php echo $sf_content ?>
    </div>
    <?php include_component('home', 'footer') ?>
</body>

</html>
<!-- <?php echo gmdate('Y-m-d H:i:s') ?> GMT -->
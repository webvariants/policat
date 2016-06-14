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
            <div class="modal-header">
                Success
            </div>
            <div class="modal-body">
                You have been successfully unsubscribed.
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="<?php echo url_for('homepage') ?>">OK</a>
            </div>
        </div>
    </body>
</html>
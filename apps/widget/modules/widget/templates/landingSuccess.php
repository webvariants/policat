<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
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
    // include_javascripts();
    ?>
</head>

<body class="modal-open">
    <div class="modal show" tabindex="-1" role="dialog" aria-hidden="true" style="display:block">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo __('Your email is being validated, please wait!') ?></h5>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div id="bar" class="progress-bar" style="width:0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop show"></div>
    <script type="text/javascript">
    var bar = document.getElementById('bar');
    var i = 0;
    var interval = window.setInterval(function() {
        i += 4;
        bar.style.width = i + '%';
        if (i >= 100) {
            window.clearInterval(interval);
            window.location.replace(
                <?php echo json_encode(strtr($landing_url, array('>' => '', '<' => ''))) ?>);
        }
    }, 70);
    </script>
</body>

</html>
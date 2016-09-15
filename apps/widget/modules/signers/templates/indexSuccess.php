<?php use_helper('I18N'); ?>
<?php
/* @var $sf_content string */
?><!DOCTYPE html>
<html lang="<?php echo $lang ?>">
    <head>
        <?php
        $title = $sf_response->getTitle();
        $sf_response->setTitle(($title ? $title . ' - ' : '') . __('All signers'));
        include_http_metas();
        include_metas();
        include_title()
        ?>
        <link rel="shortcut icon" href="<?php echo public_path('favicon.ico') ?>" />
        <?php
        include_stylesheets();
        include_javascripts();
        $data['locale'] = $lang;
        $data['translations'] = array(
            'date' => __('Date'),
            'name' => __('Name'),
            'city' => __('City'),
            'country' => __('Country')
        );
        ?>
        <style type="text/css">
            body {
                background: <?php echo $background_color ?>;
                color: <?php echo $color ?>;
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .pagination .active a {
                color: <?php echo $color ?>;
            }

            a, a:hover {
                color: <?php echo $button_color ?>;
            }
        </style>
    </head>
    <body class="container">
        <?php if (!$disabled): ?>
          <?php if ($text): ?>
            <?php echo UtilMarkdown::transform($text) ?>
          <?php else: ?>
            <h1><?php echo Util::enc($title) ?></h1>
            <h2><?php echo __('All signers') ?></h2>
          <?php endif ?>
          <div id="signers" class="signers-list" data-signers="<?php echo Util::enc(json_encode($data)) ?>"></div>
          <div class="pagination"><ul id="pager"></ul></div>
        <?php else: ?>
          <h2><?php echo __('All signers') ?></h2>
          <p>Disabled for this action.</p>
        <?php endif ?>
        <div id="waiting"><b></b><i></i><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div>
    </body>
</html>
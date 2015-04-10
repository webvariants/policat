<?php echo (file_get_contents(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'policat_counterbar.js')); ?>
counterBar(<?php echo $widgetid ?>, <?php echo json_encode(array(
    'target'   => $target,
    'percent'  => ceil($count*100/$target),
    'stylings' => $stylings,
    'markup'   => $markup
)) ?>);
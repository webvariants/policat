<?php
	use_helper('I18N');
	$css = array(
		'padding'    => 0,
		'margin'     => 0,
		'border'     => 0,
		'background' => 'transparent',
		'position'   => 'relative',
    'font-family' => 'Lucida Sans Unicode',
    'text-transform' => 'none',
    'font-weight' => 'normal',
    'line-height' => '20px',
	);
?>

<div id="counterbar-<?php echo $widgetid ?>" <?php UtilPolicat::style($css, array('width' => 'body_width', 'background' => 'bar_bg_color', 'overflow' => 'hidden', 'padding' => '5px')) ?>>
	<div id="count_target-<?php echo $widgetid ?>" <?php UtilPolicat::style($css, array('font-size' => '9px', 'line-height' => '12px', 'color' => 'body_color')) ?>>
		<?php echo number_format($count, 0, '.', ',') . ' ' . __('people so far') ?>
		<span <?php UtilPolicat::style($css, array('float' => 'right', 'line-height' => '12px')) ?>><?php echo number_format($target, 0, '.', ',') ?></span>
	</div>
	<div id="count-<?php echo $widgetid ?>" <?php UtilPolicat::style($css, array('font-size' => '12px', 'background' => 'counter_bg_color', 'height' => '18px')) ?>>
		<div id="coloredbar-<?php echo $widgetid ?>" <?php UtilPolicat::style($css, array('background' => 'line_bg_color', 'height' => '18px', 'position' => 'absolute', 'left' => '0', 'top' => '0', 'width' => '0')) ?>></div>
		<span id="counternumber-<?php echo $widgetid ?>" <?php UtilPolicat::style($css, array('position' => 'absolute', 'padding' => '0 2px', 'top' => '0', 'left' => '0', 'color' => 'number_text_color')) ?>><?php echo number_format($count, 0, '.', ',') ?></span>
	</div>
</div>
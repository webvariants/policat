<script type="text/javascript">
	var url            = <?php echo json_encode(url_for('counterbar_colors')) ?>;
	var inputs         = {
		body_width     : {label : 'Widget-width:',        name: 'body_width',       element: '',              attribute: '' ,          color : false},
		body_color     : {label : 'Font-clor:',           name: 'body_color',       element: '#count_target', attribute: 'color' ,     color : true },
		button_color   : {label : 'Bar-color:',           name: 'line_bg_color',    element: '#coloredbar',   attribute: 'background', color : true },
		bg_left_color  : {label : 'Bar-backgroundcolor:', name: 'counter_bg_color', element: '#count',        attribute: 'background', color : true },
		bg_right_color : {label : 'Backgroundcolor:',     name: 'bar_bg_color',     element: '#counterbar',   attribute: 'background', color : true }
	};
	var messages = {
		valid_id      : 'Please insert a valid Widget-ID.',
		general_error : 'An Error occured. Please try again later.',
		no_id         : 'Please insert a Widget-ID.',
		code_help     : 'Copy these lines and insert it into your HTML code.',
		generate_code : 'Generate Code'
	};
</script>
<?php $sf_response->setTitle('policat.org - Generate counter bar') ?>
<h2>Generate counter bar</h2>
<div id="counterbar_generator">
	<form class="form-inline" id="generator" method="post" action="">
			<label for="widget_id">Widget-ID:</label><input type="text" name="widget_id" id="widget_id" />
			<input class="btn" type="submit" name="submit_widget" value="Continue"/>
	</form>
</div>
<div id="counterbar_example">
	<h3>Example preview</h3><span class="help">(This example does not show the current count of your e-action.)</span>
	<div id="counterbar">
		<div id="count_target">
			49 people so far
			<span>100</span>
		</div>
		<div id="count">
			<div id="coloredbar"></div>
			<span id="counternumber">49</span>
		</div>
	</div>

</div>
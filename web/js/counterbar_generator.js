$(function() {
	function buildInput(name, value) {
		color = inputs[name]['color'] ? '' : 'style="background:' + value + ';"';
		return '<div class="line"><label for="' + inputs[name]['name'] + '">' + inputs[name]['label'] + '</label><input type="text" id="' + inputs[name]['name'] + '" name="' + inputs[name]['name'] + '" value="' + value + '" ' + color + ' alt="' + name + '" /></div>'

	}

	function setErrorText(text) {
		var $p = $('.line.error');

		if ($p.length)
			$p.html(text);
		else
			$('form#generator').before('<p class="line error">' + text + '</p>');
	}

	$('form').live('submit', function() {
		var $form     = $(this);
		var widget_id = '';
		if ($form.is('#generator')) {
			$('#options .line, div.line.code, p.line.error').remove();
			$('#counterbar_example').hide();
			widget_id = $('#widget_id').val();
			if (widget_id != '') {
				var data = $form.serializeArray();
				$.ajax({
					url: url,
					data: data,
					dataType: 'json',
					success: function(json) {
						if (json.status) {
							$form.after('<form id="options" action="" method="post"></form>');
							var stylings = eval('(' + json.stylings + ')'); // sorry

							var $options = $('#options');
							$options.append(buildInput('body_width', '250px'))
							$('#body_width').keyup(function() {
								if ($('#code').length) $('#options').submit();
							});

							for(var i in stylings) {
								if (inputs[i] != undefined) {
									$options.append(buildInput(i, stylings[i]));

									if (inputs[i]['color']) {
										var myPicker = new jscolor.color(document.getElementById(inputs[i]['name']), {})
										var color    = stylings[i].substr(1);

										myPicker.fromString(color);

										$('#' + inputs[i]['name']).change(function() {
											var $this = $(this);
											var alt   = $this.attr('alt');
											$(inputs[alt]['element']).css(inputs[alt]['attribute'], $this.val());
											if ($('#code').length) $('#options').submit();
										});
									}

									if (inputs[i]['element'] != '') {
										$(inputs[i]['element']).css(inputs[i]['attribute'], stylings[i]);
									}
								}
							}

							$options.append('<div class="line"><input class="btn" type="submit" name="generate" id="generate" value="' + messages.generate_code + '" /></div>');

							$('#counterbar_example').show();
						} else {
							setErrorText(messages.valid_id);
						}
					},
					error: function() {
						setErrorText(messages.general_error);
					}
				});
			} else
				setErrorText(messages.no_id);
		}

		if ($form.is('#options')) {
			widget_id = $('#widget_id').val();
			if (widget_id != '') {
				var code = "<script type=\"text/javascript\">\n\tif (counterbar == undefined) var counterbar = [];\n\tcounterbar[" + widget_id + "] = [];";
				$('input[type=text]', $form).each(function() {
					var $input = $(this);
					var val    = $input.val();

					if (val != '')
						code += "\n\tcounterbar[" + widget_id + "]['" + $input.attr('name') + "'] = \"" + $input.val() + "\";";
				});
				code += "\n</script>\n";

				code += "<script type=\"text/javascript\" src=\"" + window.location.href + "/" + widget_id + "\"></script>";

				var textarea = $('#code');
				if (textarea.length) {
					textarea.val(code);
				} else {
					$('#counterbar_example').after('<div class="line code"></div>');
					var $div = $('div.line.code');
					$div.append('<p class="line">' + messages.code_help + '</p>');
					$div.append('<div class="line"><textarea name="code" id="code" rows="9" cols="114">' + code + '</textarea></div>');
				}

			} else
				setErrorText(messages.no_id);
		}

		return false;
	});
});
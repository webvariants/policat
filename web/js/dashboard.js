/* global showdown */

function initRecaptcha(onLoad) {
	var recaptchas = $('#recaptcha' + (onLoad ? '.captcha-onload' : ''));
	if (recaptchas.length) {
		$.ajax({
			url: 'https://www.google.com/recaptcha/api.js?onload=onCaptchaReady&render=explicit',
			dataType: "script",
			cache: true
		});
	}
}

function onCaptchaReady() {
	var sitekey = $('#recaptcha').data('sitekey');
	$('.disable-on-captcha').prop('disabled', true);
	if (sitekey) {
		grecaptcha.render($('#recaptcha')[0], {
			sitekey: sitekey,
			callback: onCaptchaSubmit,
		});
	}
}

function onCaptchaSubmit(response) {
	var recaptcha = $('#recaptcha');
	$.ajax({
		url: recaptcha.data('url'),
		type: 'POST',
		data: [{name: 'response', value: response}],
		success: function (data) {
			if (data.success) {
				$('.disable-on-captcha').prop('disabled', false);
				recaptcha.remove();
			}
		}
	});
}

var tryEdits = function (prefix) {
	if (!prefix) {
		prefix = '';
	}

	function previewParser(template) {
		return function (content) {
			showdown.setOption('smoothLivePreview', true);
			var converter = new showdown.Converter();
			var html = converter.makeHtml(content);
			if (template === 'email') {
				html = '<link type="text/css" rel="stylesheet" href="/css/email.css" /><div class="spacer10"></div><div class="main-out"><div class="main-in"><div class="main-start"></div>' + html + '</div></div><div class="spacer10"></div>';
			} else {
				html = '<style>body { font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 14px; }</style>' + html;
			}
			return html;
		};
	}

	function markdownTitle(markItUp, char) {
		var heading = '';
		var n = $.trim(markItUp.selection || markItUp.placeHolder).length;
		for (var i = 0; i < n; i++) {
			heading += char;
		}
		return '\n' + heading;
	}

	var defaultSet = [
		{name: 'First Level Heading', key: '1', placeHolder: 'Your title here...', closeWith: function (markItUp) {
				return markdownTitle(markItUp, '=')
			}},
		{name: 'Second Level Heading', key: '2', placeHolder: 'Your title here...', closeWith: function (markItUp) {
				return markdownTitle(markItUp, '-')
			}},
		{name: 'Heading 3', key: '3', openWith: '### ', placeHolder: 'Your title here...'},
		{name: 'Heading 4', key: '4', openWith: '#### ', placeHolder: 'Your title here...'},
		{name: 'Heading 5', key: '5', openWith: '##### ', placeHolder: 'Your title here...'},
		{name: 'Heading 6', key: '6', openWith: '###### ', placeHolder: 'Your title here...'},
		{separator: '---------------'},
		{name: 'Bold', key: 'B', openWith: '**', closeWith: '**'},
		{name: 'Italic', key: 'I', openWith: '_', closeWith: '_'},
		{separator: '---------------'},
		{name: 'Bulleted List', openWith: '- '},
		{name: 'Numeric List', openWith: function (markItUp) {
				return markItUp.line + '. ';
			}},
		{separator: '---------------'},
		{name: 'Picture', key: 'P', replaceWith: '![[![Alternative text]!]]([![Url:!:http://]!] "[![Title]!]")'},
		{name: 'Link', key: 'L', openWith: '[', closeWith: ']([![Url:!:http://]!] "[![Title]!]")', placeHolder: 'Your text to link here...'},
//		{separator: '---------------'},
		{name: 'Quotes', openWith: '> '},
//		{name: 'Code Block / Code', openWith: '(!(\t|!|`)!)', closeWith: '(!(`)!)'},
		{separator: '---------------'},
		{name: 'Preview', call: 'preview', className: "preview"}
	];

	$(prefix + 'textarea.markdown').each(function () {
		var textarea = $(this);
		var extraSets = [textarea.data('markup-set-1'), textarea.data('markup-set-2'), textarea.data('markup-set-3')];
		var markupSet = defaultSet.slice();
		var template = null;
		var extraSet;

		for (var i = 0; i < extraSets.length; i++) {
			extraSet = extraSets[i];
			if (extraSet) {
				while (extraSet.length) {
					markupSet.push(extraSet.shift());
				}
			}
		}

		if (textarea.is('.email-template')) {
			template = 'email';
		}

		try {
			textarea.markItUp({
				previewParser: previewParser(template),
				previewAutoRefresh: true,
				onShiftEnter: {keepDefault: false, openWith: '\n\n'},
				markupSet: markupSet,
				resizeHandle: !textarea.is('.highlight'),
				previewTemplatePath: '/js/preview.html'
			});
		} catch (e) {
		}
	});
	try {
		$(prefix + 'textarea.elastic, ' + prefix + 'textarea.markItUpEditor:not(.highlight)').elastic();
	} catch (e) {
	}
	try {
		$('textarea.highlight').each(function () {
			var that = $(this);
			var help = that.hasClass('markItUpEditor') ? that.parents('.markItUp').parent() : that;
			help = help.next('.help-block').text();
			if (help)
				that.highlightTextarea({'words': help.match(/#[^#, ]+#/g)});

			if (that.hasClass('elastic')) {
				that.parents('.highlightTextarea').addClass('elastic-fix');
			}
		});
	} catch (e) {
	}

	if ($.fn.chosen != undefined) {
		try {
			$(prefix + 'select:not(.no-chosen)').chosen({'allow_single_deselect': true});
		} catch (e) {
		}

		$('.show-before-chosen-init').removeClass('show-before-chosen-init');
	}
	if ($.fn.select2 != undefined) {
		try {
			$(prefix + 'select.select2').select2();
		} catch (e) {
		}
		try {
			$(prefix + 'input.select2sort').each(function () {
				var input = $(this);
				var data = input.data('tags');
				var tags = [];
				var maximumSelectionSize = input.attr('data-maximumSelectionSize');
				maximumSelectionSize = maximumSelectionSize ? parseInt(maximumSelectionSize) : 0;
				for (var k in data) {
					tags.push({id: k, text: data[k]});
				}
				input.select2({
					data: tags,
					multiple: true,
					maximumSelectionSize: maximumSelectionSize
				});

				input.select2('container').find('ul.select2-choices').sortable({
					containment: 'parent',
					start: function () {
						input.select2('onSortStart');
					},
					update: function () {
						input.select2('onSortEnd');
					}
				});
			});
		} catch (e) {
		}
	}

	$(prefix + '.add_popover').each(function () {
		var $this = $(this);
		var placement = $this.hasClass('popover_left') ? 'left' : 'right';
		var trigger = $this.hasClass('popover_hover') ? 'hover' : 'focus';
		if ($this.is('button')) {
			trigger = 'hover';
			placement = 'top';
		} else if (!$this.hasClass('popover_left') && $this.hasClass('large')) {
			placement = 'top';
		}
		if (!$this.next().hasClass('chosen-container')) {
			$this.popover({trigger: trigger, placement: placement});
		} else {
			$('input', $this.next('div')).popover({trigger: trigger, content: $this.attr('data-content'), placement: placement});
		}
	});

	$(prefix + '.add_tooltip').tooltip();
};

var load_href = window.location.href;
var wvAjax_gen_id = 1;
var wvAjax = function (options) {
	var $this = $(this);
	var url = '';
	var cache = false;
	var data = [];
	var type = 'post';
	var add_data = function (extra_data) {
		$.each(extra_data, function (k, v) {
			data.push({'name': k, 'value': v});
		});
	};
	var iframe = false;
	if (options['originalEvent'] != undefined || options['handleObj'] != undefined)
		options = {}; // ignore options when it is an event

	var propagate = $this.hasClass('submit-propagate');

	var submit_extra_data = undefined;
	if ($this.hasClass('submit')) { // submit form through a link
		submit_extra_data = $this.data('submit');
		$this = $this.parents('form');
	}

	var jq1 = $.fn.jquery.substring(0, 1) === "1";

	if ($this.is('form')) {
		url = $this.attr('action');
		data = $this.serializeArray();
		$(jq1 ? '.btn.active[data-submit=*]' : '.btn.active[data-submit]', $this).each(function () {
			add_data($(this).data('submit'));
		});
		if ($this.attr('enctype') == 'multipart/form-data')
			iframe = true;
		type = $this.attr('method');
		if (type == undefined)
			type = 'get';
		cache = type == 'get';
	} else if ($this.is('a')) {
		url = $this.attr('href');
		cache = true;
		type = 'get';
	} else if ($this.is('select')) {
		url = $this.data('action');
		cache = true;
		type = 'get';
		add_data({'value': $this.val()});
	}
	if ($this.hasClass('post')) {
		type = 'post';
		cache = false;
	}
	if ($this.hasClass('domid')) {
		if (!$this.attr('id'))
			$this.attr('id', 'wvAjax_gen_id_' + (++wvAjax_gen_id));
		add_data({'domin': $this.attr('id')});
	}
	if (options['extra_data'] != undefined)
		add_data(options['extra_data']);
	if ($this.data('submit') != undefined)
		add_data($this.data('submit'));
	if ($this.data('collect') != undefined) {
		$.each($this.data('collect'), function (k, v) {
			data.push({'name': k, 'value': $(v).val()});
			;
		});
	}
	if (submit_extra_data != undefined)
		add_data(submit_extra_data);
	if (options['url'] != undefined)
		url = options['url'];
	if (options['type'] != undefined)
		type = options['type'];
	if ($this.hasClass('add_href')) {
		add_data({'href': load_href});
	}

	if ($this.hasClass('captcha_modal')) {
		$this.parents('.modal').modal('hide').remove();
		return false;
	}

	// $this.addClass('progress');
	$('#waiting').show();
	$.ajax({
		'url': url,
		'cache': cache,
		'dataType': 'json',
		'data': data,
		'type': type,
		'iframe': iframe,
		'files': iframe ? $(':file', $this) : null,
		'processData': !iframe,
		'success': function (data) {
			// $this.removeClass('progress');
			$('#waiting').hide();
			$.each(data, function (index, action) {
				var action_data = action.data == undefined ? {} : action.data;
				switch (action.cmd) {
					case 'j':
						if (action_data.selector != undefined) {
							var s = jQuery(action_data.selector);
							if (action_data.args == undefined)
								s[action_data.cmd].apply(s);
							else
								s[action_data.cmd].apply(s, action_data.args);
						}
						break;
					case 'redirect':
						$this.addClass('redirect');
						$('#waiting').show();
						window.location.href = action_data.url;
						if (action_data.reload) {
							window.location.reload(true);
						}
						return;
					case 'redirect-post':
						$('#waiting').show();
						var form = $('<form style="display: none" method="post"></form>');
						$('body').append(form);
						form.attr('action', action_data.url);
						$.each(action_data.data, function (name, value) {
							var input = $('<input type="hidden"/>');
							form.append(input);
							input.attr('name', name).val(value);
						});
						form.submit();
						break;
					case 'auth':
						alert('please login');
						return;
					case 'scroll':
						$(window).scrollTop(action_data);
						break;
					case 'edits':
						tryEdits(action_data);
						break;
					case 'initRecaptcha':
						initRecaptcha();
						break;
					case 'form':
						var form_prefix = action_data.form_prefix != undefined ? action_data.form_prefix : '';
						$('.invalid-feedback.' + form_prefix + 'form_error').remove();
						$('.is-invalid.' + form_prefix + 'group_error').removeClass('is-invalid').removeClass(form_prefix + 'group_error');
						$('a.' + form_prefix + 'tab_error').removeClass('is-invalid').removeClass(form_prefix + 'tab_error');
						if (action_data.form_errors != undefined) {
							$.each(action_data.form_errors, function (error_field, error_message) {
								var fieldname = error_field;
								var target = $('#' + form_prefix + fieldname);
								while (target.length == 0 && fieldname) {
									var pos = fieldname.lastIndexOf('_');
									if (pos > 0) {
										fieldname = fieldname.substr(0, pos);
										target = $('#' + form_prefix + fieldname);
									} else
										fieldname = ''; // to abort loop
								}
								if (target.length) {
									var p = target.parent();
									if (p.is('label'))
										target = p;
									if (target.hasClass('highlight'))
										target = target.parents('.highlightTextarea');
									target.after($('<p class="invalid-feedback"></p>').text(error_message).addClass(form_prefix + 'form_error'));
									target.addClass('is-invalid').addClass(form_prefix + 'group_error');
									var pane = target.parents('.tab-pane');
									if (pane.length) {
										var pane_link = $('a[href="#' + pane.attr('id') + '"]', pane.parents('form'));
										pane_link.addClass('is-invalid').addClass(form_prefix + 'tab_error');
									}
								}
							});
						}
						break;
				}
			});

			if (options['success'] != undefined && $.isFunction(options['success']))
				options['success'].call($this);
		},
		'error': function (data) {
			// $this.removeClass('progress');
			$('#waiting').hide();
			var text = (typeof data == 'object' && data.responseText != undefined && data.responseText) ? data.responseText : '';
			if (!text) {
				text = data.status == 0 ? '<span>Connection to server lost. Please check your internet connection and retry</span>' : 'Unknown error';
			}
			$('#waiting').before('<div id="crit_error_modal" class="modal hide hidden_remove"><div class="modal-header"><a class="close" data-dismiss="modal">&times;</a><h3>ERROR</h3></div><div class="modal-body"> </div><div class="modal-footer"><a class="btn btn-secondary" data-dismiss="modal">Close</a></div></div>');
			if (text.indexOf('<') == 0) {
				$('#crit_error_modal .modal-body').append(text);
			} else {
				var pre = $('<pre></pre>').text(text);
				$('#crit_error_modal .modal-body').append(pre);
			}
			$('#crit_error_modal').modal('show');
		}
	});

	if (!propagate) {
		return false;
	}
};

$(function ($) {
	$('.nav-collapse').collapse('hide');

	$('body')
			.on('submit', 'form.ajax_form', wvAjax)
			.on('click', 'a.ajax_link:not(.disabled), form.ajax_form .submit', wvAjax)
			.on('change', 'select.ajax_change', wvAjax)
			.on('hidden hidden.bs.modal', '.hidden_remove', function () {
				$(this).remove();
			})
			.on('click', 'a.disabled', function () {
				return false;
			})
			;
	$('.change_onload select.ajax_change').each(wvAjax);
	$('button.filter_reset').click(function () {
		var form = $(this).parents('form');
		$('select', form).val('');
		$('input', form).val('');
		if ($.fn.chosen != undefined)
			try {
				$('select', form).trigger("chosen:updated");
			} catch (e) {
			}
	});

	$('select.select-update').change(function () {
		wvAjax({
			type: 'get',
			url: $(this).attr('data-update-url'),
			extra_data: {id: $(this).val(), target: $(this).attr('data-update-target')}
		});
	}).each(function () {
		if ($(this).val()) {
			var target = $(this).attr('data-update-target');
			wvAjax({
				type: 'get',
				url: $(this).attr('data-update-url'),
				extra_data: {id: $(this).val(), target: $(this).attr('data-update-target')},
				success: function () {
					if ($(target).attr('data-refresh'))
						$(target).val($(target).attr('data-refresh')).trigger("chosen:updated");
				}
			});
		}
	});

	$('select.toggle-on-value').on('change', function () {
		var data = $(this).data('toggle-on-value');
		$(data.target).toggleClass(data.class, data.values.indexOf($(this).val()) > -1);
	}).change();

	var target_selector_1 = $('#edit_petition_target_selector_1');
	if (target_selector_1.length) {
		var target_selector_2 = $('#edit_petition_target_selector_2');
		target_selector_1.change(function () {
			var val1 = target_selector_1.val();
			var val2 = target_selector_2.val();
			if (val1 == val2) {
				target_selector_2.val('');
			}
			$('option', target_selector_2).each(function () {
				var option = $(this);
				var oval = option.val();
				if (oval && oval == val1) {
					option.prop('disabled', true);
				} else {
					option.prop('disabled', false);
				}
			});
			if (val1) {
				target_selector_2.prop('disabled', false);
			} else {
				target_selector_2.prop('disabled', true);
			}
			target_selector_2.trigger("chosen:updated");
		});

		target_selector_1.change();
	}

	$('body').on('click', '.filter_order', function () {
		$('#o').val($(this).attr('data-value'));
		$('form.filter_form').submit();
	});

	tryEdits();

	$('form.form_show_submit').on('change keyup', 'textarea, input[type=text]', function () {
		var form = $(this).parents('form');
		$('button[type=submit]', form).show();
	});

	$('.modal_show').modal('show');
	initRecaptcha(true);

	$.fn.select2color = function () {
		var format = function (state) {
			var val = $(state.element).val();
			return '<span class="pledge_color pledge_color_' + val + '"></span>' + state.text;
		};
		$(this).select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function (m) {
				return m;
			},
			minimumResultsForSearch: -1
		});
	};

	$('body').on('click', '.download-prepare', function () {
		var a = $(this);
		var href = a.attr('href');
		var submit = a.data('submit');
		var pages = submit.pages;
		var modal_body = $('#prepare-download .modal-body');


		var error = function () {
			modal_body.append('<div class="alert">Error.</div>');
		};

		a.hide();

		var progress = $('<div class="progress"></div>');
		a.after(progress);
		var bar = $('<div class="progress-bar" style="width: 0%;"></div>');
		progress.append(bar);

		var process = function (page) {
			$.ajax({
				url: href,
				cache: false,
				dataType: 'json',
				data: [{name: 'page', value: page}],
				type: 'post',
				success: function (data) {
					if (data.status === 'ok') {
						bar.css('width', Math.floor(((page + 1) * 100) / pages) + '%');
						if (page + 1 < pages) {
							process(page + 1);
						} else {
							bar.css('width', '100%');
							var ready = $('#prepare-download .download-ready');
							ready.attr('href', ready.data('href')).attr('disabled', null);
							$('#prepare-download .modal-header h3').text('Export file generated. Download ready.');
						}
					} else {
						error();
					}

				},
				error: error
			});
		};

		process(0);

		return false;
	});

	$('.select-order').each(function () {
		var options = $(this).data('options');
		var target = $(options.target);
		var option;

		if (target.length) {
			for (var i = options.keys.length - 1; i >= 0; i--) {
				option = $('option[value=' + options.keys[i] + ']', target);
				if (option.length) {
					target.prepend(option);
				}
			}

			if (options.selectFirst) {
				target.val($('option:first', target).val()).change();
			}
		}
	});

	$('input[type=checkbox].checkbox-all').on('change', function () {
		var target = $($(this).data('target'));
		if (target.length) {
			target.prop('checked', $(this).prop('checked'));
		}
	});

	function luma(hexCode) {
		hexCode = hexCode.replace('#', '');

		var r = parseInt(hexCode.substr(0, 2), 16) / 255;
		var g = parseInt(hexCode.substr(2, 2), 16) / 255;
		var b = parseInt(hexCode.substr(4, 2), 16) / 255;

		return (0.213 * r + 0.715 * g + 0.072 * b);
	}

	$('input.luma-light').on('change', function () {
		var val = $(this).val();
		var l = luma(val);
		var info = $(this).data('luma-info');
		if (!info) {
			info = $('<span style="margin-left: 5px;color: red;"></span>');
			$(this).data('luma-info', info);
			$(this).after(info);
		}

		if (l < 0.5) {
			info.text('Please select a lighter color.');
		} else {
			info.text('');
		}
	}).change();


	var registerInitRecaptchaDone = false;
	$('.login-register-switch').on('click', function () {
		$('#register_form, #login_form').toggle();
		//$('#login_modal').modal();

		if (!registerInitRecaptchaDone) {
			initRecaptcha();
			registerInitRecaptchaDone = true;
		}
	});
});

jscolor.dir = '/js/dist/';

$(document).ready(function($) {
	(function($, widget_id, window, Math, target_selectors, CT_extra, t_sel, t_sel_all, petition_id, numberSeparator, isOpenECI) {
		var widget = $('#widget');
		var widget_left = $('#widget-left');
		var widget_right = $('#widget-right');
		var content_right = $('#content-right');
		var down_button = $('#down-button');
		var head = $('#head');
		var pledge_ul = $('#pledges');
		var tabs = $('#tabs');
		var tabs_left = $('.left-tab', tabs);
		var tabs_right = $('.right-tab', tabs);
		var tab_pad = $('.tab-pad', tabs);
		var scroll_pledges = $('#scroll-pledges');
		var lastSigners = $('#last-signers');
		var lastSignersExists = $('#last-signers-exists');
		var font_size_auto_elements = $('.font-size-auto');
		var textarea_email = $('#petition_signing_email_body_copy');

		var textarea_email_base_text = null;
		var replaceTargetKeywords = null;
		var replaceSignKeywords = null;
		var replaceForceRefresh = false;
		var old_height = null;
		var openECIsigned = false;

		var numberWithCommas = function numberWithCommas(x) {
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, numberSeparator);
		};

		var pledge_ul_clear = function () {
			var t = $('.pledge-text');
			t.hide();
			widget.append(t);
			pledge_ul.empty();
		}

		if (window.navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i)) {
			$('body').addClass('isMobile');
		} else {
			$('body').addClass('isDesktop');
		}

		if (textarea_email.length) {
			textarea_email.data('baseHeight', textarea_email.height());
			textarea_email_base_text = $('#petition_signing_email_body').val();
		}
		function resetTextareaEmail() {
			if (textarea_email.length) {
				var baseHeight = textarea_email.data('baseHeight');
				textarea_email.height(baseHeight);
			}
		}
		function resizeTextareaEmail() {
			// pretty experimental stuff, disable on problems

			if (textarea_email.length) {
				if (widget_right.width() === widget_left.width()) {
					// detected one column
					return null;
				}

				var diff = widget_right.height() - widget_left.height();
				if (diff > 2) {
					textarea_email.height(textarea_email.height() + diff - 2);
				}
			}
		}

		function resize() {
			resetTextareaEmail();

			if (tabs.length) {
				// z-index: 0  tabs if right side small
				// z-index: 1  force tabs
				// z-index: 2  disable tabs

				fontResize(font_size_auto_elements);

				var mode = parseInt(tabs.css('z-index'), 10);
				var tabsOn = false;

				if (mode === 0) {
					tabs.addClass('calc-tab');
					tabsOn = content_right.height() < tabs_left.outerHeight() + tabs_right.outerHeight() + head.height();
					tabs.removeClass('calc-tab');
				} else if (mode === 1) {
					tabsOn = true;
				} else if (mode === 2) {
					tabsOn = false;
				}

				if (tabsOn) {
					tabs.removeClass('no-tabs');
					resizeTextareaEmail();

					// tab_pad
					var diff = widget_right.height() - widget_left.height();
					if (diff > 0) {
						tab_pad.css({
							height: diff,
							bottom: -diff
						});
					} else {
						tab_pad.css({
							height: 0,
							bottom: 0
						});
					}

				} else {
					tabs.addClass('no-tabs');
					tabs.addClass('left');
					tabs.removeClass('right');

					resizeTextareaEmail();
				}
			} else {
				resizeTextareaEmail();
			}

			var height = widget.height();
			if (!old_height || height !== old_height) {
				if ('postMessage' in window)
					window.parent.postMessage('policat_height;' + iframe_no + ';' + height, '*');
			}
			old_height = height;
		}

		down_button.click(function() {
			scrollTop($('#sign'), true);
			return false;
		});

		function scrollTop(element,force) {
			var top = Math.ceil(element.offset().top);
			window.parent.postMessage('policat_scroll;' + iframe_no + ';' + top+ ';' + (force ? 1 : 0), '*');
		}

		function show_right(name) {
			widget.removeClass('right-only');
			widget_right.removeClass('show-sign').removeClass('show-donate').removeClass('show-embed-this').removeClass('show-thankyou').removeClass('show-openECI').removeClass('show-openECI-thankyou-with-sign');
			widget_right.addClass('show-' + name);
			window.parent.postMessage('policat_show;' + JSON.stringify({side: 'right', content: name, iframe: iframe_no, widget: widget_id}) , '*');
		}

		function show_left(name) {
			$('#action, #privacy-policy, #embed-this-left').hide();
			$('#' + name).show();
			window.parent.postMessage('policat_show;' + JSON.stringify({side: 'left', content: name, iframe: iframe_no, widget: widget_id}) , '*');
		}

		function show_sign() {
			show_left('action');
			show_right('sign');
			resize();
		}
		function show_openECI() {
			show_left('action');
			show_right('openECI');
			resize();
		}
		function show_donate() {
			show_left('action');
			show_right('donate');
			resize();
		}
		function show_embed_this() {
			if ($('#embed-this-left').length) {
				show_left('embed-this-left');
			} else {
				show_left('action');
			}
			show_right('embed-this');
			resize();
		}
		function show_thankyou() {
			if (openECI && !hasSign) {
				show_right('openECI-thankyou-with-sign');
				fontResize($('.font-size-auto-subscribe'));
				$('.font-size-auto-subscribe').removeClass('font-size-auto-subscribe');
				$('#petition_signing_subscribe').addClass('required');
			} else {
				show_right('thankyou');
				widget.addClass('right-only');
			}
			$('.share').after($('.last-signings'));
			resize();
			fetchLastSigners(1, 30);
		}
		function show_privacy_policy() {
			show_left('privacy-policy');
			if (hasSign) {
				show_right('thankyou');
			}
			else {
				show_right('sign');
			}
			resize();
			scrollTop($('#privacy-policy'), false);
		}

		if (!('postMessage' in window)) {
			$('.content-right .stage-right').css('max-height', '500px'); // IE7
		}

		function fontResize(element) {
			if (!element || !element.length) {
				return null;
			}

			if (element.length > 1) {
				element.each(function() {
					fontResize($(this));
				});

				return null;
			}

			var parent = element.parent();
			var width = parent.width();
			if (width === element.data('fontResize')) {
				return null;
			}

			element.data('fontResize', width);

			var baseSize = element.data('fontBaseSize');

			if (!baseSize) {
				baseSize = parseInt(element.css('font-size'), 10);
				if (!baseSize || baseSize < 10) {
					baseSize = 32;
				}
				element.data('fontBaseSize', baseSize);
			}

			var n = baseSize;
			element.css('font-size', n + 'px');
			while (n > 11) {
				n--;
				if (element.width() > width || element.width() < (element.height() * 2)) {
					element.css('font-size', n + 'px');
				}
				else {
					return null;
				}
			}
		}

		fontResize(font_size_auto_elements);

		if (parseInt($('#labels-inside').css('z-index'), 10) === 1) {
			$("#sign input[type=text], #sign textarea, #sign select").each(function(index, elem) {
				var eId = $(elem).attr("id");
				var label = null;
				if (eId && (label = $(elem).parents("form").find("label[for="+eId+"]")).length == 1) {
					if ($(elem).is('select')) {
						var firstOption = $('option:first', elem);
						if (firstOption.length && !firstOption.val() && !firstOption.text()) {
							firstOption.text($(label).text())
						}
					} else {
						$(elem).attr("placeholder", $(label).html());
					}
					$(label).remove();
				}
			});
		}

		$('select').wrap('<div class="select-wrap"/>');

		var hash_parts = window.location.hash.substring(1).split('!');
		var verified_id = parseInt(hash_parts[0], 10);
		var hasSign = verified_id === petition_id;
		var editMode = hash_parts[1].length > 0;
		if (editMode)
			var edit_code = hash_parts[1];
		var count = decodeURIComponent(hash_parts[2]);
		var iframe_no = hash_parts[3];
		var name = hash_parts[4];
		var ref = hash_parts[5];

		if (hasSign) {
			$('.reload', widget_right).remove();
			widget.addClass('has_sign');
		}

		if (count) {
			var c = count.split('-');
			if (c.length >= 2) {
				var a = parseInt(c[0]);
				var b = parseInt(c[1]);
				var p = Math.ceil(a / b * 100);
				var el_count = $('#count .count-count');
				var el_target = $('#count .count-target');
				$('#count .count-target-number').text(numberWithCommas(b));
				el_count.text(el_count.first().text().replace('#', numberWithCommas(a)));
				el_target.text(el_target.first().text().replace('#', numberWithCommas(b)));
				if (p > 30) {
					$('#count .count-bar span').css({'color': 'white', 'width': p + '%', 'margin-left': '-4px'});
				}
				else {
					$('#count .count-bar span').css({'text-align': 'left', 'margin-left': p + '%'});
				}
				$('#count .count-bar div').animate({'width': p + '%'}, 2500, 'swing', function() {
					$('#count .count-bar span').html(numberWithCommas(a));
				});
			}
		}
		else
			$('#count').hide();

		$('a.facebook, a.whatsapp, a.twitter, a.gplus').each(function() {
			if ($(this).hasClass('twitter'))
				$(this).attr('href', $(this).attr('href') + ref + '&amp;source=');
			$(this).attr('href', $(this).attr('href') + encodeURIComponent(ref));
		});

		var readmore = $('#readmore').attr('href');
		if (!readmore)
			readmore = ref;
		$('a.mailto').each(function() {
			var h = $(this).attr('href').replace('UURRLLMMOORREE', encodeURIComponent(readmore)).replace('UURRLLRREEFF', encodeURIComponent(ref));
			if (h.length > 2040)
				h = h.substring(0, 2040);
			$(this).attr('href', h);
		});

		var country_names = null;
		if (CT_extra) {
			country_names = CT_extra;
		} else {
			country_names = new Array();
			$('#petition_signing_country option').each(function() {
				country_names[$(this).val()] = $(this).text();
			});
		}

		if (target_selectors) {
			var ts = $('#target-selector');
			var insert_sort = function(dom, list, add_class, is_country, pledges, template, infos, pledge_count, natsort) {
				var k, sk, option, element, i;
				var count_pledge = 0;
				if (is_country != undefined && is_country) {
					for (k in list) {
						if (country_names[k] != undefined)
							list[k] = country_names[k];
					}
				}
				do {
					sk = null;
					for (k in list) {
						if (sk == null)
							sk = k;
						else {
							if (natsort !== null && typeof natsort === 'object') {
								if (k in natsort && sk in natsort && naturalSorter(natsort[k], natsort[sk]) < 0) {
									sk = k;
								}
							} else {
								if (list[k] < list[sk]) {
									sk = k;
								}
							}
						}
					}
					if (sk != null) {
						if (typeof pledges == 'object') {
							// pledge selector
							element = $(template.data);
							$('input', element).attr('id', 'pledge_contact_' + sk).val(sk);
							$('.pledge_contact_name', element).attr('for', 'pledge_contact_' + sk);
							var name = $('.pledge_contact_name', element).text(list[sk]);
							if (infos !== null && typeof infos === 'object' && infos[sk]) {
								name.append($('<span></span>').text(infos[sk]));
							}
							dom.append(element);
							if (pledges[sk]) {
								var pledges_yes = 0;
								for (i in pledges[sk]) {
									$('.pledge_item_' + i, element).addClass('pledge_status_' + pledges[sk][i]);
									if (pledges[sk][i] == 1) {
										pledges_yes++;
									}
								}

								if (pledges_yes == pledge_count) {
									$('input', element).attr('disabled', 'disabled').hide();
									$('.pledge_done', element).css('display', 'inline-block');
								}
							}
							count_pledge++;
						} else {
							// regular target selector
							option = $('<option></option>');
							dom.append(option);
							option.text(list[sk]).attr('value', sk);
							if (add_class)
								option.addClass(add_class);
						}
						delete list[sk];
					}
				} while (sk != null);

				if (count_pledge === 1) {
					$('input[type=checkbox]', dom).prop('checked', true);
				}

				if (dom.hasClass('type_select')) {
					dom.prepend('<option value=""></option>').val('');
					dom.prev('input.type_select').val('');
				}

				resize();
			};

			var select_lookup = {};
			var insert = function(list, is_country) {
				if (is_country != undefined && is_country) {
					for (var k in list) {
						if (country_names[k] != undefined)
							list[k] = country_names[k];
					}
				}
				for (var k in list) {
					select_lookup[list[k].toLowerCase()] = k;
				}
			};

			var first = true;
			var next_fixed_choices = null;
			var keywords = null;
			$.each(target_selectors, function(_, selector) {
				var pledges = selector['pledges'] == undefined ? false : selector['pledges'];
				if (typeof pledges == 'object') {
					insert_sort(pledge_ul, selector['choices'], null, null, pledges, pledge_ul.data('template'), selector['infos'], pledge_ul.data('pledge-count'), selector.sort);
				} else {
					var isCountry = selector['country'] == undefined ? false : selector['country'];
					var div = $('<div></div>');
					ts.append(div.addClass('ts'));
					if (first) {
						div.addClass('ts_first');
					}
					var label = $('<label></label>');
					div.append(label);
					label.text(selector['name']);
					var select = $('<select></select>');
					var div_s = $('<div></div>');
					div.append(div_s.addClass('select-wrap'));
					div_s.append(select);
					if (isCountry) {
						select.addClass('country');
						$('#petition_signing_country').change(function() {
							if (!select.val()) {
								select.val($(this).val());
								if (select.val())
									select.change();
							}
						});
					}
					var option = $('<option></option>');
					select.append(option);
					if (selector.id === 'contact') {
						select.addClass('change_contact');
					}
					option.text(first ? '--' + t_sel + '--' : t_sel_all).attr('value', (first /* && target_selectors.length !== 1 */) ? '' : 'all');
					if (selector['choices'] != undefined) {
						var is_typefield = selector['typfield'] != undefined && selector['typfield'];
						var is_contact = selector['id'] != undefined && selector['id'] === 'contact';
						if (is_contact) {
							option.text(t_sel_all);
							select.addClass('not_required');
							$('#petition_signing_ts_1').addClass('not_required');
						}
						select.attr('id', 'petition_signing_ts_1_copy');
						select.attr('name', 'petition_signing_[ts_1]');
						if (is_typefield) {
							insert(selector['choices'], isCountry);
							var h = $('<input type="hidden" />');
							select.before(h);
							var sid = select.attr('id');
							h.attr('name', select.attr('name'));
							select.remove();
							select = h;
							select.attr('id', sid);
						} else {
							insert_sort(select, selector['choices'], null, isCountry);
						}
						if (!is_contact) {
							select.change(function() {
								var ts_2 = $('#petition_signing_ts_2_copy');
								var s_val = select.val();
								if (s_val) {
									ts_2.attr('disabled', 'disabled');
									scroll_pledges.removeClass('error');
									if (pledge_ul.length)
										pledge_ul_clear();
									$.ajax({
										type: 'POST',
										dataType: 'json',
										url: window.location.href.split('#', 1)[0],
										data: {'target_selector': s_val},
										success: function(data) {
											if (typeof data.keywords === 'object') {
												keywords = data.keywords;
											}
											if (typeof data.pledges == 'object') {
												pledge_ul_clear();
												insert_sort(pledge_ul, data.choices, null, null, data.pledges, pledge_ul.data('template'), data.infos, pledge_ul.data('pledge-count'), data.sort);
											} else {
												$('option.x', ts_2).remove();
												insert_sort(ts_2, data.choices, 'x', ts_2.hasClass('country'));
											}
											ts_2.attr('disabled', null);
											ts_2.change(); // trigger load of contacts
											resize();
										}
									});
								} else {
									$('option.x', ts_2).remove();
								}
							});
						}

						if (is_typefield) {
							var search = $('<input type="text" class="search" value="" />');
							select.after(search);
							select.hide();
							var search_h = function() {
								var s_old = select.val();
								var search_val = search.val();
								var search_val_lower = search_val.toLowerCase();
								if (search_val_lower in select_lookup)
									search_val = select_lookup[search_val_lower];
								else
									search_val = '';
								select.val(search_val);
								if (s_old !== select.val()) {
									select.change();
								}
								if (select.val()) {
									select.parent().addClass('form-indicator-positive');
								}
							};
							search.click(search_h).keyup(search_h);
							search.on('blur', function () {
								select.blur();
							});
						}

						if (selector['fixed']) {
							select.val(selector['fixed']);
							div.hide();
							ts.addClass('single');
							if (selector['fix_choices']) {
								next_fixed_choices = selector['fix_choices'];
							}
						}
					}
					else {
						select.attr('id', 'petition_signing_ts_2_copy').attr('disabled', 'disabled');
						select.attr('name', 'petition_signing_[ts_2]');

						if (next_fixed_choices) {
							var contact_choices = $.extend({}, next_fixed_choices);
							insert_sort(select, next_fixed_choices, 'x', select.hasClass('country'));
							select.attr('disabled', null);

							if (selector['id'] === 'contact' && select.val() === 'all' && $('option', select).length === 2) {
								div.hide();
								show_fix_contacts(contact_choices, selector['name']);
							}
							next_fixed_choices = null;
						}

						if (selector['typfield']) {
							insert(selector['choices'], isCountry);
							var type_select = $('<input type="text" class="type_select not_required" />');
							select.addClass('type_select');
							select.before(type_select);
							type_select.on('keyup change', function () {
								select.val(type_select.val());
								if (select.val()) {
									select.change();
								}

								return false;
							});
						}

						function show_fix_contacts(choices, label) {
							var contacts = $('<div class="contacts"></div>');
							ts.append(contacts);
							$('<strong></strong>', {text: label + ': '}).appendTo(contacts);
							$.each(choices, function (_, name) {
								$('<span></span>', {text: name}).appendTo(contacts);
							});
						}

						if (selector['fixed']) {
							select.attr('disabled', null);
							var fix_option = $('<option></option>');
							select.append(fix_option);
							fix_option.text(selector['fixed']).attr('value', selector['fixed']);
							select.val(selector['fixed']);
							div.hide();
							if (selector['fix_choices']) {
								show_fix_contacts(selector['fix_choices'], selector['fix_label']);
							}
						}

						if (selector['fix_choices_plegde']) {
							insert_sort(select, selector['fix_choices_plegde'], 'x', select.hasClass('country'));
							select.attr('disabled', null);
							var fix_choices_plegde_all = selector['fix_choices_plegde_all'];
							insert_sort(pledge_ul, fix_choices_plegde_all['choices'], null, null, fix_choices_plegde_all['pledges'], pledge_ul.data('template'), fix_choices_plegde_all['infos'], pledge_ul.data('pledge-count'));
						}

						if (pledge_ul.length) {
							select.change(function() {
								scroll_pledges.removeClass('error');
								var ts_1 = $('#petition_signing_ts_1_copy');
								var s_val = select.val();
								if (s_val) {
									ts_1.attr('disabled', 'disabled');
									select.attr('disabled', 'disabled');
									if (pledge_ul.length) {
										pledge_ul_clear();
									}
									$.ajax({
										type: 'POST',
										dataType: 'json',
										url: window.location.href.split('#', 1)[0],
										data: {target_selector1: ts_1.val(), target_selector2: s_val},
										success: function(data) {
											pledge_ul_clear();
											insert_sort(pledge_ul, data.choices, null, null, data.pledges, pledge_ul.data('template'), data.infos, pledge_ul.data('pledge-count'), data.sort);
											ts_1.attr('disabled', null);
											select.attr('disabled', null);
											resize();
										}
									});
								} else {
									pledge_ul_clear();
									resize();
								}
							});
						}
					}
					first = false;

					if (selector.keywords && !keywords) {
						keywords = selector.keywords;
					}
				}

				if (textarea_email.length) {
					ts.on('change', 'select.change_contact', function () {
						if (!keywords) {
							return;
						}
						var contactId = $(this).val();
						if (contactId && keywords[contactId]) {
							replaceTargetKeywords = keywords[contactId];
							replaceTextareaEmail();
							replaceForceRefresh = true;
						} else {
							replaceTargetKeywords = null;
							replaceTextareaEmail();
							replaceForceRefresh = false;
						}
					});
				}
			});

			if ($('div.ts', ts).length == 1) {
				ts.addClass('single');
				$('#petition_signing_ts_2').remove();
			}
		}

		function replaceTextareaEmail() {
			if (replaceTargetKeywords || replaceSignKeywords) {
				if (!replaceForceRefresh) {
					textarea_email.val(replaceAll(textarea_email.val(), replaceTargetKeywords, replaceSignKeywords));
				} else {
					textarea_email.val(replaceAll(textarea_email_base_text, replaceTargetKeywords, replaceSignKeywords));
				}
			} else {
				if (replaceForceRefresh) {
					textarea_email.val(textarea_email_base_text);
				}
			}
		}

		var petition_signing_comment = $('#petition_signing_comment');

		if (textarea_email.length && textarea_email.attr('disabled') && petition_signing_comment.length) {
			petition_signing_comment.on('blur keyup change', function () {
				var val = $(this).val();
				replaceForceRefresh = true;
				if (val) {
					replaceSignKeywords = {"#PERSONAL-COMMENT#": val};
				} else {
					replaceSignKeywords = null;
				}

				replaceTextareaEmail();
			});
		}

		$('#privacy-policy').hide();
		if (hasSign) {
			show_thankyou();
		}
		if (editMode) {
			$('#action, a.back').hide();
			$('#widget_edit_code').val(edit_code);
			$('#widget_id').val(widget_id); // from outer JS
			$('#widget_email, #widget_organisation').parent().remove();
			$('#embed-this-register, #embed-this-generate').remove();
			$('#embed-this-change').show();
			show_embed_this();
		}
		else
		{
			$('#embed-this-left').hide();
		}
		$('#policat-widget-loading').hide();
		if (hasSign)
			$('#petition_signing_email_subject_copy, #petition_signing_email_body_copy, #petition_signing_ts_1_copy').attr('disabled', 'disabled');

		$('#widget-right input[type=hidden],textarea.original').each(function() {
			var id = $(this).attr('id') + '_copy';
			var copy = $('#' + id);

			if (copy.val() == '' && $(this).val() != '') {
				copy.val($(this).val());
			}
		});

		// Embed this
		$('form#embed2 textarea, form#embed2 input[type=text]').focus(function() {
			$(this).select();
		});

		$('#widget_petition_text_id_copy').change(function() {
			$.getJSON('/text/' + $(this).val(), null, function(data, textStatus) {
				for (var key in data) {
					$('#embed2 #widget_' + key + '_copy, #embed2 #widget_' + key).val(data[key]);
				}
			});
		});

		function prepareValidate(field, positive) {
			var input = $(field);
			var parent =input.parent();

			if (input.is('input[type=text], select')) {
				parent.addClass('form-indicator');
			}

			if (input.is('select') && input.is(':not(.type_select)')) {
				parent.addClass('form-indicator-select');
			}

			if (positive) {
				parent.addClass('form-indicator-positive');
			}
		}

		function validate(field) {
			var input = $(field);
			var parent =input.parent();
			var valid = true;
			var val = $.trim(input.val());
			if (val === '' && !input.hasClass('not_required')) {
				valid = false;
			}

			if (valid && input.is('#petition_signing_email, #widget_email')) {
				var mail = val.match(/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i);
				if (!mail) {
					valid = false;
				}
			}

			if (valid && input.is('#paypal_amount')) {
				var number = val.match(/^\d+([.,]\d*)?$/i);
				if (!number) {
					valid = false;
				}
			}

			parent.toggleClass('form-error', !valid);
			parent.toggleClass('form-valid', valid);

			var label = parent.prev();
			if (label.is('label')) {
				label.toggleClass('form-error', !valid);
			}

			return valid;
		}

		var validate_base = $('#sign, #target-selector, #paypal, #embed');

		validate_base.on('blur', 'input, select', function () {
			validate(this);
		});

		validate_base.on('keyup', '.form-error input', function () {
			validate(this);
		});

		$('#petition_signing_privacy, #petition_signing_subscribe').on('change', function() {
			$(this).parent().parent().removeClass('form-error');
		});

		validate_base.on('change', '.form-error select', function () {
			validate(this);
		});

		$('#sign input, #sign select, #paypal_amount, #embed input').each(function() {
			prepareValidate(this, true);
		});
		$('#target-selector input, #target-selector select').each(function() {
			prepareValidate(this, false);
		});

		$('input[type=checkbox]:not(.no-checkbox-wrap)').wrap('<span class="checkbox"></span>');

		// Form handling
		$('#widget-right form').each(function() {
			var form = $(this);
			var isClick = false;
			$('.submit', form).click(function() { // FORM ONCLICK
				if (isClick)
					return false;
				isClick = true;
				var form_error = false;
				var formId = form.attr('id');
				$('.form-error').removeClass('form-error');

				if (formId === 'sign') {
					$('#tabs .left').click();
					if (isOpenECI && openECIsigned) {
						show_thankyou();
					} else {
						show_sign();
					}
				}

				// copy -> original
				$('input[type=hidden],textarea.original', form).each(function() {
					var id = $(this).attr('id') + '_copy';
					var copy = $('#' + id);
					if (copy.length)
						$(this).val(copy.val());
				});

				// pledges
				var pledges = $('#petition_signing_pledges', form);
				if (pledges.length) {
					var pledges_val = [];
					$('#pledges input[type=checkbox]:checked').each(function() {
						pledges_val.push($(this).val());
					});
					pledges.val(pledges_val.join(','));
					if (!pledges_val.length) {
						form_error = true;

						if ($('li', pledge_ul).length) {
							scroll_pledges.addClass('error');
						} else {
							scroll_pledges.removeClass('error');
							$('select', ts).each(function() {
								validate(this);
							});
						}
					} else {
						scroll_pledges.removeClass('error');
					}
				}

				// field validation input
				$('input[type=text],input[type=hidden]:not([id=petition_signing_id]):not([id=widget_id]):not([id=widget_ref]):not([id=widget_edit_code])', form).each(function() {
					var input = $(this);
					var val = $.trim(input.val());
					if (val === '' && !input.hasClass('not_required')) {
						if (input.attr('type') === 'text') {
							input.parent().addClass('form-error');
						}
						else if (input.attr('type') === 'hidden') {
							$('#' + input.attr('id') + '_copy').parent().addClass('form-error');
						}
						form_error = true;
					}
				});

				// field validation select
				$('select', form).each(function() {
					var select = $(this);

					if ($('option:selected', this).val() == '' && !select.hasClass('not_required')) {
						var error_div = select.parent();
						error_div.addClass('form-error');
						var prev = error_div.prev();
						if (prev.is('label')) {
							prev.addClass('form-error');
						}
						form_error = true;
					}
				});

				// field validation email
				$('#petition_signing_email, #widget_email', form).each(function() {
					var field = $(this);
					var val = $.trim(field.val());
					field.val(val);
					var mail = val.match(/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i);
					if (!mail) {
						field.parent().addClass('form-error');
						form_error = true;
					}
				});

				// field validation color
				$('input.color', form).each(function() {
					var color = $(this).val().match(/#[0-9abcdefABCDEF]{6}/);
					if (!color) {
						$(this).parent().addClass('form-error');
						form_error = true;
					}
				});

				// field validation url
				$('input.url', form).each(function() {
					var val = $(this).val();
					var url = (val == '') || val.match(/^https?:\/\/[^\"\s]+\.[^\"\s]+$/);
					if (!url) {
						$(this).parent().addClass('form-error');
						form_error = true;
					}
				});

				$('#paypal_amount', form).each(function() {
					var amount = $(this).val().match(/^\d+(\.\d\d?)?$/);
					if (!amount) {
						$(this).parent().addClass('form-error');
						form_error = true;
					}
				});

				// field validation textarea
				$('textarea:not(#widget_intro):not(#widget_footer):not(#petition_signing_comment)', form).each(function() {
					var textarea = $(this);
					if (textarea.val() === '' && !textarea.hasClass('not_required')) {
						if (textarea.hasClass('original'))
							$('#' + textarea.attr('id') + '_copy').parent().addClass('form-error');
						else
							textarea.parent().addClass('form-error');
						form_error = true;
					}
				});

				// privacy validation
				$('#petition_signing_privacy:not(:checked)', form).each(function() {
					$(this).parent().parent().addClass('form-error');
					form_error = true;
				});
				$('#petition_signing_subscribe.required:not(:checked)', form).each(function() {
					$(this).parent().parent().addClass('form-error');
					form_error = true;
				});

				if (!form_error) {
					if (formId == 'paypal') {
						// paypal form
						window.open(form.attr('action') + '?' + form.serialize(), '_blank');
						isClick = false;
					} else {
						// policat form
						var refName = 'ref';
						switch (formId) {
							case 'sign':
								refName = 'petition_signing[ref]';
								$('#petition_signing_email_subject_copy, #petition_signing_email_body_copy').attr('disabled', 'disabled');
								break;
							case 'embed':
								refName = 'widget[ref]';
								break;
						}
						$.post(window.location.href.split('#', 1)[0], form.serialize() + '&' + refName + '=' + ref, function(data) {
							switch (formId) {
								case 'sign':
									if (isOpenECI) {
										var eciPost = {}
										var eciFields = ["firstname","lastname","post_code","email","country"];
										eciFields.forEach(function (e) {
											var val = $("#petition_signing_" + e).val();
											if (val) {
												eciPost[e.replace('_', '')] = val;
											}
										});
										// window.top.postMessage("@speakout:sign@"+JSON.stringify(eciPost),'*');
										document.getElementById('openECI').contentWindow.postMessage("@speakout:sign@"+JSON.stringify(eciPost),'*');
									}
									window.parent.postMessage('policat_signed;' + JSON.stringify({iframe: iframe_no, widget: widget_id}) , '*');
									hasSign = true;
									if (isOpenECI && !openECIsigned) {
										show_openECI();
									} else {
										show_thankyou();
									}
									$('#widget-right .thankyou .form_message').text('');
									for (var error in data.errors) {
										$('#widget-right .thankyou .form_message').append($('<div></div>').text(data.errors[error]));
									}
									widget.addClass('has_sign');
									resize();
									window.parent.postMessage('policat_scroll;' + iframe_no + ';0;0', '*');
									break;
								case 'embed':
									if (data.isValid) {
										$('#embed_markup').val(data.extra.markup);
										$('.embed-code').show();
										$('#embed button').hide();
										$('#embed input, #embed select, #embed2 input, #embed2 textarea').attr('disabled', 'disabled');
										resize();
									} else {
										if ('landing_url' in data.errors) {
											$('#widget_landing_url_copy').parent().addClass('form-error');
										}
									}
									break;
								default:
									break;
							}
							isClick = false;
						}, "json");
					}
				}
				else {
					isClick = false;
				}

				return false;
			});
		});

		// disable left side
		if ($('#footer_ot').length)
			$('#action input, #action select, #action textarea').attr('disabled', 'disabled');

		// TABS
		if (tabs.length) {
			$('.to-left-tab', tabs).click(function() {
				tabs.removeClass('right').addClass('left');
				resize();
				scrollTop(tabs, false);
			});

			$('.to-right-tab', tabs).click(function() {
				tabs.removeClass('left').addClass('right');
				resize();
				scrollTop(tabs, false);
			});
		}

		// FOOTER
		$('#a-embed-this').click(function() {
			show_embed_this();
		});

		$('#a-donate').click(function() {
			show_donate();
		});

		$('div.privacy label').attr('for', 'useless').click(function() {
			show_privacy_policy();
		});

		$('a.newwin, .widget-left a:not(.back):not(.nonewwin)').click(function() {
			var href = $(this).attr('href');
			if (href)
				window.open(href, '_blank');
			return false;
		});

		// BACK
		$('a.back').click(function() {
			if (hasSign) {
				show_thankyou();
				show_left('action');
			} else {
				show_sign();
			}

			return false;
		});

		$('a.go-to-eci-form').click(function() {
			show_openECI();
			return false;
		});

		$('a.reload-iframe').click(function() {
			window.location.reload();
		});

		$('#embed-copy').on('click', function () {
			$('#embed_markup').select();

			try {
				document.execCommand('copy');
			} catch (err) {
			}
		});

		if (pledge_ul.length) {
			var last_text_for = '';
			pledge_ul.on('touchend', 'a.pledge_item', function(e) {
				// https://stackoverflow.com/questions/10614481/
				e.preventDefault();
				$(this).click();
			});
			pledge_ul.on('click', 'a.pledge_item', function() {
				var a = $(this);
				var pledge_icons = a.parents('.pledge_icons');
				var text_for = a.data('for');
				var text = $(text_for);
				var show = !a.hasClass('pledge_item_active');
				pledge_icons.after(text);
				if (show) {
					text.show();
				} else {
					text.hide();
				}
				$('.pledge_item_active', pledge_ul).removeClass('pledge_item_active');
				if (show) {
					a.addClass('pledge_item_active');
				}
				if (last_text_for && last_text_for != text_for) {
					$(last_text_for).hide();
				}
				last_text_for = text_for;

				return false;
			});
		}

		resize();
		$(window).resize(resize);

		$('img:not(.no_load)', widget).each(function() {
			var src = $(this).attr('src');
			if (!src)
				return;
			var img = new Image();
			img.onload = function() {
				setTimeout(resize, 100);
				setTimeout(resize, 1000);
				setTimeout(resize, 2000);
			};
			img.src = src;
		});

		$('.external_links a').attr('target', '_blank');

		if (lastSigners.length) {
			if (lastSigners.find('span').length) {
				lastSignersExists.show();
			}

			if (lastSigners.data('update')) {
				fetchLastSigners(1, 10);
			}
		}

		function signerText(signer, fields) {
			var ret = signer.name;
			var extra = [];
			if (fields.indexOf('city') > -1 && signer.city) {
				extra.push(signer.city);
			}
			if (fields.indexOf('country') > -1 && signer.country) {
				var country = signer.country;
				if (country_names && country in country_names) {
					country = country_names[country];
				}
				extra.push(country);
			}

			ret = ret + (extra.length ? ' (' + extra.join(', ') + ')' : '');
			return ret;
		}

		function fetchLastSigners(page, max) {
			if (!lastSigners.length) {
				return null;
			}

			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: '/api/v2/actions/' + petition_id + '/last-signings/' + page,
				success: function(data) {
					if (typeof data === 'object' && data.status === 'ok') {
						lastSigners.empty();

						if (page === 1 && name) {
							lastSigners.append($('<span class="self"></span>').text(name));
						}

						for (var i = 0; i < data.signers.length && i < max; i++) {
							var signer = data.signers[i];
							if (name !== signer.name) {
								lastSigners.append($('<span></span>').text(signerText(signer, data.fields)));
							}
						}

						if (data.signers.length) {
							lastSignersExists.show();
						}

						resize();
					}
				}
			});
		}

		function replaceAll(str, mapObj1, mapObj2) {
			if (mapObj2) {
				if (!mapObj1) {
					return replaceAll(str, mapObj2);
				} else {
					return replaceAll(str, $.extend({}, mapObj1, mapObj2));
				}
			}
			if (!mapObj1) {
				return str;
			}

			var re = new RegExp(Object.keys(mapObj1).join("|"), "g");

			return str.replace(re, function (matched) {
				return mapObj1[matched];
			});
		}

		function naturalSorter(as, bs){
			var a, b, a1, b1, i= 0, n, L,
			rx=/(\.\d+)|(\d+(\.\d+)?)|([^\d.]+)|(\.\D+)|(\.$)/g;
			if(as=== bs) return 0;
			a= as.toLowerCase().match(rx);
			b= bs.toLowerCase().match(rx);
			L= a.length;
			while(i<L){
				if(!b[i]) return 1;
				a1= a[i],
				b1= b[i++];
				if(a1!== b1){
					n= a1-b1;
					if(!isNaN(n)) return n;
					return a1>b1? 1:-1;
				}
			}
			return b[i]? -1:0;
		}

		if (isOpenECI) {
			window.addEventListener('message', function(event) {
				if (typeof event.data === 'string') {
					if (event.data.indexOf('@openeci:duplicate@') === 0 || event.data.indexOf('@openeci:sign@') === 0) {
						var data = JSON.parse(event.data.substr(1 + event.data.indexOf('@', 1)));
						if (data && typeof data === 'object' && data.uuid) {
							$('.openECI-ref-number span').text(data.uuid);
							$('.openECI-ref-number').show();
						}
						openECIsigned = true;
						$('div.go-to-eci-form').remove();
						show_thankyou();
					}
				}
			});
		}

	})($, widget_id, window, Math, target_selectors, CT_extra, t_sel, t_sel_all, petition_id, numberSeparator, isOpenECI);
});

jscolor.dir = '/js/dist/';

$(document).ready(function($) {
	(function($, widget_id, window, Math, target_selectors, CT_extra, t_sel, t_sel_all) {
		var policat_widget = $('#policat_widget');
		var body_policat_widget = $('#body_policat_widget');
		var policat_widget_left = $('#policat_widget_left');
		var policat_widget_right = $('#policat_widget_right');
		var content_right = $('#content_right');
		var stage_right = $('.stage_right ');
		var down_button = $('#down_button');
		var pledge_ul = $('#pledges');
		var resize_throttleTimeout = 1;
//		var availHeight = "availHeight" in window.screen ? window.screen.availHeight : null;
		var webkit = window.navigator.userAgent.match(/AppleWebKit\/([0-9]+)/);
		var wkversion = webkit && webkit[1];
		var wkLte534 = webkit && wkversion >= 534;
		var android2 = window.navigator.userAgent.match(/Android ([0-9]+)/) && RegExp.$1 <= 2;
		var iphone = window.navigator.userAgent.match(/iPhone/i) || window.navigator.userAgent.match(/iPod/i);
		var useWkScroll = wkLte534 && !android2;
		var scroll_privacy_policy = $('#privacy_policy_text');
		var scroll_petition_text = $('#petition_text');
		var scroll_petition_text_img = $('img', scroll_petition_text);
		var scroll_background_text = $('#background_text');
		var scroll_pledges = $('#scroll_pledges');
		var scrolls_ = [scroll_privacy_policy, scroll_petition_text, scroll_background_text];
		if (scroll_pledges) {
			scrolls_.push(scroll_pledges);
		}
		var scrolls = $(scrolls_);
		var isSmall = false;
		var old_height = null;

		var numberWithCommas = function numberWithCommas(x) {
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		};

		var resize_left = function() {
			var title_height = $('#pet_title').outerHeight(true);
			var subtitle_height = $('#pet_subtitle').outerHeight(true);
			var tabs_height = $('#petition_tabs').outerHeight(true);
			var target_selector_height = $('#target_selector').outerHeight(true);
			if (!target_selector_height)
				target_selector_height = 0;

			stage_right.addClass('stage_calc');
			stage_right.css('height', stage_right.height());
			stage_right.removeClass('stage_calc');

			var content_right_height = content_right.height();
			if (content_right_height < 400 || isSmall) {
				content_right_height = 400;
			}
			$('#petition_text').css('height', content_right_height - (title_height + subtitle_height + tabs_height));
			$('#petition_signing_email_body_copy').css('height', content_right_height - (61 + target_selector_height + title_height + subtitle_height + tabs_height));
			$('#right_tab,.right_tab_content').css('height', content_right_height - (title_height + tabs_height));
			$('#embed_this_left').css('height', content_right_height);
			$('#privacy_policy_text').css('height', content_right_height - 40);
			scroll_pledges.css('height', content_right_height - (target_selector_height + title_height + subtitle_height + tabs_height));
		};

		var resize = function() {
			var width = $(window).width();
			if (width < 440) {
				body_policat_widget.addClass('small');
				isSmall = true;
//				if (availHeight && policat_widget_left.height() < availHeight / 1.5) {
//					down_button.addClass('hide');
//				}
//				else {
//					down_button.removeClass('hide');
//				}
			} else {
				body_policat_widget.removeClass('small');
				isSmall = false;
			}
			var height = policat_widget.height();
			if (!old_height || height !== old_height) {
				if ('postMessage' in window)
					window.parent.postMessage('policat_height;' + iframe_no + ';' + height, '*');
			}
			old_height = height;

			resize_left();
			policat_widget.css('background-color', policat_widget_left.height() > policat_widget_right.height() ? policat_widget_right.css('background-color') : policat_widget_left.css('background-color'));

			if (!useWkScroll) {
				if (!resize_throttleTimeout) {
					resize_throttleTimeout = setTimeout(function() {
						scrolls.each(function() {
							var scroll = $(this);
							if (scroll.length) {
								if (scroll_petition_text.length) {
									scroll_petition_text_img.css('max-width', scroll_petition_text.width() - 10);
								}
								scroll.data('jsp').reinitialise();
							}
						});
						resize_throttleTimeout = null;
					}, 50);
				}
			}
		};

		down_button.click(function() {
			window.parent.postMessage('policat_scroll;' + iframe_no + ';' + policat_widget_left.height(), '*');
			return false;
		});

		function show_right(name) {
			policat_widget_right.removeClass('show_sign').removeClass('show_donate').removeClass('show_embed_this').removeClass('show_tell');
			policat_widget_right.addClass('show_' + name);
		}

		function show_left(name) {
			$('#petition, #privacy_policy, #embed_this_left').hide();
			$('#' + name).show();
			resize();
		}

		function show_sign() {
			show_left('petition');
			show_right('sign');
		}
		function show_donate() {
			show_left('petition');
			show_right('donate');
		}
		function show_embed_this() {
			if ($('#embed_this_left').length) {
				show_left('embed_this_left');
			} else {
				show_left('petition');
			}
			show_right('embed_this');
		}
		function show_tell() {
			show_right('tell');
		}
		function show_privacy_policy() {
			show_left('privacy_policy');
			if (!useWkScroll && scroll_privacy_policy.length)
				scroll_privacy_policy.data('jsp').reinitialise();
			if (hasSign)
				show_right('tell');
			else
				show_right('sign');
		}

		if (!('postMessage' in window))
			$('#content_right .stage_right').css('max-height', '500px'); // IE7

		// AUTO SIZE
		var n = 32;
		var e = $('#btn_sign');
		while (n > 12) {
			n--;
			if (e.width() >= 170)
				e.css('font-size', n + 'px');
			else
				n = 0;
		}
		e.css('line-height', '49px');
		n = 33;
		e = $('#pet_title');
		while (n > 12) {
			n--;
			if (n === 20)
				e.css('font-weight', 'normal');
			if (e.height() > 72)
				e.css('font-size', n + 'px');
			else
				n = 0;
		}
		n = 16;
		e = $('#policat_widget_right div.sign h2:first');
		while (n > 11) {
			n--;
			if (e.height() > 20)
				e.css('font-size', n + 'px');
			else
				n = 0;
		}

		$('#widget_styling_type').each(function() {
			var label = $("label", $(this).parent());
			label.after($('#embed_this_help_type'));
			label.html(label.html() + " (?)");
		});
		$('#widget_styling_width').each(function() {
			var label = $("label", $(this).parent());
			label.after($('#embed_this_help_width'));
			label.html(label.html() + " (?)");
		});
		$('select').wrap('<div class="div_select"/>');

		var hash_parts = window.location.hash.substring(1).split('!');
		var hasSign = hash_parts[0] == '1' ? true : false;
		var editMode = hash_parts[1].length > 0;
		if (editMode)
			var edit_code = hash_parts[1];
		var count = decodeURIComponent(hash_parts[2]);
		var iframe_no = hash_parts[3];
		var ref = hash_parts[4];

		if (count) {
			var c = count.split('-', 3);
			if (c.length == 3) {
				var a = parseInt(c[0]);
				var b = parseInt(c[1]);
				var t = c[2];
				var p = Math.ceil(a / b * 100);
				$('div#count').before($('<div id="count_target"></div>').text(t).append('<span>' + numberWithCommas(b) + '</span>'));
				if (p > 30) {
					$('div#count span').css({'color': 'white', 'width': p + '%', 'margin-left': '-4px'});
				}
				else {
					$('div#count span').css({'text-align': 'left', 'margin-left': p + '%'});
				}
				$('div#count div').animate({'width': p + '%'}, 2500, 'swing', function() {
					$('div#count span').html(numberWithCommas(a));
				});
			}
		}
		else
			$('div#count').hide();

		$('a.facebook, a.twitter, a.gplus').each(function() {
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

		if (target_selectors) {
			var ts = $('#target_selector');
			var CT = null;
			if (CT_extra) {
				CT = CT_extra;
			} else {
				CT = new Array();
				$('#petition_signing_country option').each(function() {
					CT[$(this).val()] = $(this).text();
				});
			}
			var insert_sort = function(dom, list, add_class, is_country, pledges, template, infos, pledge_count) {
				var k, sk, option, element, i;
				if (is_country != undefined && is_country) {
					for (k in list) {
						if (CT[k] != undefined)
							list[k] = CT[k];
					}
				}
				do {
					sk = null;
					for (k in list) {
						if (sk == null)
							sk = k;
						else {
							if (list[k] < list[sk])
								sk = k;
						}
					}
					if (sk != null) {
						if (typeof pledges == 'object') {
							// pledge selector
							element = $(template.data);
							$('input', element).attr('id', 'pledge_contact_' + sk).val(sk);
							$('.pledge_contact_name', element).attr('for', 'pledge_contact_' + sk);
							var name = $('.pledge_contact_name', element).text(list[sk]);
							if (typeof infos == 'object' && infos[sk]) {
								name.append($('<span></span>').text(' (' + infos[sk] + ')'));
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
			};

			var select_lookup = {};
			var insert = function(list, is_country) {
				if (is_country != undefined && is_country) {
					for (var k in list) {
						if (CT[k] != undefined)
							list[k] = CT[k];
					}
				}
				for (var k in list) {
					select_lookup[list[k].toLowerCase()] = k;
				}
			};

			var first = true;
			$.each(target_selectors, function(_, selector) {
				var pledges = selector['pledges'] == undefined ? false : selector['pledges'];
				if (typeof pledges == 'object') {
					insert_sort(pledge_ul, selector['choices'], null, null, pledges, pledge_ul.data('template'), selector['infos'], pledge_ul.data('pledge-count'));
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
					div.append(div_s.addClass('div_select'));
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
					option.text(first ? '--' + t_sel + '--' : t_sel_all).attr('value', (first && !target_selectors.length == 1) ? '' : 'all');
					if (selector['choices'] != undefined) {
						var is_typefield = selector['typfield'] != undefined && selector['typfield'];
						var is_contact = selector['id'] != undefined && selector['id'] === 'contact';
						if (is_contact) {
							option.text(t_sel_all);
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
									if (pledge_ul.length)
										pledge_ul.empty();
									$.ajax({
										type: 'POST',
										dataType: 'json',
										url: window.location.href.split('#', 1)[0],
										data: {'target_selector': s_val},
										success: function(data) {
											if (typeof data.pledges == 'object') {
												pledge_ul.empty();
												insert_sort(pledge_ul, data.choices, null, null, data.pledges, pledge_ul.data('template'), data.infos, pledge_ul.data('pledge-count'));
												if (!useWkScroll && scroll_pledges.length)
													scroll_pledges.data('jsp').reinitialise();
											} else {
												$('option.x', ts_2).remove();
												insert_sort(ts_2, data.choices, 'x', ts_2.hasClass('country'));
											}
											ts_2.attr('disabled', null);
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
							var div_correct = $('<div>&#10003;</div>').addClass('correct');
							div_s.append(div_correct);
							div_correct.hide();
							var search_h = function() {
								var s_old = select.val();
								var search_val = search.val();
								var search_val_lower = search_val.toLowerCase();
								if (search_val_lower in select_lookup)
									search_val = select_lookup[search_val_lower];
								else
									search_val = '';
								select.val(search_val);
								if (s_old !== select.val())
									select.change();
								if (select.val())
									div_correct.show();
								else
									div_correct.hide();
							};
							search.click(search_h).keyup(search_h);
						}

					}
					else {
						select.attr('id', 'petition_signing_ts_2_copy').attr('disabled', 'disabled');
						select.attr('name', 'petition_signing_[ts_2]');

						if (pledge_ul.length) {
							select.change(function() {
								var ts_1 = $('#petition_signing_ts_1_copy');
								var s_val = select.val();
								if (s_val) {
									ts_1.attr('disabled', 'disabled');
									select.attr('disabled', 'disabled');
									if (pledge_ul.length)
										pledge_ul.empty();
									$.ajax({
										type: 'POST',
										dataType: 'json',
										url: window.location.href.split('#', 1)[0],
										data: {target_selector1: ts_1.val(), target_selector2: s_val},
										success: function(data) {
											pledge_ul.empty();
											insert_sort(pledge_ul, data.choices, null, null, data.pledges, pledge_ul.data('template'), data.infos, pledge_ul.data('pledge-count'));
											ts_1.attr('disabled', null);
											select.attr('disabled', null);
											if (!useWkScroll && scroll_pledges.length)
												scroll_pledges.data('jsp').reinitialise();
										}
									});
								} else {
									pledge_ul.empty();
								}
							});
						}
					}
					first = false;
				}
			});

			if ($('div.ts', ts).length == 1) {
				ts.addClass('single');
				$('#petition_signing_ts_2').remove();
			}
		}

		resize_left();

		scrolls.each(function() {
			var scroll = $(this);
			if (scroll.length) {
				if (useWkScroll) {
					scroll.addClass('wkScroll');
					if (iphone) {
						scroll.addClass('wkScroll_iPhone');
					}
				} else {
					scroll.jScrollPane({mouseWheelSpeed: 30});
				}
			}
		});

		$('#right_tab').hide();
		$('#privacy_policy').hide();
		if (hasSign) {
			$('.tell .back').remove();
			show_tell();
		}
		if (editMode) {
			$('#petition, #policat_widget_footer, a.back').hide();
			$('#widget_edit_code').val(edit_code);
			$('#widget_id').val(widget_id); // from outer JS
			$('#widget_email, #widget_organisation').parent().remove();
			$('#h_widget_reg, #p_widget_reg').remove();
			$('#p_widget_reg_alt').show();
			show_embed_this();
		}
		else
		{
			$('#embed_this_left').hide();
		}
		$('#policat_widget_loading').hide();
		if (hasSign)
			$('#petition_signing_email_subject_copy, #petition_signing_email_body_copy, #petition_signing_ts_1_copy').attr('disabled', 'disabled');

		$('#policat_widget_right input[type=hidden],textarea.original').each(function() {
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

		// Form handling
		$('#policat_widget_right form').each(function() {
			var form = $(this);
			var isClick = false;
			$('.submit', form).click(function() { // FORM ONCLICK
				if (isClick)
					return false;
				isClick = true;
				var form_error = false;
				var formId = form.attr('id');
				$('.form_error').removeClass('form_error');

				if (formId === 'sign') {
					$('#petition_tabs .left').click();
					show_sign();
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
						scroll_pledges.addClass('error');
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
							input.parent().addClass('form_error');
						}
						else if (input.attr('type') === 'hidden') {
							$('#' + input.attr('id') + '_copy').parent().addClass('form_error');
						}
						form_error = true;
					}
				});

				// field validation select
				$('select', form).each(function() {
					if ($('option:selected', this).val() == '') {
						var error_div = $(this).parent();
						error_div.addClass('form_error');
						var prev = error_div.prev();
						if (prev.is('label')) {
							prev.addClass('form_error');
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
						field.parent().addClass('form_error');
						form_error = true;
					}
				});

				// field validation color
				$('input.color', form).each(function() {
					var color = $(this).val().match(/#[0-9abcdefABCDEF]{6}/);
					if (!color) {
						$(this).parent().addClass('form_error');
						form_error = true;
					}
				});

				// field validation url
				$('input.url', form).each(function() {
					var val = $(this).val();
					var url = (val == '') || val.match(/^https?:\/\/[^\"\s]+\.[^\"\s]+$/);
					if (!url) {
						$(this).parent().addClass('form_error');
						form_error = true;
					}
				});

				$('#paypal_amount', form).each(function() {
					var amount = $(this).val().match(/^\d+(\.\d\d?)?$/);
					if (!amount) {
						$(this).parent().addClass('form_error');
						form_error = true;
					}
				});

				// field validation textarea
				$('textarea:not(#widget_intro):not(#widget_footer):not(#petition_signing_comment)', form).each(function() {
					var textarea = $(this);
					if (textarea.val() === '' && !textarea.hasClass('not_required')) {
						if (textarea.hasClass('original'))
							$('#' + textarea.attr('id') + '_copy').parent().addClass('form_error');
						else
							textarea.parent().addClass('form_error');
						form_error = true;
					} else if (textarea.attr('id') === 'tellyour_emails') {
						var emails = textarea.val().split(/[,\s\n\r]+/);
						for (var i = 0; i < emails.length; i++) {
							var mail = emails[i].match(/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i);
							if (!mail) {
								textarea.parent().addClass('form_error');
								form_error = true;
								break;
							}
						}
					}
				});

				// privacy validation
				$('#petition_signing_privacy:not(:checked)', form).each(function() {
					if (!$(this).parent().is('span'))
						$(this).wrap('<span class="checkbox"></span>');
					$(this).parent().parent().addClass('form_error');
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
							case 'tell':
								refName = 'tellyour[ref]';
								break;
							case 'embed':
								refName = 'widget[ref]';
								break;
						}
						$.post(window.location.href.split('#', 1)[0], form.serialize() + '&' + refName + '=' + ref, function(data) {
							var message = '';
							for (var error in data.errors)
								message += '<p>' + data.errors[error] + '</p>';
							switch (formId) {
								case 'sign':
									show_tell();
									$('#policat_widget_right .tell .error').html(message);
									$('.tell .back').remove();
									hasSign = true;
									break;
								case 'tell':
									$('#tellyour_emails').val('');
									$('#policat_widget_right .error').html(message);
									break;
								case 'embed':
									if (data.isValid) {
										$('#embed_markup').val(data.extra.markup);
									} else {
										if ('landing_url' in data.errors) {
											$('#widget_landing_url_copy').parent().addClass('form_error');
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
			$('#petition input, #petition select, #petition textarea').attr('disabled', 'disabled');

		// TABS
		$('#petition_tabs').each(function() {
			var parent = $(this);
			$('.left', this).click(function() {
				parent.removeClass('right').addClass('left');
				$('#right_tab').hide();
				$('#left_tab').show();
				if (!useWkScroll && scroll_petition_text.length)
					scroll_petition_text.data('jsp').reinitialise();
			});

			$('.right', this).click(function() {
				parent.removeClass('left').addClass('right');
				$('#right_tab').show();
				$('#left_tab').hide();
				if (!useWkScroll && scroll_background_text.length)
					scroll_background_text.data('jsp').reinitialise();
			});
		});

		// FOOTER
		$('#a_embed_this').click(function() {
			show_embed_this();
		});

		$('#a_donate, #a_donate2').click(function() {
			show_donate();
		});

		$('#a_share, #a_share2').click(function() {
			$('#policat_widget_right div.tell .thankyou').hide();
			show_tell();
		});

		$('div.privacy label').attr('for', 'useless').click(function() {
			show_privacy_policy();
		});

		$('a.newwin, div#policat_widget_left a:not(.back):not(.nonewwin)').click(function() {
			var href = $(this).attr('href');
			if (href)
				window.open(href, '_blank');
			return false;
		});

		// BACK
		$('a.back').click(function() {
			if (hasSign) {
				show_tell();
				show_left('petition');
			}
			else
				show_sign();

			if ($(this).parent().is('.tell')) {
				$('.thankyou').show();
			}

			return false;
		});

		if (pledge_ul.length) {
			var last_text_for = '';
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
				if (!useWkScroll && scroll_pledges.length)
					scroll_pledges.data('jsp').reinitialise();

				return false;
			});
		}

		resize();
		resize_throttleTimeout = 0;
		$(window).resize(resize);

		$('img:not(.no_load)', policat_widget).each(function() {
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

	})($, widget_id, window, Math, target_selectors, CT_extra, t_sel, t_sel_all);
});

var policat = typeof policat === "undefined" ? {widgets: []} : policat;
(function(policat, window, document, Math, ref, verified_id, width, edit, name) {
	if (policat.widget_here === undefined) {
		policat.overlay_frame_height = null;
		policat.iframe_no = 0;

		var docHeight = function() {
			var e = window, a = 'inner';
			if (!('innerWidth' in window)) {
				a = 'client';
				e = document.documentElement || document.body;
			}
			return e[ a + 'Height' ];
		};

		var docWidth = function() {
			var e = window, a = 'inner';
			if (!('innerWidth' in window)) {
				a = 'client';
				e = document.documentElement || document.body;
			}
			return e[ a + 'Width' ];
		};

		var middleOverlay = function(no, height) {
			var content_frame = document.getElementById('pt_widget_content_frame_' + no);
			if (content_frame) {
				content_frame.style.height = height + 'px';
				var spacer = document.getElementById('pt_widget_spacer_' + no);
				if (spacer) {
					var docHeight_ = docHeight();
					if (height > docHeight_) {
						spacer.style.marginBottom = '-' + docHeight_ + 'px';
					}
					else {
						spacer.style.marginBottom = '-' + Math.floor(docHeight_ - ((docHeight_ - height) / 2)) + 'px';
					}
				}
				return true;
			}
			return false;
		};

		function scrollTo(no, x, y, force) {
			var iframe = document.getElementById('policat_iframe_no_' + no);
			if (iframe) {
				var topOffset = window.policat_scroll_offset || 0;
				var rect = iframe.getBoundingClientRect();
				var height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
				var diffY = y + rect.top - topOffset;
				if (force || diffY < 0 || diffY > (height * 0.7)) {
					window.scrollBy(x + rect.left, diffY);
				}
			}
		}

		var receivePostMsg = function(event) {
			if (typeof event.data === 'string') {
				if (event.data.match(/^policat_height;\d+;\d+$/)) {
					var data = event.data.split(';');
					var no = data[1];
					var height = parseInt(data[2], 10);
					var iframe = document.getElementById('policat_iframe_no_' + no);
					if (iframe) {
						iframe.style.height = height + 'px';
					}

					// overlay
					if (middleOverlay(no, height)) {
						if (policat.overlay_frame_height === null) {
							// first call from iframe
							var doc = document.documentElement, body = document.body;
							var left = (doc && doc.scrollLeft || body && body.scrollLeft || 0);
							var irect = iframe.getBoundingClientRect();
							if (irect.right > left + docWidth()) { // if right margin outside then move widget to top left
								window.scrollBy(irect.left, irect.top);
							}
						}

						policat.overlay_frame_height = height;
					}
				} else if (event.data.match(/^policat_scroll;\d+;\d+;[10]$/)) {
					var data = event.data.split(';');
					var no = data[1];
					var offset = parseInt(data[2], 10);
					scrollTo(no, 0, offset, data[3] === '1' ? true : false);
				}
			}
		};
		if (window.addEventListener)
			window.addEventListener('message', receivePostMsg, false);
		else if (window.attachEvent)
			window.attachEvent('onmessage', receivePostMsg);

		policat.widget_here = function(id, click) {
			var widget = policat.widgets[id];
			var maxWidth = widget.max_width ? widget.max_width : '1080px';
			if ('matchMedia' in window) {
				if (window.matchMedia("(max-device-width:767px)").matches) { // 768 is min height of windows 8 desktops and most netbooks
					maxWidth = '360px';
				}
			} else {
				if ('availWidth' in window.screen && 'availHeight' in window.screen) {
					if (window.screen.availWidth < 768) {
						maxWidth = '360px';
					}
				}
			}
			var iframe_no = policat.iframe_no++;
			if (verified_id) {
				widget.type = 'embed';
			}
			if (edit) {
				widget.type = 'embed';
			}
			if (width) {
				widget.width = 'auto';
			}
			var hash = verified_id + '!' + edit + '!' + widget.target + '!' + iframe_no + '!' + name + '!' + ref;

			function createIFrame(auto) {
				var width = (auto || widget.width === 'auto') ? '100%' : (widget.width + 'px');
				var noPost = 'postMessage' in window ? '' : 'min-width: 502px; min-height: 550px; '; // IE7

				return '<iframe id="policat_iframe_no_' + iframe_no + '" src="' + widget.url + '#' + hash + '" allowTransparency="true" frameborder="0", hspace="0", vspace="0", marginheight="0", marginwidth="0", scrolling="no" style="border:0;outline:0;marin:0;padding:0;width:' + width + ';height:470px;max-width:' + maxWidth + ';' + noPost + '"></iframe>';
			}

			function baseStyle(dom, c) {
				dom.style.margin = '0';
				dom.style.border = '0';
				dom.style.padding = '0';
				dom.style.background = 'transparent none no-repeat 0 0';
				dom.style.position = 'static';
				dom.setAttribute('class', 'policat_widget_' + c);
			}

			function buildOverlay() {
				var doc = document.documentElement, body = document.body;
				var left = (doc && doc.scrollLeft || body && body.scrollLeft || 0);
				var top = (doc && doc.scrollTop || body && body.scrollTop || 0);

				var overlay_darken = document.createElement('div');
				baseStyle(overlay_darken, 'darken');
				overlay_darken.style.width = '100%';
				overlay_darken.style.height = '100%';
				overlay_darken.style.position = 'fixed';
				overlay_darken.style.left = '0';
				overlay_darken.style.top = '0';
				overlay_darken.style.zIndex = '10000';
				overlay_darken.style.backgroundColor = '#cccccc';
				overlay_darken.style.opacity = '0.6';
				body.appendChild(overlay_darken);

				var overlay = document.createElement('div');
				baseStyle(overlay, 'overlay');
				overlay.style.width = '100%';
				overlay.style.height = '100%';
				overlay.style.position = 'absolute';
				overlay.style.left = left + 'px';
				overlay.style.top = '0px';
				overlay.style.zIndex = '10001';
				body.appendChild(overlay);

				var spacer = document.createElement('div');
				baseStyle(spacer, 'spacer');
				spacer.style.width = '100%'; /* IE8 needs 100% */
				spacer.style.height = (docHeight() + document.documentElement.scrollTop) + 'px';
				spacer.style.margin = '0 0 -' + docHeight() +  'px 0';
				spacer.setAttribute('id', 'pt_widget_spacer_' + iframe_no);
				overlay.appendChild(spacer);

				var content_frame = document.createElement('div');
				baseStyle(content_frame, 'content_frame');
				content_frame.setAttribute('id', 'pt_widget_content_frame_' + iframe_no);
				content_frame.style.maxWidth = '100%';
				content_frame.style.height = '502px';
				content_frame.style.width = '100%';
				content_frame.style.margin = '0 auto';
				content_frame.style.position = 'relative';
				content_frame.style.overflow = 'visible';
				overlay.appendChild(content_frame);

				var close_frame = document.createElement('div');
				baseStyle(close_frame, 'close_frame');
				close_frame.style.width = '100%';
				close_frame.style.maxWidth = maxWidth;
				close_frame.style.height = '1px';
				close_frame.style.margin = '0 auto -1px auto';
				close_frame.style.position = 'relative';
				close_frame.style.border = '0';
				close_frame.style.padding = '0';
				close_frame.style.background = 'none';
				close_frame.style.zIndex = '1';
				content_frame.appendChild(close_frame);

				var close = document.createElement('a');
				baseStyle(close, 'close');
				close.style.cursor = 'pointer';
				close.style.position = 'absolute';
				close.style.right = '0px';
				close.style.top = '0px';
				close.style.width = '27px';
				close.style.height = '27px';
				close.style.padding = '0 0 15px 20px';
				close.style.display = 'block';
				close.style.textDecoration = 'none';

				close_frame.appendChild(close);

				var close_x = document.createElement('span');
				baseStyle(close_x, 'close_x');
				close_x.style.display = 'block';
				close_x.style.width = '25px';
				close_x.style.height = '25px';
				close_x.style.lineHeight = '25px';
				close_x.style.fontSize = '25px';
				close_x.appendChild(document.createTextNode("\u00d7"));
				close_x.style.fontFamily = '"Lucida Sans Unicode"';
				close_x.style.textAlign = 'center';
				close_x.style.borderStyle = 'solid';
				close_x.style.color = widget.body_color;
				close_x.style.borderColor = widget.body_color;
				close_x.style.backgroundColor = '#ffffff';
				close_x.style.borderLeftWidth = '2px';
				close_x.style.borderBottomWidth = '2px';
				close_x.style.borderRightWidth = '0';
				close_x.style.borderTopWidth = '0';
				close_x.style.textDecoration = 'none';
				close.appendChild(close_x);

				var content = document.createElement('div');
				baseStyle(content, 'content');
				content.style.width = '100%';
				content.style.maxWidth = maxWidth;
				content.style.margin = '0 auto';
				content.style.position = 'relative';
				content.style.overflow = 'auto';
				content.style.zIndex = '0';
				content.innerHTML = createIFrame(true);
				content_frame.appendChild(content);

				var scroll = function() {
					var iframe = content.firstChild;
					var irect = iframe.getBoundingClientRect();
					var y = 0;
					var x = 0;

					if (irect.top < (-1 * (irect.height)))
						y = irect.top + Math.floor(irect.height / 2);
					else if (irect.top > docHeight())
						y = irect.top - Math.floor(irect.height / 2);

					if (irect.left < (-1 * (irect.width)))
						x = irect.left + Math.floor(irect.width / 2);
					else if (irect.left > docWidth())
						x = irect.left - Math.floor(irect.width / 2);

					if (x != 0 || y != 0) {
						window.scrollBy(x, y);
						return false;
					}
				};

				var resize = function() {
					middleOverlay(iframe_no, policat.overlay_frame_height);
					return scroll();
				};

				if (window.addEventListener) {
					window.addEventListener('resize', resize, false);
					window.addEventListener('scroll', scroll, false);
				}
				else if (window.attachEvent) {
					window.attachEvent('onresize', resize);
					window.attachEvent('onscroll', scroll);
				}

				close.onclick = function() {
					close.removeChild(close_x);
					close_frame.removeChild(close);
					content_frame.removeChild(close_frame);
					while (content.firstChild)
						content.removeChild(content.firstChild);
					content_frame.removeChild(content);
					overlay.removeChild(content_frame);
					overlay.removeChild(spacer);
					body.removeChild(overlay);
					body.removeChild(overlay_darken);

					if (window.removeEventListener) {
						window.removeEventListener('resize', resize, false);
						window.removeEventListener('scroll', scroll, false);
					}
					else {
						window.detachEvent('onresize', resize);
						window.detachEvent('onscroll', scroll);
					}

					policat.overlay_frame_height = null;

					return false;
				};

				return false;
			}

			if (click) {
				buildOverlay();
			}
			else {
				var write = function(markup) {
					if (window['policat_target_id_' + id] == undefined)
						document.write(markup);
					else {
						var t = document.getElementById(window['policat_target_id_' + id]);
						if (t)
							t.innerHTML = markup;
					}
				};

				switch (widget.type) {
					case 'popup':
						write(widget.markup);
						break;
					case 'embed':
						write(createIFrame(false));
						break;
					default:
						break;
				}
			}
		};
	}
})(policat, window, document, Math,
		typeof policat_ref !== 'undefined' ? policat_ref : window.location.href,
		typeof policat_verified !== 'undefined' ? policat_verified : 0,
		typeof policat_width !== 'undefined' ? policat_width : null,
		typeof policat_widget_edit_code !== 'undefined' ? policat_widget_edit_code : '',
		typeof policat_name !== 'undefined' ? policat_name : ''
		);

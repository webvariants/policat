//JAVASCRIPT
if (counter == undefined) var counter = [];
if (interval == undefined) var interval = [];

function counterBar(id, params) {

	function buildMarkup() {
		var markup = params.markup;

		//body_color
		markup = markup.replace(/body_color/g, counterbar[id]['body_color'] != undefined ? counterbar[id]['body_color'] : params.stylings.body_color);

		//body_width
		markup = markup.replace(/body_width/g, counterbar[id]['body_width'] != undefined ? counterbar[id]['body_width'] : '100%');

		//bar_bg_color
		markup = markup.replace(/bar_bg_color/g, counterbar[id]['bar_bg_color'] != undefined ? counterbar[id]['bar_bg_color'] : params.stylings.bg_right_color);

		//counter_bg_color
		markup = markup.replace(/counter_bg_color/g, counterbar[id]['counter_bg_color'] != undefined ? counterbar[id]['counter_bg_color'] : params.stylings.bg_left_color);

		//line_bg_color
		markup = markup.replace(/line_bg_color/g, counterbar[id]['line_bg_color'] != undefined ? counterbar[id]['line_bg_color'] : params.stylings.button_color);

		if (params.percent < 50) {
			markup = markup.replace(/number_text_color/g, counterbar[id]['body_color'] != undefined ? counterbar[id]['body_color'] : params.stylings.body_color);
		} else {
			markup = markup.replace(/number_text_color/g, '#FFF');
		}

		return markup;
	}

	document.write(buildMarkup());

	var number = el('counternumber-' + id);
	if (params.percent < 50) {
		number.style.marginLeft = params.percent + '%';
		number.style.textAlign  = 'left';
 	} else {
		number.style.textAlign  = 'right';
		number.style.width      = params.percent + '%';
		number.style.marginLeft = '-4px';
	}

	number.style.display = 'none';

	counter[id]  = 0;
	interval[id] = window.setInterval("animateBar(" + id + ", " + params.percent + ")", 75);
}

function animateBar(id, percent) {
	counter[id]++;
	el('coloredbar-' + id).style.width = counter[id] + '%';

	if (counter[id] == percent) {
		window.clearInterval(interval[id]);
		el('counternumber-' + id).style.display = 'block';
	}
}

function el(id) {
	return document.getElementById(id);
}
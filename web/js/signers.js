document.addEventListener('DOMContentLoaded', function () {
	(function (document, window) {
		var waiting = document.getElementById('waiting');
		var signers_div = document.getElementById('signers');
		var fields = [];
		var with_city = false;
		var with_country = false;
		var table = false;
		var table_body = null;
		var table_single_td = null;
		var order = 'date_desc';

		if (!signers_div) {
			return;
		}

		var pager_ul = document.getElementById('pager');
		var data = JSON.parse(signers_div.dataset.signers);
		var locale = data.locale;

		function e(tag, text, cssClass) {
			var el = document.createElement(tag);
			if (text) {
				el.appendChild(document.createTextNode(text));
			}
			if (cssClass) {
				el.setAttribute('class', cssClass);
			}
			return el;
		}

		function addSigner(signer) {
			table_single_td.appendChild(e('span', signer.name));
		}

		function thOrder(tr, type) {
			var th = e('th');
			tr.appendChild(th);
			var orderClass = '';
			if (order === (type + '_asc')) {
				orderClass = 'order-asc';
			}
			if (order === (type + '_desc')) {
				orderClass = 'order-desc';
			}
			var span = e('span', data.translations[type], 'order' + ' ' + orderClass);
			th.appendChild(span);
			span.appendChild(e('span', '↑', 'order-desc'));
			span.appendChild(e('span', '↓', 'order-asc'));
			span.setAttribute('data-toogle-order', type);

			if (type === 'date') {
				th.style.width = '165px';
			}
			if (type === 'city') {
				th.style.width = '25%';
			}
			if (type === 'country') {
				th.style.width = '25%';
			}

			span.addEventListener('click', toogleOrder);
		}

		function toogleOrder(e) {
			var name = this.getAttribute('data-toogle-order');
			if (name === 'date' && order !== 'date_desc') {
				order = 'date_desc';
			} else if ((name + '_asc') === order) {
				order = name + '_desc';
			} else {
				order = name + '_asc';
			}

			fetch(1);
		}

		function addHeadRow(table) {
			var thead = e('thead');
			table.appendChild(thead);
			var tr = e('tr');
			thead.appendChild(tr);

			thOrder(tr, 'date');
			thOrder(tr, 'name');
			if (with_city) {
				thOrder(tr, 'city');
			}
			if (with_country) {
				thOrder(tr, 'country');
			}
		}
		function addSignerRow(signer) {
			var tr = e('tr');
			var date = new Date(signer.date + 'Z');
			var dateLocale = date.toLocaleString ? date.toLocaleString(locale, {
				year: 'numeric',
				month: '2-digit',
				day: '2-digit',
				hour: '2-digit',
				minute: '2-digit'
			}) : signer.date;
			table_body.appendChild(tr);
			tr.appendChild(e('td', dateLocale));
			tr.appendChild(e('td', signer.name));
			if (with_city) {
				tr.appendChild(e('td', signer.city));
			}
			if (with_country) {
				tr.appendChild(e('td', signer.country + (signer.country in data.countries ? ' ' + data.countries[signer.country] : '')));
			}
		}

		function fetch(page) {
			if (!window.XMLHttpRequest) {
				return;
			}
			var xhttp = new XMLHttpRequest();

			xhttp.onreadystatechange = function () {
				if (xhttp.readyState === 4 && xhttp.status === 200) {
					var result = JSON.parse(xhttp.responseText);
					fields = result.fields;
					table = fields.length > 2;
					with_country = fields.indexOf('country') > -1;
					with_city = fields.indexOf('city') > -1;

					while (signers_div.firstChild) {
						signers_div.removeChild(signers_div.firstChild);
					}

					if (table) {
						var t = e('table', null, 'table table-condensed');
						signers_div.appendChild(t);
						table_body = e('tbody');
						t.appendChild(table_body);
						addHeadRow(t);
					} else {
						var t2 = e('table', null, 'table table-condensed no-tr-hover');
						signers_div.appendChild(t2);
						table_body = e('tbody');
						addHeadRow(t2);
						t2.appendChild(table_body);
						var tr = e('tr');
						table_single_td = e('td', null, 'signers-list');
						table_body.appendChild(tr);
						table_single_td.setAttribute('colspan', 2);
						tr.appendChild(table_single_td);
					}

					for (var i = 0; i < result.signers.length; i++) {
						if (table) {
							addSignerRow(result.signers[i]);
						} else {
							addSigner(result.signers[i]);
						}
					}

					pager(result.page, result.pages);
				}

				if (xhttp.readyState === 4 && xhttp.status === 404) {
					alert('Got no data from server. Maybe list was disabled. (Status 404)');
				}

				waiting.style.display = 'none';
			};

			xhttp.open('GET', '/api/v2/actions/' + data.id + '/list-signings/' + order + '/' + page, true);
			waiting.style.display = 'block';
			xhttp.send();
		}

		pager_ul.addEventListener('click', function (e) {
			var target = e.toElement || e.relatedTarget || e.target || false;
			if (target && target.dataset.page) {
				fetch(parseInt(target.dataset.page, 10));
			}
		});

		function addPage(number, active) {
			var li = e('li');
			var a = e('a');
			var text = document.createTextNode(number);
			li.appendChild(a);
			a.appendChild(text);
			pager_ul.appendChild(li);
			if (number === active) {
				li.setAttribute("class", "active");
			} else {
				if (active !== false) {
					li.style.cursor = 'pointer';
					a.dataset.page = number;
				}
			}
		}

		function pager(page, pages) {
			while (pager_ul.firstChild) {
				pager_ul.removeChild(pager_ul.firstChild);
			}

			if (pages < 2) {
				return null;
			}

			var elements = [1];
			for (var i = page - 2; i < page + 3; i++) {
				if (i > 1 && i < pages) {
					elements.push(i);
				}
			}
			elements.push(pages);

			for (var j = 0; j < elements.length; j++) {
				addPage(elements[j], page);

				if (j < (elements.length - 1) && (elements[j + 1] !== (elements[j] + 1))) {
					addPage('...', false);
				}
			}
		}

		fetch(1);

	})(document, window);
}, false);
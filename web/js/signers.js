document.addEventListener('DOMContentLoaded', function () {
	(function (document, window) {
		var waiting = document.getElementById('waiting');
		var signers_div = document.getElementById('signers');

		if (!signers_div) {
			return;
		}

		var pager_ul = document.getElementById('pager');
		var data = JSON.parse(signers_div.dataset.signers);

		function addSigner(signer) {
			var span = document.createElement('span');
			var text = document.createTextNode(signer.name);
			span.appendChild(text);
			signers_div.appendChild(span);
		}

		function fetch(page) {
			if (!window.XMLHttpRequest) {
				return;
			}
			var xhttp = new XMLHttpRequest();

			xhttp.onreadystatechange = function () {
				if (xhttp.readyState === 4 && xhttp.status === 200) {
					var result = JSON.parse(xhttp.responseText);

					while (signers_div.firstChild) {
						signers_div.removeChild(signers_div.firstChild);
					}

					for (var i = 0; i < result.signers.length; i++) {
						addSigner(result.signers[i]);
					}

					pager(result.page, result.pages);
				}
				
				if (xhttp.readyState === 4 && xhttp.status === 404) {
					alert('Got no data from server. Maybe list was disabled. (Status 404)');
				}

				waiting.style.display = 'none';
			};

			xhttp.open('GET', '/api/v2/actions/' + data.id + '/last-signings/' + page + '/large', true);
			waiting.style.display = 'block';
			xhttp.send();
		}

		pager_ul.addEventListener('click', function(e) {
			var target = e.toElement || e.relatedTarget || e.target || false;
			if (target && target.dataset.page) {
				fetch(parseInt(target.dataset.page, 10));
			}
		});

		function addPage(number, active) {
			var li = document.createElement('li');
			var a = document.createElement('a');
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
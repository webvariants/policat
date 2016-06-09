document.addEventListener('DOMContentLoaded', function () {
	(function (document, window) {

		var signers_div = document.getElementById('signers');
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

					for (var i = 0; i < result.signers.length; i++) {
						addSigner(result.signers[i]);
					}
				}
			};

			xhttp.open('GET', '/api/v2/actions/' + data.id + '/last-signings/' + page + '/large', true);
			xhttp.send();
		}

		fetch(1);

	})(document, window);
}, false);
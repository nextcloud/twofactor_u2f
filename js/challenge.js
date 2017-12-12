/* global OCA */

define(function (require) {
	'use strict';

	var u2f = require('u2f-api');

	function toggleError(state) {
		var $info = $('#u2f-info');
		var $error = $('#u2f-error');
		if (state) {
			$info.hide();
			$error.show();
		} else {
			$info.show();
			$error.hide();
		}
	}

	function signCallback(data) {
		var $form = $('#u2f-form');
		var $auth = $('#challenge');
		if (data.errorCode) {
			console.error('U2F auth failed: ' + data.errorCode);

			toggleError(true);
			setTimeout(sign, 5 * 1000);
			return;
		}
		$auth.val(JSON.stringify(data));
		$form.submit();
	}

	function checkHTTPS() {
		if (document.location.protocol !== 'https:') {
			$('#u2f-http-warning').show();
		}
	}

	function sign() {
		checkHTTPS();
		var req = JSON.parse($('#u2f-auth').val());

		toggleError(false);
		u2f.sign(req).then(signCallback);
	}

	$(sign);

});

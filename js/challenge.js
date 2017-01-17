/* global OCA, u2f */

(function(OCA, u2f) {
	'use strict';

	OCA.TwoFactorU2F = OCA.TwoFactorU2F || {};

	function toggleError(state) {
		const $info = $('#u2f-info');
		const $error = $('#u2f-error');
		if (state) {
			$info.hide();
			$error.show();
		} else {
			$info.show();
			$error.hide();
		}
	}

	function signCallback(data) {
		const $form = $('#u2f-form');
		const $auth = $('#challenge');
		console.log("Authenticate callback", data);
		if (data.errorCode) {
			console.error('U2F auth failed: ' + data.errorCode);

			toggleError(true);
			setTimeout(sign, 5 * 1000);
			return;
		}
		$auth.val(JSON.stringify(data));
		$form.submit();
	}

	function sign() {
		const req = JSON.parse($('#u2f-auth').val());

		toggleError(false);
		var pathArray = location.href.split('/');
		var protocol = pathArray[0];
		var host = pathArray[2];
		var url = protocol + '//' + host;
		console.log("sign: ", req);
		u2f.sign(url, req[0].challenge, req, signCallback);
	}

	$(sign);

})(OCA || {}, u2f);

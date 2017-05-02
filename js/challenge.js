/* global OCA, u2f */

(function(OCA, u2f) {
	'use strict';

	OCA.TwoFactorU2F = OCA.TwoFactorU2F || {};

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
		var req = JSON.parse($('#u2f-auth').val());

		toggleError(false);
		console.log("sign: ", req);
		u2f.sign(req, signCallback);
	}

	$(sign);

})(OCA || {}, u2f);

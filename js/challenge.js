/* global OCA, u2f */

(function (OCA, u2f) {
	'use strict';

	OCA.TwoFactor_U2F = OCA.TwoFactor_U2F || {};

	$(function () {
		var req = JSON.parse($('#u2f-auth').val());
		console.log("sign: ", req);
		u2f.sign(req, function (data) {
			var $form = $('#u2f-form');
			var $auth = $('#challenge');
			console.log("Authenticate callback", data);
			if (data.errorCode) {
				console.error('U2F auth failed: ' + data.errorCode);
				return;
			}
			$auth.val(JSON.stringify(data));
			$form.submit();
		});
	});
})(OCA || {}, u2f);

/* global OCA, u2f */

(function (OCA, u2f) {
	'use strict';

	OCA.TwoFactorU2F = OCA.TwoFactorU2F || {};

	$(function () {
		var req = JSON.parse($('#u2f-auth').val());
		console.log("sign: ", req);
		var pathArray = location.href.split('/');
		var protocol = pathArray[0];
		var host = pathArray[2];
		var url = protocol + '//' + host;
		u2f.sign(url, req[0].challenge, req, function (data) {
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

/* global OC */

(function (OC) {
	'use strict';

	$(function () {
		var view = new OCA.TwoFactorU2F.SettingsView({
			el: $('#twofactor-u2f-settings')
		});
		view.load();
	});
})(OC);

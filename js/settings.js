define(function () {
	'use strict';

	var $ = require('jquery');

	var SettingsView = require('./settingsview');

	$(function () {
		var view = new SettingsView({
			el: $('#twofactor-u2f-settings')
		});
		view.load();
	});
});

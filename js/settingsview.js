/* global Backbone, Handlebars, OC, u2f */

(function (OC, Backbone, Handlebars, $, u2f) {
	'use strict';

	OC.Settings = OC.Settings || {};
	OC.Settings.TwoFactorU2F = OC.Settings.TwoFactorU2F || {};

	var TEMPLATE = '<div>'
		+ '	<button id="u2f-register">Register</button>'
		+ '</div>';

	var View = Backbone.View.extend({
		template: Handlebars.compile(TEMPLATE),
		events: {
			'click #u2f-register': 'onRegister'
		},
		initialize: function () {

		},
		render: function (data) {
			this.$el.html(this.template(data));
		},
		onRegister: function () {
			console.log('start register…');
			var url = OC.generateUrl('apps/twofactor_u2f/settings/startregister');
			$.ajax(url, {
				method: 'POST'
			}).done(function (data) {
				this.doRegister(data.req, data.sigs);
			}.bind(this));
		},
		doRegister: function (req, sigs) {
			console.log('doRegister', req, sigs);
			u2f.register([req], sigs, function (data) {
				console.log(data.errorCode);
				if (data.errorCode && data.errorCode !== 0) {
					alert("registration failed with errror: " + data.errorCode);
					return;
				}
				this.finishRegister(data);
			}.bind(this));
		},
		finishRegister: function (data) {
			console.log('finish register…', data);
			var url = OC.generateUrl('apps/twofactor_u2f/settings/finishregister');
			$.ajax(url, {
				method: 'POST',
				data: data
			}).then(function() {
				OC.Notification.showTemporary('U2F device successfully registered');
				console.log('registration finished');
			});
		}
	});

	OC.Settings.TwoFactorU2F.View = View;

})(OC, Backbone, Handlebars, $, u2f);
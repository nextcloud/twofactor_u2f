/* global Backbone, Handlebars, OC, u2f */

(function(OC, Backbone, Handlebars, $, u2f) {
	'use strict';

	OC.Settings = OC.Settings || {};
	OC.Settings.TwoFactorU2F = OC.Settings.TwoFactorU2F || {};

	var TEMPLATE = '<div>'
		+ '	<input type="checkbox" class="checkbox" id="u2f-enabled" {{#if enabled}}checked{{/if}}>'
		+ '	<label for="u2f-enabled">' + t('twofactor_u2f', 'Use U2F device') + '</label>'
		+ '</div>';

	var View = Backbone.View.extend({
		_template: undefined,
		_enabled: undefined,
		template: function(data) {
			if (!this._template) {
				this._template = Handlebars.compile(TEMPLATE);
			}
			return this._template(data);
		},
		events: {
			'change #u2f-enabled': '_onToggleEnabled',
		},
		initialize: function() {
			this._load();
		},
		render: function() {
			this.$el.html(this.template({
				enabled: this._enabled
			}));
		},
		_load: function() {
			var url = OC.generateUrl('/apps/twofactor_u2f/settings/state');
			$.ajax(url, {
				method: 'GET',
			}).done(function(data) {
				this._enabled = data.enabled;
				this.render();
			}.bind(this)).fail(function() {
				OC.Notification.showTemporary('Could not get U2F enabled/disabled state.');
			});
		},
		_onToggleEnabled: function() {
			if (this._loading) {
				// Ignore event
				return;
			}

			var enabled = this.$('#u2f-enabled').is(':checked');

			if (enabled === this._enabled) {
				console.log('ign');
				return;
			}
			this._enabled = enabled;

			if (enabled) {
				this._onRegister();
			} else {
				this._onDisable();
			}
		},
		_onRegister: function() {
			this._loading = true;
			console.log('start register…');
			var url = OC.generateUrl('apps/twofactor_u2f/settings/startregister');
			$.ajax(url, {
				method: 'POST'
			}).done(function(data) {
				this.doRegister(data.req, data.sigs);
			}.bind(this)).fail(function() {
				OC.Notification.showTemporary('server error while trying to add U2F device');
			}).always(function() {
				this._loading = false;
			}.bind(this));
		},
		_onDisable: function() {
			this._loading = true;
			console.log('disabling U2F…');
			var url = OC.generateUrl('apps/twofactor_u2f/settings/disable');
			$.ajax(url, {
				method: 'POST'
			}).fail(function() {
				OC.Notification.showTemporary('Could not disable U2F');
			}.bind(this)).always(function() {
				this._loading = false;
			}.bind(this));
		},
		doRegister: function(req, sigs) {
			console.log('doRegister', req, sigs);
			u2f.register([req], sigs, function(data) {
				console.log(data.errorCode);
				if (data.errorCode && data.errorCode !== 0) {
					OC.Notification.showTemporary('U2F device registration failed (error code ' + data.errorCode + ')');
					this._enabled = false;
					this.render();
					return;
				}
				this.finishRegister(data);
			}.bind(this));
		},
		finishRegister: function(data) {
			console.log('finish register…', data);
			var url = OC.generateUrl('apps/twofactor_u2f/settings/finishregister');
			$.ajax(url, {
				method: 'POST',
				data: data
			}).then(function() {
				OC.Notification.showTemporary('U2F device successfully registered');
				console.log('registration finished');
			}).fail(function() {
				OC.Notification.showTemporary('server error while trying to complete U2F device registration');
			});
		}
	});

	OC.Settings.TwoFactorU2F.View = View;

})(OC, Backbone, Handlebars, $, u2f);
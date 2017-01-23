/* global Backbone, Handlebars, OC, u2f, Promise */

(function (OC, Backbone, Handlebars, $, u2f) {
	'use strict';

	OC.Settings = OC.Settings || {};
	OC.Settings.TwoFactorU2F = OC.Settings.TwoFactorU2F || {};

	var TEMPLATE = '<div>'
		+ '	{{#unless loading}}'
		+ '	<input type="checkbox" class="checkbox" id="u2f-enabled" {{#if enabled}}checked{{/if}}>'
		+ '	<label for="u2f-enabled">' + t('twofactor_u2f', 'Use U2F device') + '</label>'
		+ '	{{else}}'
		+ '	<span class="icon-loading-small u2f-loading"></span>'
		+ '	<span>' + t('twofactor_u2f', 'Use U2F device') + '</span>'
		+ '	{{/unless}}'
		+ '</div>';

	/**
	 * @class OC.Settings.TwoFactorU2F.View
	 */
	var View = Backbone.View.extend({
		/**
		 * @type {function|undefined}
		 */
		_template: undefined,

		/**
		 * @type {boolean}
		 */
		_enabled: undefined,

		/**
		 * @type {boolean}
		 */
		_loading: false,

		/**
		 * @param {object} data
		 * @returns {string}
		 */
		template: function (data) {
			if (!this._template) {
				this._template = Handlebars.compile(TEMPLATE);
			}
			return this._template(data);
		},

		events: {
			'change #u2f-enabled': '_onToggleEnabled'
		},

		/**
		 * @returns {undefined}
		 */
		initialize: function () {
			this._load();
		},

		/**
		 * @returns {undefined}
		 */
		render: function () {
			this.$el.html(this.template({
				enabled: this._enabled,
				loading: this._loading
			}));
		},

		/**
		 * @returns {undefined}
		 */
		_load: function () {
			var url = OC.generateUrl('/apps/twofactor_u2f/settings/state');
			Promise.resolve($.ajax(url, {
				method: 'GET'
			})).then(function (data) {
				this._enabled = data.enabled;
				this.render();
			}.bind(this)).catch(function (e) {
				console.error(e);
				OC.Notification.showTemporary('Could not get U2F enabled/disabled state.');
			});
		},

		/**
		 * @returns {undefined}
		 */
		_onToggleEnabled: function () {
			if (this._loading) {
				// Ignore event
				return;
			}

			var enabled = this.$('#u2f-enabled').is(':checked');

			if (enabled === this._enabled) {
				return;
			}
			this._enabled = enabled;

			if (enabled) {
				this._onRegister();
			} else {
				this._onDisable();
			}
		},

		/**
		 * @returns {undefined}
		 */
		_onRegister: function () {
			this._loading = true;
			this.render();

			console.log('start register…');

			var url = OC.generateUrl('apps/twofactor_u2f/settings/startregister');
			var self = this;
			Promise.resolve($.ajax(url, {
				method: 'POST'
			})).then(function (data) {
				self.doRegister(data.req, data.sigs);
			}).catch(function (e) {
				console.error(e);
				OC.Notification.showTemporary('server error while trying to add U2F device');
				self._loading = false;
				self.render();
			});
		},

		/**
		 * @returns {undefined}
		 */
		_onDisable: function () {
			this._loading = true;
			this.render();
			console.log('disabling U2F…');

			var url = OC.generateUrl('apps/twofactor_u2f/settings/disable');
			var self = this;
			Promise.resolve($.ajax(url, {
				method: 'POST'
			})).done(function () {
				OC.Notification.showTemporary('Could not disable U2F');
			}).catch(console.error.bind(this)).then(function () {
				self._loading = false;
				self.render();
			});
		},

		/**
		 * @returns {Promise}
		 */
		_requirePasswordConfirmation: function () {
			if (!OC.PasswordConfirmation.requiresPasswordConfirmation()) {
				return Promise.resolve();
			}
			return new Promise(function (resolve) {
				OC.PasswordConfirmation.requirePasswordConfirmation(resolve);
			});
		},

		/**
		 * @param {object} req
		 * @param {object} sigs
		 * @returns {undefined}
		 */
		doRegister: function (req, sigs) {
			console.log('doRegister', req, sigs);
			$('.utf-register-info').slideDown();
			var pathArray = location.href.split('/');
			var protocol = pathArray[0];
			var host = pathArray[2];
			var url = protocol + '//' + host;
			u2f.register(url, [req], sigs, function (data) {
				console.log(data);
				if (data.errorCode && data.errorCode !== 0) {
					OC.Notification.showTemporary('U2F device registration failed (error code ' + data.errorCode + ')');
					this._enabled = false;
					this._loading = false;
					this.render();
					$('.utf-register-info').slideUp();
					return;
				}
				this.finishRegister(data);
			}.bind(this));
		},

		/**
		 * @param {object} data
		 * @returns {undefined}
		 */
		finishRegister: function (data) {
			console.log('finish register…', data);

			var url = OC.generateUrl('apps/twofactor_u2f/settings/finishregister');
			var self = this;
			Promise.resolve($.ajax(url, {
				method: 'POST',
				data: data
			})).catch(function () {
				OC.Notification.showTemporary('server error while trying to complete U2F device registration');
			}).then(function () {
				$('.utf-register-info').slideUp();
				self._loading = false;
				self.render();
			});
		}
	});

	OC.Settings.TwoFactorU2F.View = View;

})(OC, Backbone, Handlebars, $, u2f);

/* global Backbone, Handlebars, OC, u2f, Promise */

(function (OC, OCA, Backbone, Handlebars, $, u2f) {
	'use strict';

	OCA.TwoFactorU2F = OCA.TwoFactorU2F || {};

	var TEMPLATE = ''
		+ '<div>'
		+ '	{{#unless loading}}'
		+ '	<input type="checkbox" class="checkbox" id="u2f-enabled" {{#if enabled}}checked{{/if}}>'
		+ '	<label for="u2f-enabled">' + t('twofactor_u2f', 'Use U2F device') + '</label>'
		+ '	{{else}}'
		+ '	<span class="icon-loading-small u2f-loading"></span>'
		+ '	<span>' + t('twofactor_u2f', 'Use U2F device') + '</span>'
		+ '	{{/unless}}'
		+ '</div>';

	/**
	 * @class 
	 */
	var SettingsView = Backbone.View.extend({

		/**
		 * @type {function|undefined}
		 */
		_template: undefined,

		/**
		 * @type {boolean}
		 */
		_enabled: false,

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
		render: function () {
			this.$el.html(this.template({
				enabled: this._enabled,
				loading: this._loading
			}));
		},

		/**
		 * @returns {Promise}
		 */
		load: function () {
			var url = OC.generateUrl('/apps/twofactor_u2f/settings/state');
			return Promise.resolve($.ajax(url, {
				method: 'GET'
			})).then(function (data) {
				this._enabled = data.enabled;
				this.render();
			}.bind(this)).catch(function (e) {
				throw e;
				OC.Notification.showTemporary('Could not get U2F enabled/disabled state.');
			}).catch(console.error.bind(this));
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

			var self = this;
			this._requirePasswordConfirmation()
				.then(this._startRegistrationOnServer)
				.then(function (data) {
					return self._registerU2fDevice(data.req, data.sigs);
				})
				.then(this.finishRegisterOnServer)
				.catch(function (e) {
					console.error(e);
					OC.Notification.showTemporary(e.message);
					self._enabled = false;
				})
				.then(function () {
					self._loading = false;
					self.render();
				});
		},

		/**
		 * @returns {Promise}
		 */
		_startRegistrationOnServer: function () {
			var url = OC.generateUrl('apps/twofactor_u2f/settings/startregister');
			return Promise.resolve($.ajax(url, {
				method: 'POST'
			})).catch(function () {
				throw new Error(t('twofactor_u2f', 'Server error while trying to add U2F device'));
			});
		},

		/**
		 * @returns {undefined}
		 */
		_onDisable: function () {
			this._loading = true;
			this.render();
			console.log('disabling U2F…');

			var self = this;
			this._requirePasswordConfirmation()
				.then(this._disableU2fOnServer)
				.catch(function (e) {
					console.error(e);
					OC.Notification.showTemporary(e);
				})
				.then(function () {
					self._loading = false;
					self.render();
				});
		},

		/**
		 * @returns {Promise}
		 */
		_disableU2fOnServer: function () {
			var url = OC.generateUrl('apps/twofactor_u2f/settings/disable');
			return Promise.resolve($.ajax(url, {
				method: 'POST'
			})).catch(function () {
				throw new Error(t('twofactor_u2f', 'Server error while disabling U2F'));
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
		 * @returns {Promise}
		 */
		_registerU2fDevice: function (req, sigs) {
			$('.utf-register-info').slideDown();
			var pathArray = location.href.split('/');
			var protocol = pathArray[0];
			var host = pathArray[2];
			var url = protocol + '//' + host;

			return new Promise(function (resolve, reject) {
				console.log('doRegister', req, sigs);
				u2f.register(url, [req], sigs, function (data) {
					console.log(data);
					if (data.errorCode && data.errorCode !== 0) {
						$('.utf-register-info').slideUp();
						reject(new Error(t('twofactor_u2f', 'U2F device registration failed (error code {errorCode})', {
							errorCode: data.errorCode
						})));
						return;
					}
					resolve(data);
				});
			});
		},

		/**
		 * @param {object} data
		 * @returns {Promise}
		 */
		finishRegisterOnServer: function (data) {
			console.log('finish register…', data);

			var url = OC.generateUrl('apps/twofactor_u2f/settings/finishregister');
			return Promise.resolve($.ajax(url, {
				method: 'POST',
				data: data
			})).catch(function () {
				throw new Error(t('twofactor_u2f', 'Server error while trying to complete U2F device registration'));
			}).then(function () {
				$('.utf-register-info').slideUp();
			});
		}
	});

	OCA.TwoFactorU2F.SettingsView = SettingsView;

})(OC, OCA, Backbone, Handlebars, $, u2f);

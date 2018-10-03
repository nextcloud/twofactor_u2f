/* OC, t, Handlebars */

define(function () {
	'use strict';

	var _ = require('underscore');
	var $ = require('jquery');
	var Backbone = require('backbone');
	var u2f = require('u2f-api');

	var TEMPLATE = ''
		+ '<div>'
		+ '	{{#unless loading}}'
		+ '     <div>'
		+ '         {{#unless devices.length}}'
		+ '         <p>' + t('twofactor_u2f', 'No U2F devices configured. You are not using U2F as second factor at the moment.') + '</p>'
		+ '         {{else}}'
		+ '         <p>' + t('twofactor_u2f', 'The following devices are configured for U2F second-factor authentication:') + '</p>'
		+ '         {{/unless}}'
		+ '         {{#each devices}}'
		+ '         <div class="u2f-device" data-u2f-id="{{id}}">'
		+ '             <span class="icon-u2f-device"></span>'
		+ '             <span>{{#if name}}{{name}}{{else}}' + t('twofactor_u2f', 'Unnamed device') + '{{/if}}</span>'
		+ '             <span class="more">'
		+ '                 <a class="icon icon-more"></a>'
		+ '                 <div class="popovermenu">'
		+ '                     <ul>'
		+ '                         <li class="remove-device">'
		+ '                             <a><span class="icon-delete"></span><span>' + t('twofactor_u2f', 'Remove') + '</span></a>'
		+ '                         </li>'
		+ '                     </ul>'
		+ '                 </div>'
		+ '             </span>'
		+ '         </div>'
		+ '         {{/each}}'
		+ '     </div>'
		+ '     <input  id="u2f-device-name" type="text" placeholder="Name your device">'
		+ '	<button id="add-u2f-device">' + t('twofactor_u2f', 'Add U2F device') + '</button><br>'
		+ '     <p><em>' + t('twofactor_u2f', 'You can add as many devices as you like. It is recommended to give each device a distinct name.') + '</em></p>'
		+ '	{{else}}'
		+ '     <span class="icon-loading-small u2f-loading"></span>'
		+ '	<span>' + t('twofactor_u2f', 'Adding a new device …') + '</span>'
		+ '	{{/unless}}'
		+ '</div>';

	/**
	 * @class
	 */
	var SettingsView = Backbone.View.extend(/** @lends Backbone.View */ {

		/**
		 * @type {function|undefined}
		 */
		_template: undefined,

		/**
		 * @type {boolean}
		 */
		_loading: false,

		/**
		 * @type {Object[]}
		 */
		_devices: undefined,

		/**
		 * @param {object} data
		 * @returns {string}
		 */
		template: function (data) {
			if (!this._template) {
				console.log('compiling u2f settings template');
				this._template = Handlebars.compile(TEMPLATE);
			}
			return this._template(data);
		},

		events: {
			'click #add-u2f-device': '_onAddU2FDevice',
			'keydown #u2f-device-name': '_onInputKeyDown',
			'click .u2f-device .remove-device': '_onRemoveDevice'
		},

		/**
		 * @returns {undefined}
		 */
		render: function () {
			console.log('rendering u2f settings view');
			this._checkHTTPS();

			this._devices = _.sortBy(this._devices, function (device) {
				// Underscore's stable sort requires a value for each item
				return device.name || '';
			});

			this.$el.html(this.template({
				loading: this._loading,
				devices: this._devices
			}));

			_.each(this._devices, function (device) {
				var $deviceEl = this.$('div[data-u2f-id="' + device.id + '"]');
				OC.registerMenu($deviceEl.find('a.icon-more'), $deviceEl.find('.popovermenu'));
			}, this);

			console.log('u2f settings view rendered');

			return this;
		},

		_checkHTTPS: function () {
			if (document.location.protocol !== 'https:') {
				console.error('u2f requires https');
				$('#u2f-http-warning').show();
			} else {
				console.log('https connection detected');
			}
		},

		/**
		 * @private
		 * @returns {Promise}
		 */
		_getServerState: function () {
			var url = OC.generateUrl('/apps/twofactor_u2f/settings/state');
			return Promise.resolve($.ajax(url, {
				method: 'GET'
			}));
		},

		/**
		 * @returns {Promise}
		 */
		load: function () {
			return this._getServerState().then(function (data) {
				this._devices = data.devices;
				this.render();
			}.bind(this), function () {
				console.error('Could not load list of u2f devices');
				OC.Notification.showTemporary('Could not load list of U2F devices.');
			}).catch(console.error.bind(this));
		},

		/**
		 * @private
		 * @returns {Promise}
		 */
		_onAddU2FDevice: function () {
			if (this._loading) {
				console.log('view is loading, ignoring `_onAddU2FDevice` call');
				// Ignore event
				return Promise.resolve();
			}

			return this._onRegister();
		},

		/**
		 * @private
		 * @param {Event} e
		 */
		_onInputKeyDown: function (e) {
			if (e.which === 13) {
				return this._onAddU2FDevice();
			}
			return Promise.resolve();
		},

		/**
		 * @private
		 * @returns {Promise}
		 */
		_onRemoveDevice: function (e) {
			var deviceId = $(e.target).closest('.u2f-device').data('u2f-id');
			var device = _.find(this._devices, function (device) {
				return device.id === deviceId;
			}, this);
			if (!device) {
				console.error('Cannot remove u2f device: unkown');
				return Promise.reject('Unknown u2f device');
			}

			return this._requirePasswordConfirmation().then(function () {
				// Remove visually
				this._devices.splice(this._devices.indexOf(device), 1);
				this.render();

				// Remove on server
				return this._removeOnServer(device);
			}.bind(this)).catch(function (e) {
				this._devices.push(device);
				this.render();
				console.error(e);
				OC.Notification.showTemporary(t('twofactor_u2f', 'Could not remove your U2F device'));
				throw new Error('Could not remove u2f device on server');
			}.bind(this)).catch(function (e) {
				console.error('Unexpected error while removing the u2f device', e);
				throw e;
			});
		},

		/**
		 * @private
		 * @returns {Promise}
		 */
		_onRegister: function () {
			console.log('starting u2f registration');
			var name = this.$('#u2f-device-name').val();

			// Show loading feedback
			this._loading = true;
			this.render();

			var self = this;
			return this._requirePasswordConfirmation()
				.then(this._startRegistrationOnServer)
				.then(function (data) {
					console.log('got server u2f registration data');
					return self._registerU2fDevice(data.req, data.sigs);
				})
				.then(function (data) {
					console.log('finished client-side u2f registration');
					data.name = name;
					return self._finishRegisterOnServer(data);
				})
				.then(function (newDevice) {
					console.log('finished server-side u2f registration');
					self._devices.push(newDevice);
				})
				.catch(function (e) {
					console.error(e);
					OC.Notification.showTemporary('Error while registering u2f device: ' + e.message);
				})
				.then(function () {
					console.log('finished u2f registration');
					self._loading = false;
					self.render();
				});
		},

		/**
		 * @private
		 * @returns {Promise}
		 */
		_startRegistrationOnServer: function () {
			var url = OC.generateUrl('/apps/twofactor_u2f/settings/startregister');
			return Promise.resolve($.ajax(url, {
				method: 'POST'
			})).catch(function (e) {
				console.error(e);
				throw new Error(t('twofactor_u2f', 'Server error while trying to add U2F device'));
			});
		},

		/**
		 * @private
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
		 * @private
		 * @param {object} req
		 * @param {object} sigs
		 * @returns {Promise}
		 */
		_registerU2fDevice: function (req, sigs) {
			$('.utf-register-info').slideDown();
			return u2f.register([req], sigs).then(function (data) {
				return new Promise(function (resolve, reject) {
					if (data.errorCode && data.errorCode !== 0) {
						$('.utf-register-info').slideUp();

						// https://developers.yubico.com/U2F/Libraries/Client_error_codes.html
						switch (data.errorCode) {
							case 4:
								// 4 - DEVICE_INELIGIBLE
								reject(new Error(t('twofactor_u2f', 'U2F device is already registered (error code {errorCode})', {
									errorCode: data.errorCode
								})));
								break;
							case 5:
								// 5 - TIMEOUT
								reject(new Error(t('twofactor_u2f', 'U2F device registration timeout reached (error code {errorCode})', {
									errorCode: data.errorCode
								})));
								break;
							default:
								// 1 - OTHER_ERROR
								// 2 - BAD_REQUEST
								// 3 - CONFIGURATION_UNSUPPORTED
								reject(new Error(t('twofactor_u2f', 'U2F device registration failed (error code {errorCode})', {
									errorCode: data.errorCode
								})));
						}
						return;
					}
					resolve(data);
				});
			});
		},

		/**
		 * @private
		 * @param {Object} data
		 * @param {string} data.name device name (specified by the user)
		 * @returns {Promise}
		 */
		_finishRegisterOnServer: function (data) {
			var url = OC.generateUrl('/apps/twofactor_u2f/settings/finishregister');
			return Promise.resolve($.ajax(url, {
				method: 'POST',
				data: data
			})).catch(function (e) {
				console.error(e);
				throw new Error(t('twofactor_u2f', 'Server error while trying to complete U2F device registration'));
			}).then(function (data) {
				$('.utf-register-info').slideUp();
				return data;
			});
		},

		/**
		 * @private
		 * @param {Object} device
		 * @returns {Promise}
		 */
		_removeOnServer: function (device) {
			var url = OC.generateUrl('/apps/twofactor_u2f/settings/remove');

			return Promise.resolve($.ajax(url, {
				method: 'POST',
				data: {
					id: device.id
				}
			})).catch(function (e) {
				console.error(e);
				throw e;
			});
		}
	});

	return SettingsView;

});

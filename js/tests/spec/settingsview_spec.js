/* global expect, Promise, spyOn */

define(['settingsview'], function(SettingsView) {
	describe('Settings view', function() {

		var view;

		beforeEach(function() {
			jasmine.Ajax.install();
			view = new SettingsView();
		});

		afterEach(function() {
			jasmine.Ajax.uninstall();
		});

		it('fetches state from the server', function() {
			spyOn(OC.Notification, 'showTemporary');

			view.load();

			expect(jasmine.Ajax.requests.mostRecent().url).toBe('/apps/twofactor_u2f/settings/state');

			jasmine.Ajax.requests.mostRecent().respondWith({
				status: 200,
				contentType: 'application/json',
				responseText: JSON.stringify({
					enabled: false
				})
			});

			expect(OC.Notification.showTemporary).not.toHaveBeenCalled();
		});

		it('shows a notification if the state cannot be loaded from the server', function(done) {
			spyOn(OC.Notification, 'showTemporary');

			var loading = view.load();

			expect(jasmine.Ajax.requests.mostRecent().url).toBe('/apps/twofactor_u2f/settings/state');

			jasmine.Ajax.requests.mostRecent().respondWith({
				status: 500,
				contentType: 'application/json'
			});

			loading.then(function() {
				expect(OC.Notification.showTemporary).toHaveBeenCalled();
				done();
			}).catch(function(e) {
				done.fail(e);
			});
		});

		it('asks for password confirmation when the user enables u2f', function(done) {
			spyOn(OC.Notification, 'showTemporary');
			spyOn(view, '_getServerState').and.returnValue(Promise.resolve({
				devices: []
			}));
			spyOn(view, '_requirePasswordConfirmation').and.returnValue(Promise.reject({
				message: 'Wrong password'
			}));

			view.load().then(function() {
				view._onAddU2FDevice().then(function() {
					expect(OC.Notification.showTemporary).toHaveBeenCalledWith('Wrong password');
					done();
				}).catch(function(e) {
					done.fail(e);
				});
			}, function(e) {
				done.fail(e);
			});
		});

		it('lets the user register a new device', function(done) {
			spyOn(OC.Notification, 'showTemporary');
			spyOn(view, '_getServerState').and.returnValue(Promise.resolve({
				devices: []
			}));
			spyOn(view, '_registerU2fDevice').and.returnValue(Promise.resolve({}));
			spyOn(view, '_requirePasswordConfirmation').and.returnValue(Promise.resolve());
			jasmine.Ajax.stubRequest('/apps/twofactor_u2f/settings/startregister').andReturn({
				contentType: 'application/json',
				responseText: JSON.stringify({
					req: 'reqdata',
					sigs: 'sigsdata'
				})
			});
			jasmine.Ajax.stubRequest('/apps/twofactor_u2f/settings/finishregister').andReturn({
				contentType: 'application/json',
				responseText: JSON.stringify({})
			});

			view.load().then(function() {
				expect(view._getServerState).toHaveBeenCalled();
				return view._onAddU2FDevice().then(function() {
					expect(view._registerU2fDevice).toHaveBeenCalled();
					expect(OC.Notification.showTemporary).not.toHaveBeenCalled();
					done();
				});
			}).catch(function(e) {
				done.fail(e);
			});
		});

		it('lets the user remove a device', function(done) {
			spyOn(OC.Notification, 'showTemporary');
			spyOn(view, '_getServerState').and.returnValue(Promise.resolve({
				devices: [
					{
						id: 13,
						name: 'Yolokey'
					}
				]
			}));
			spyOn(view, '_requirePasswordConfirmation').and.returnValue(Promise.resolve());
			jasmine.Ajax.stubRequest('/apps/twofactor_u2f/settings/remove').andReturn({
				contentType: 'application/json',
				responseText: JSON.stringify({})
			});

			view.load().then(function() {
				expect(view._getServerState).toHaveBeenCalled();
				var fakeEvent = {
					target: view.$('.remove-device')
				};
				return view._onRemoveDevice(fakeEvent).then(function() {
					expect(OC.Notification.showTemporary).not.toHaveBeenCalled();
					done();
				});
			}).catch(function(e) {
				done.fail(e);
			});
		});

	});
});
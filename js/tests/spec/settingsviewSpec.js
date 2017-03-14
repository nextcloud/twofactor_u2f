/* global expect, Promise, spyOn */

describe('Settings view', function () {

	var view;

	beforeEach(function () {
		jasmine.Ajax.install();
		view = new OCA.TwoFactorU2F.SettingsView();
	});

	afterEach(function () {
		jasmine.Ajax.uninstall();
	});

	it('fetches state from the server', function () {
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
		expect(view.$el.find('#u2f-enabled').prop('checked')).toBeUndefined();
	});

	it('ticks the checkbox if u2f is enabled for the user', function (done) {
		spyOn(OC.Notification, 'showTemporary');

		var loading = view.load();

		expect(jasmine.Ajax.requests.mostRecent().url).toBe('/apps/twofactor_u2f/settings/state');

		jasmine.Ajax.requests.mostRecent().respondWith({
			status: 200,
			contentType: 'application/json',
			responseText: JSON.stringify({
				enabled: true
			})
		});

		loading.then(function () {
			expect(OC.Notification.showTemporary).not.toHaveBeenCalled();
			expect(view.$el.find('#u2f-enabled').prop('checked')).toBe(true);
			done();
		}).catch(function (e) {
			done.fail(e);
		});
	});

	it('shows a notification if the state cannot be loaded from the server', function (done) {
		spyOn(OC.Notification, 'showTemporary');

		var loading = view.load();

		expect(jasmine.Ajax.requests.mostRecent().url).toBe('/apps/twofactor_u2f/settings/state');

		jasmine.Ajax.requests.mostRecent().respondWith({
			status: 500,
			contentType: 'application/json'
		});

		loading.then(function () {
			expect(OC.Notification.showTemporary).toHaveBeenCalled();
			done();
		}).catch(function (e) {
			done.fail(e);
		});
	});

	it('asks for password confirmation when the user enables u2f', function (done) {
		spyOn(OC.Notification, 'showTemporary');
		spyOn(view, '_getServerState').and.returnValue(Promise.resolve({
			enabled: false
		}));
		spyOn(view, '_requirePasswordConfirmation').and.returnValue(Promise.reject({
			message: 'Wrong password'
		}));

		view.load().then(function () {
			view.$el.find('#u2f-enabled').prop('checked', true);
			view._onToggleEnabled().then(function () {
				expect(OC.Notification.showTemporary).toHaveBeenCalledWith('Wrong password');
				done();
			}).catch(function (e) {
				done.fail(e);
			});
		}, function (e) {
			done.fail(e);
		});
	});

	it('lets the user register a new device', function (done) {
		spyOn(OC.Notification, 'showTemporary');
		spyOn(view, '_getServerState').and.returnValue(Promise.resolve({
			enabled: false
		}));
		spyOn(view, '_registerU2fDevice').and.returnValue(Promise.resolve());
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

		view.load().then(function () {
			view.$el.find('#u2f-enabled').prop('checked', true);
			expect(view._getServerState).toHaveBeenCalled();
			return view._onToggleEnabled().then(function () {
				expect(view._registerU2fDevice).toHaveBeenCalled();
				expect(OC.Notification.showTemporary).not.toHaveBeenCalled();
				done();
			});
		}).catch(function (e) {
			done.fail(e);
		});
	});

});

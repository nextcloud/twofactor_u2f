/* global expect */

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
		expect(view.$el.find('#u2f-enabled').attr('checked')).toBeUndefined();
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
			expect(view.$el.find('#u2f-enabled').attr('checked')).toBe('checked');
			done();
		}).catch(function (e) {
			done.fail(e);
		});
	});

});

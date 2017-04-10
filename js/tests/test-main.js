
(function (global, Backbone) {
	// Global variable stubs
	global.OC = {};
	global.OC.generateUrl = function (url) {
		return url;
	};
	global.OC.Backbone = Backbone;
	global.OC.Notification = {};
	global.OC.Notification.showTemporary = function (txt) {
		console.error('temporary notification', txt)
	};
	global.OC.registerMenu = function () {

	};
	global.OCA = {};
	global.t = function (app, txt) {
		if (app !== 'twofactor_u2f') {
			throw Error('wrong app used for translatoin');
		}
		return txt;
	};
})(window, Backbone);

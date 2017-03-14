// Karma configuration

module.exports = function (config) {
	config.set({
		// frameworks to use
		// available frameworks: https://npmjs.org/browse/keyword/karma-adapter
		frameworks: ['jasmine-ajax', 'jasmine'],

		// list of files / patterns to load in the browser
		files: [
			'../../core/vendor/es6-promise/dist/es6-promise.js',
			'../../core/vendor/jquery/dist/jquery.js',
			'../../core/vendor/underscore/underscore.js',
			'../../core/vendor/backbone/backbone.js',
			'../../core/vendor/handlebars/handlebars.js',
			'js/tests/test-main.js',
			'js/tests/fake-u2f.js',
			{pattern: 'js/**/*.js', included: true},
			{pattern: 'js/tests/*.js', included: false}
		],

		// list of files to exclude
		exclude: [
			'js/vendor/**/*.js',
			'js/challenge.js',
			'js/settings.js'
		],

		// preprocess matching files before serving them to the browser
		// available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
		preprocessors: {
			'js/*.js': ['coverage']
		},

		// test results reporter to use
		// possible values: 'dots', 'progress'
		// available reporters: https://npmjs.org/browse/keyword/karma-reporter
		reporters: ['progress', 'coverage'],

		coverageReporter: {
			type: 'lcov',
			dir: 'coverage/'
		},

		// web server port
		port: 9876,

		// enable / disable colors in the output (reporters and logs)
		colors: true,

		// level of logging
		// possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
		logLevel: config.LOG_INFO,

		// enable / disable watching file and executing tests whenever any file changes
		autoWatch: true,

		// start these browsers
		// available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
		browsers: ['PhantomJS'],

		// Continuous Integration mode
		// if true, Karma captures browsers, runs the tests and exits
		singleRun: false
	});
};

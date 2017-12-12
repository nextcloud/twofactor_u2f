const path = require('path');
const webpack = require('webpack');

module.exports = {
	entry: {
		challenge: './js/challenge.js',
		settings: './js/settings.js'
	},
	output: {
		filename: '[name].js',
		path: __dirname + '/build'
	},
	resolve: {
		modules: [path.resolve(__dirname), 'node_modules'],
		alias: {
			'handlebars': 'handlebars/runtime.js'
		}
	},
	module: {
		rules: [
			{test: /davclient/, use: 'exports-loader?dav'},
			{
				test: /\.html$/, loader: "handlebars-loader", query: {
					extensions: '.html',
					helperDirs: __dirname + '/templatehelpers'
				}
			}
		],
		loaders: [
			{test: /ical/, loader: 'exports-loader?ICAL'}
		]
	}
};

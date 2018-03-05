const path = require('path');
const webpack = require('webpack');

module.exports = {
	entry: {
		challenge: './js/init_challenge.js',
		settings: './js/init_settings.js'
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
			{
				test: /\.html$/, loader: "handlebars-loader", query: {
					extensions: '.html',
					helperDirs: __dirname + '/templatehelpers'
				}
			}
		]
	}
};

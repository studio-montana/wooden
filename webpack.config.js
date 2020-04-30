const path = require('path');

/** gutenberg modules declaration */
let gutenberg_modules = [
	{'entry': 'index.jsx', 'name': 'eventmeta', 'path': 'tools/event/gutenberg/plugins/eventmeta/'}, // event tool's plugin
	{'entry': 'index.jsx', 'name': 'breadcrumbmeta', 'path': 'tools/breadcrumb/gutenberg/plugins/breadcrumbmeta/'}, // breadcrumb tool's plugin
	{'entry': 'index.jsx', 'name': 'wall', 'path': 'tools/wall/gutenberg/blocks/wall/'}, // wall tool's block
];

/** all configurations */
let configs = [];

/** common configuration */
var config = {
	module: {
		rules: [
			{
				test: /\.jsx?$/,
				exclude: /node_modules/,
				use: "babel-loader"
			}
		]
	},
	resolve: {
		alias: {
		  wkgcomponents: path.resolve(__dirname, '../../plugins/woodkit/src/gutenberg/components/'), // Woodkit Gutenberg dependencies : import ... from 'wkgcomponents/...'
		},
    	extensions: ['.js', '.jsx'], // import without extension
	}
};

/**
 * gutenberg modules configuration
 */
for (var gutenberg_module of gutenberg_modules) {
	configs.push(Object.assign({}, config, {
		name: gutenberg_module.name,
		entry: './src/'+gutenberg_module.path+gutenberg_module.entry,
		output: {
			path: path.resolve(__dirname, 'src/' + gutenberg_module.path),
			filename: 'build.js'
		},
	}));
}

/** return configurations */
module.exports = configs;

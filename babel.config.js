module.exports = {
	presets: ["@babel/preset-env"],
	plugins: [
		["@babel/plugin-transform-react-jsx", {"pragma": "wp.element.createElement"}], // build JSX script - using el() function from wp.element.createElement instead of React
		["@babel/plugin-syntax-object-rest-spread"], // allow JS spread operator and JS spread extracts - like : import { Humm } from './Yep'
		["@babel/plugin-transform-runtime"] // babel helpers
	]
};
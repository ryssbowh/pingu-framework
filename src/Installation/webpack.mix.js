const mix = require('laravel-mix');
const path = require('path');

var dir = __dirname;

var assetPath = __dirname + '/Resources/assets';
var publicPath = '/Resources/assets/public';

mix.setPublicPath(publicPath);

mix.autoload({
	'jquery': ['$', 'jQuery']
});

//Javascript
mix.js(assetPath + '/js/app.js', 'installer.js').sourceMaps();

//Css
mix.sass(assetPath + '/sass/app.scss', 'installer.css');
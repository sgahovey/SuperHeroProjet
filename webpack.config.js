const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addStyleEntry('tailwind', './assets/styles/tailwind.css')
    .enablePostCssLoader() // Pour Tailwind CSS
    .enableSingleRuntimeChunk();

module.exports = Encore.getWebpackConfig();

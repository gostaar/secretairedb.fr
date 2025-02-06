const Encore = require('@symfony/webpack-encore');
const dotenv = require('dotenv');
const webpack = require('webpack');

dotenv.config();

Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'production');
Encore.enableSingleRuntimeChunk();

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addEntry('loginStyle', './assets/styles/login.css')
    .addEntry('siteStyle', './assets/styles/site.css')
    .addEntry('userStyle', './assets/styles/user.css')
    // .addEntry('script', './assets/js/script.js')
    // .addEntry('google', './assets/js/google/main.js')
    // .addEntry('styles', './assets/styles/app.css')
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .copyFiles({
        from: './assets/img',
        to: 'images/[path][name].[ext]',
    })
    .addPlugin(
        new webpack.DefinePlugin({
            'process.env.GOOGLE_CLIENT_ID': JSON.stringify(process.env.GOOGLE_CLIENT_ID),
            'process.env.GOOGLE_API_KEY': JSON.stringify(process.env.GOOGLE_API_KEY),
            'process.env.GOOGLE_CLIENT_SECRET': JSON.stringify(process.env.GOOGLE_CLIENT_SECRET),
            'process.env.GOOGLE_OAUTH_SCOPE': JSON.stringify(process.env.GOOGLE_OAUTH_SCOPE),
            'process.env.REDIRECT_URI': JSON.stringify(process.env.REDIRECT_URI),
            'process.env.DISCOVERY_DOC': JSON.stringify(process.env.DISCOVERY_DOC),
        })
    );

module.exports = Encore.getWebpackConfig();
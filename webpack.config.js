// webpack.config.js
var Encore = require('@symfony/webpack-encore');

//DECLARATION OF THE NEW PLUGIN
let CopyWebpackPlugin = require('copy-webpack-plugin');
let HtmlWebpackPlugin = require('html-webpack-plugin');


Encore

    .enableCoffeeScriptLoader()
    // the project directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    .addEntry('app', './assets/js/app.js')
    .addEntry('store', './assets/js/store.js')
    .addEntry('pdf', './assets/css/pdf.scss')

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()

    .autoProvideVariables({
        Tether: "tether"
        //Routing: "./router"
    })

    .addPlugin(new CopyWebpackPlugin([
        // Copy the skins from tinymce to the build/skins directory
        { from: 'node_modules/tinymce/skins', to: 'skins' },
        { from: 'node_modules/tinymce/plugins', to: 'plugins' },
        { from: 'src/AppBundle/Resources/public/image', to: 'images' },
        { from: 'src/LilWorks/StoreBundle/Resources/public/image', to: 'images' },
    ]))


;



// export the final configuration
module.exports = Encore.getWebpackConfig();

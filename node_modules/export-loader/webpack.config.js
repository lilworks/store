const path = require('path')
const webpack = require('webpack')
const fs = require('fs')
let nodeModules = {}
fs.readdirSync('node_modules')
  .filter(function (x) {
    return ['.bin'].indexOf(x) === -1
  })
  .forEach(function (mod) {
    nodeModules[mod] = 'commonjs ' + mod
  })

module.exports = {
  // resolveLoader: {
  //   alias: {
  //     'export-loader': path.join(__dirname, 'src','export-loader.js')
  //   }
  // },
  externals:nodeModules,
  entry: './src/export-loader.js',
  // entry: './index.js',
  target: 'node',
  // externals: nodeModules,
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'bundle.js',
    libraryTarget: 'commonjs2'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        loader:"babel-loader",
        query: {
          presets: ["env"]
        }
      },
      // {
      //   test: /\.js$/,
      //   exclude: /(node_modules)/,
      //   loader:"export-loader",
      // },
    ]
  }
};
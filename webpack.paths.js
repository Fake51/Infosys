const path = require('path');

module.exports = {
  root: path.resolve(__dirname, './'),
  outputPath: path.resolve(__dirname, './', 'public'),
  entryPath: path.resolve(__dirname, './', 'assets/index.jsx'),
//  templatePath: path.resolve(__dirname, './', 'src/template.html'),
  imagesFolder: 'images',
  fontsFolder: 'fonts',
  cssFolder: 'css',
  jsFolder: 'js',
};

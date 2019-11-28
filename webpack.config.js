const path = require('path')

module.exports = (env, argv) => ({
  entry: './src/js/app.js',
  output: {
    path: path.resolve(__dirname, 'assets/js'),
    filename: 'lazy-embeds.js'
  },
  watch: argv.mode === 'development',
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      }
    ]
  },
  stats: {
    colors: true
  }
})

const mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js('resources/js/app.js', 'public/js')
  .webpackConfig({
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'frontend/src/'),
        '@themeConfig': path.resolve(__dirname, 'frontend/themeConfig.js'),
        '@core': path.resolve(__dirname, 'frontend/src/@core'),
        '@validations': path.resolve(__dirname, 'frontend/src/@core/utils/validations/validations.js'),
        '@axios': path.resolve(__dirname, 'frontend/src/libs/axios')
      }
    },
    module: {
      rules: [
        {
          test: /\.s[ac]ss$/i,
          use: [
            {
              loader: 'sass-loader',
              options: {
                sassOptions: {
                  includePaths: ['frontend/node_modules', 'frontend/src/assets']
                }
              }
            }
          ]
        },
        {
          test: /(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/,
          loaders: {
            loader: 'file-loader',
            options: {
              name: 'images/[path][name].[ext]',
              context: '../vuexy-vuejs-bootstrap-vue-template/src/assets/images'
            }
          }
        }
      ]
    },
    output: {
      chunkFilename: 'js/chunks/[name].js'
    }
  })
  .sass('resources/sass/app.scss', 'public/css')
  .options({
    postCss: [require('autoprefixer'), require('postcss-rtl')]
  })

if (mix.inProduction()) {
  mix.version()
}

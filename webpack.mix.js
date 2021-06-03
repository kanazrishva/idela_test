const mix = require('laravel-mix');
const ChunkRenamePlugin = require("webpack-chunk-rename-plugin");
const tailwindcss = require('tailwindcss')
require('laravel-mix-purgecss')
require('mix-env-file')

const ASSET_URL = `${process.env.ASSET_URL || ''}/`
const MIX_ASSET_URL = `${process.env.ASSET_URL || 'http://localhost:3000'}`

mix.setResourceRoot(ASSET_URL)
mix.babelConfig({
  plugins: ['@babel/plugin-syntax-dynamic-import'],
});

if (mix.inProduction()) {
  mix
    .env(process.env.ENV_FILE)
    .webpackConfig(webpack => {
      return {
        plugins: [
          new webpack.DefinePlugin({
            "process.env.ASSET_PATH": JSON.stringify(ASSET_URL),
            "process.env.MIX_ASSET_URL": JSON.stringify(MIX_ASSET_URL),
            "process.env.IMGIX_DOMAIN": JSON.stringify(process.env.IMGIX_DOMAIN),
          }),
          new ChunkRenamePlugin({
            initialChunksWithEntry: true,
            '/js/vendor': '/js/vendor.js'
          }),
        ],
        output: {
          publicPath: ASSET_URL,
          filename: '[name].js',
          chunkFilename: 'js/[name].js'
        },
        resolve: {
          alias: {
            vue$: 'vue/dist/vue.runtime.esm.js',
            '@': path.resolve('resources/js'),
          },
        },
      };
    })
  .js('resources/js/app.js', 'public/js')
  .extract(['vue', 'axios', 'jquery', 'apexcharts', 'lodash', 'vue-plotly'])
  .sass('resources/sass/app.scss', 'public/css')
  .sass('resources/sass/main.scss', 'public/css')
  .options({
    processCssUrls: false,
    postCss: [ tailwindcss('./tailwind.config.js')]
  })
  .purgeCss({
    enabled: true
  })
  .copyDirectory('resources/images', 'public/images')

} else {
  mix
  .webpackConfig(webpack => {
    return {
      // output: {
      //   filename: '[name].js',
      //   chunkFilename: 'js/[name].js'
      // },
      plugins: [
        new webpack.DefinePlugin({
          "process.env.ASSET_PATH": JSON.stringify(ASSET_URL),
          "process.env.MIX_ASSET_URL": JSON.stringify(MIX_ASSET_URL),
          "process.env.IMGIX_DOMAIN": JSON.stringify(process.env.IMGIX_DOMAIN),
        }),
        new ChunkRenamePlugin({
          initialChunksWithEntry: true,
          '/js/vendor': '/js/vendor.js'
        }),
      ],
      output: {
        publicPath: ASSET_URL,
        filename: '[name].js',
        chunkFilename: 'js/[name].js'
      },
      resolve: {
        alias: {
          vue$: 'vue/dist/vue.runtime.esm.js',
          '@': path.resolve('resources/js/'),
        },
      }
    }
  })
  .js('resources/js/app.js', 'public/js')
  .extract(['vue', 'axios', 'jquery', 'apexcharts', 'lodash', 'd3', 'vue-plotly'])
  .sass('resources/sass/app.scss', 'public/css')
  .sass('resources/sass/main.scss', 'public/css')
  .options({
    processCssUrls: false,
    postCss: [ tailwindcss('./tailwind.config.js')]
  })
  .purgeCss({
    enabled: false
  })
  .copyDirectory('resources/images', 'public/images')
}

mix.browserSync({
  proxy: 'ideladata.test',
  files: [
    "resources/js",
    "resources/sass"
  ]
});

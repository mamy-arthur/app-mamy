/*!

=========================================================
* Nuxt Argon Dashboard PRO - v1.3.0
=========================================================

* Product Page: https://www.creative-tim.com/product/nuxt-argon-dashboard-pro
* Copyright 2019 Creative Tim (https://www.creative-tim.com)

* Coded by www.creative-tim.com and www.binarcode.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/

const pkg = require('./package')

console.log('ENV', process.env.NODE_ENV)

require('dotenv').config()

module.exports = {
  env: {
    BASE_URL: process.env.BASE_URL || 'http://localhost:3000',
  },

  router: {
    base: '/',
    linkExactActiveClass: 'active',
    linkActiveClass: 'active',
    middleware: ['auth', 'user-permissions'],
  },

  /*
   ** Headers of the page
   */
  head: {
    title: 'Aéroport de Paris - Vatry',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      {
        hid: 'description',
        name: 'description',
        content:
          'Nuxt Argon Dashboard PRO - Premium Admin Nuxt.js & Bootstrap 4 Dashboard',
      },
    ],
    link: [
      { rel: 'icon', type: 'image/png', href: 'favicon.ico' },
      {
        rel: 'stylesheet',
        href: 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700',
      },
      {
        rel: 'stylesheet',
        href: 'https://use.fontawesome.com/releases/v5.6.3/css/all.css',
        integrity:
          'sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/',
        crossorigin: 'anonymous',
      },
    ],
  },

  /*
   ** Customize the progress-bar color
   */
  loading: { color: '#1D4293' },

  /*
   ** Global CSS
   */
  css: ['assets/css/nucleo/css/nucleo.css', 'assets/sass/argon.scss'],

  /*
   ** Plugins to load before mounting the App
   */
  plugins: [
    '~/plugins/dashboard/dashboard-plugin',
    { src: '~/plugins/dashboard/full-calendar', ssr: false },
    { src: '~/plugins/dashboard/world-map', ssr: false },
    '~/plugins/axios',
    '~/plugins/mixins/permissions-global.mixin',
  ],

  /*
   ** Nuxt.js modules
   */
  modules: [
    '@nuxtjs/axios', // https://axios.nuxtjs.org/usage
    '@nuxtjs/pwa',
    '@nuxtjs/auth-next',
    '@nuxtjs/redirect-module', // https://github.com/nuxt-community/redirect-module#readme
  ],

  /*
   ** Nuxt.js build modules
   */
  buildModules: ['@nuxtjs/dotenv'],

  /*
   ** Axios module configuration
   */
  axios: {
    baseURL: process.env.PUBLIC_API_BASE_URL,
  },

  /*
   ** Redirects
   */
  // redirect: [
  //   { from: '/insights', to: '/insights/flights-list', statusCode: 301 },
  //   // { from: '/parkings', to: '/insights/parking-stats', statusCode: 301 },
  // ],

  /*
   ** Auth module configuration
   */
  auth: {
    redirect: {
      login: '/login',
      logout: '/login',
      home: '/',
    },
    strategies: {
      local: {
        token: {
          property: 'token',
        },
        user: {
          property: false,
        },
        endpoints: {
          login: { url: '/auth-api/login', method: 'post' },
          logout: false,
          user: { url: '/auth-api/user', method: 'get' },
        },
      },
    },
  },

  publicRuntimeConfig: {
    axios: {
      browserBaseURL: process.env.PUBLIC_API_BASE_URL,
    },
  },

  privateRuntimeConfig: {
    axios: {
      baseURL:
        process.env.PRIVATE_API_BASE_URL || process.env.PUBLIC_API_BASE_URL,
    },
    isServer: true,
  },

  /*
   ** Build configuration
   */
  build: {
    transpile: ['vee-validate/dist/rules'],
    /*
     ** You can extend webpack config here
     */
    extend(config, ctx) {},
    extractCSS: process.env.NODE_ENV === 'production',
    babel: {
      plugins: [
        [
          'component',
          {
            libraryName: 'element-ui',
            styleLibraryName: 'theme-chalk',
          },
        ],
      ],
    },
  },
}

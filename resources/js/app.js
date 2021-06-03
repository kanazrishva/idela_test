import { InertiaApp } from '@inertiajs/inertia-vue'
import PortalVue from 'portal-vue'
import Notifications from 'vue-notification'
import Vue from 'vue'
import Echo from 'laravel-echo'
import VueLodash from 'vue-lodash'
import lodash from 'lodash'
import VueObserveVisibility from 'vue-observe-visibility'
import {asset} from '@codinglabs/laravel-asset'

window.$ = window.jQuery = require('jquery')
window.Vapor = require('laravel-vapor')
window.Pusher = require('pusher-js')

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  encrypted: true
})

Vue.mixin({
  methods: {
      asset: asset
  }
})

Vue.mixin({ 
  data() {
    return {
      imgix_domain: process.env.IMGIX_DOMAIN
    }
  },
  methods: { route: window.route }
 })

Vue.mixin({
  methods: {
    error(field, errorBag = 'default') {
      if (!this.$page.errors.hasOwnProperty(errorBag)) {
        return []
      }

      if (this.$page.errors[errorBag].hasOwnProperty(field)) {
        return this.$page.errors[errorBag][field]
      }

      return []
    }
  }
});

Vue.use(InertiaApp)
Vue.use(PortalVue)
Vue.use(Notifications)
Vue.use(VueLodash, { lodash: lodash })
Vue.use(VueObserveVisibility)


const app = document.getElementById('app')

new Vue({
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
    },
  }),
}).$mount(app)
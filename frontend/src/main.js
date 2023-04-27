import Vue from 'vue'
import { ToastPlugin, ModalPlugin } from 'bootstrap-vue'
import UUID from 'vue-uuid'
import router from './router'
import store from './store'
import App from './App.vue'

// Global Components
import './global-components'

// 3rd party plugins
import '@/libs/portal-vue'
import '@/libs/acl'

// BSV Plugin Registration
Vue.use(ToastPlugin)
Vue.use(ModalPlugin)

// UUID
Vue.use(UUID)

// import core styles
require('@core/scss/core.scss')

// import assets styles
require('@/assets/scss/style.scss')

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App),
  watch:{
    '$route' (to, from){
    }
  }
}).$mount('#app')

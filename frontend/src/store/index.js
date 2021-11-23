import Vue from 'vue'
import Vuex from 'vuex'

// Modules
import app from './app'
import appConfig from './app-config'
import verticalMenu from './vertical-menu'
import pcmCategories from './pcm-categories'
import pcmTemplates from './pcm-templates'
import pcmObjects from './pcm-objects'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    app,
    appConfig,
    verticalMenu,
    pcmCategories,
    pcmTemplates,
    pcmObjects
  },
  strict: process.env.DEV,
})

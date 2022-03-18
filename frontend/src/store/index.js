import Vue from 'vue'
import Vuex from 'vuex'

// Modules
import app from './app'
import appConfig from './app-config'
import verticalMenu from './vertical-menu'
import pcmLocations from './pcm-locations'
import pcmCategories from './pcm-categories'
import pcmTemplates from './pcm-templates'
import pcmFloorplanTemplates from './pcm-floorplan-templates'
import pcmObjects from './pcm-objects'
import pcmProps from './pcm-props'
import pcmTrunks from './pcm-trunks'
import pcmConnections from './pcm-connections'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    app,
    appConfig,
    verticalMenu,
    pcmLocations,
    pcmCategories,
    pcmTemplates,
    pcmFloorplanTemplates,
    pcmObjects,
    pcmProps,
    pcmTrunks,
    pcmConnections,
  },
  strict: process.env.DEV,
})
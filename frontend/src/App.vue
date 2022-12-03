<template>
  <div
    id="app"
    class="h-100"
    :class="[skinClasses]"
  >
    <component :is="layout">
      <router-view />
    </component>

  </div>
</template>

<script>

// This will be populated in `beforeCreate` hook
import { $themeColors, $themeBreakpoints, $themeConfig } from '@themeConfig'
import { provideToast } from 'vue-toastification/composition'
import { watch } from '@vue/composition-api'
import useAppConfig from '@core/app-config/useAppConfig'

import { useWindowSize, useCssVar } from '@vueuse/core'

import store from '@/store'

import { PCM } from '@/mixins/PCM.js'

const LayoutVertical = () => import('@/layouts/vertical/LayoutVertical.vue')
const LayoutHorizontal = () => import('@/layouts/horizontal/LayoutHorizontal.vue')
const LayoutFull = () => import('@/layouts/full/LayoutFull.vue')

export default {
  mixins: [PCM],
  components: {

    // Layouts
    LayoutHorizontal,
    LayoutVertical,
    LayoutFull,

  },
  data() {
    return {}
  },
  // ! We can move this computed: layout & contentLayoutType once we get to use Vue 3
  // Currently, router.currentRoute is not reactive and doesn't trigger any change
  computed: {
    layout() {
      if (this.$route.meta.layout === 'full') return 'layout-full'
      return `layout-${this.contentLayoutType}`
    },
    contentLayoutType() {
      return this.$store.state.appConfig.layout.type
    },
    UserData() {
      return JSON.parse(localStorage.getItem('userData'))
    },
    StateDataSyncTimestamp() {
      return this.$store.state.pcmState.DataSyncTimestamp
    },
    StateDataSyncIntervalID() {
      return this.$store.state.pcmState.DataSyncIntervalID
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    Organization() {
      return this.$store.state.pcmOrganization.Organization
    },
    CablePaths() {
      return this.$store.state.pcmCablePaths.CablePaths
    },
    Trunks() {
      return this.$store.state.pcmTrunks.Trunks
    },
  },
  methods: {
    DataSync() {

      const vm = this
      const timestamp = vm.StateDataSyncTimestamp
      const tables = [
        {
          'name': 'categories',
          'update_mutation': 'pcmCategories/UPDATE_Category',
          'add_mutation': 'pcmCategories/ADD_Category',
          'ready_mutation': 'pcmCategories/SET_Ready',
          'object': vm.Categories,
          'contexts': ['actual', 'workspace', 'template'],
          'custom': function(responseData){
            vm.SetDefaultCategory()
          }
        },{
          'name': 'locations',
          'update_mutation': 'pcmLocations/UPDATE_Location',
          'add_mutation': 'pcmLocations/ADD_Location',
          'ready_mutation': 'pcmLocations/SET_Ready',
          'object': vm.Locations,
          'contexts': ['actual', 'template'],
          'custom': function(responseData){}
        },
        {
          'name': 'objects',
          'update_mutation': 'pcmObjects/UPDATE_Object',
          'add_mutation': 'pcmObjects/ADD_Object',
          'ready_mutation': 'pcmObjects/SET_Ready',
          'object': vm.Objects,
          'contexts': ['actual'],
          'custom': function(responseData){}
        },
        {
          'name': 'templates',
          'update_mutation': 'pcmTemplates/UPDATE_Template',
          'add_mutation': 'pcmTemplates/ADD_Template',
          'ready_mutation': 'pcmTemplates/SET_Ready',
          'object': vm.Templates,
          'contexts': ['actual', 'template'],
          'custom': function(responseData){
            responseData.forEach(function(template) {
              vm.GeneratePseudoData('template', template)
              vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:'template', ReadyState:true})
              vm.$store.commit('pcmTemplates/SET_Ready', {pcmContext:'template', ReadyState:true})
            })
          }
        },
        {
          'name': 'connections',
          'update_mutation': 'pcmConnections/UPDATE_Connection',
          'add_mutation': 'pcmConnections/ADD_Connection',
          'ready_mutation': 'pcmConnections/SET_Ready',
          'object': vm.Connections,
          'contexts': [false],
          'custom': function(responseData){}
        },
        {
          'name': 'organization',
          'update_mutation': 'pcmOrganization/UPDATE_Organization',
          'add_mutation': 'pcmOrganization/ADD_Organization',
          'ready_mutation': 'pcmOrganization/SET_Ready',
          'object': vm.Organization,
          'contexts': [false],
          'custom': function(responseData){}
        },
        {
          'name': 'cable-paths',
          'update_mutation': 'pcmCablePaths/UPDATE_CablePath',
          'add_mutation': 'pcmCablePaths/ADD_CablePath',
          'ready_mutation': 'pcmCablePaths/SET_Ready',
          'object': vm.CablePaths,
          'contexts': ['actual'],
          'custom': function(responseData){}
        },
        {
          'name': 'trunks',
          'update_mutation': 'pcmTrunks/UPDATE_Trunk',
          'add_mutation': 'pcmTrunks/ADD_Trunk',
          'ready_mutation': 'pcmTrunks/SET_Ready',
          'object': vm.Trunks,
          'contexts': [false],
          'custom': function(responseData){}
        },
      ]

      vm.$http.get('/api/data-sync/'+timestamp).then(response => {

        const responseData = response.data

        // Loop over tables
        tables.forEach(function(table){

          // Store table variables
          const tableName = table.name
          const updateMutation = table.update_mutation
          const addMutation = table.add_mutation
          const readyMutation = table.ready_mutation
          const tableObject = table.object
          const tableContexts = table.contexts

          // Evaluate if table is included in returnData
          if(tableName in responseData.tables) {
            // Loop over table contexts
            tableContexts.forEach(function(tableContext){
              // Loop over table items
              responseData.tables[tableName].forEach(function(tableEntry){
              
                // Look for table item in local store
                const tableObjectContext = (tableContext) ? tableObject[tableContext] : tableObject
                const entryIndex = tableObjectContext.findIndex((entry) => entry.id == tableEntry.id);

                if(entryIndex == -1) {
                  // Add if it does not exist
                  vm.$store.commit(addMutation, {pcmContext:tableContext, data:tableEntry})

                } else {
                  // Update if it does exist
                  vm.$store.commit(updateMutation, {pcmContext:tableContext, data:tableEntry})
                }
                
              })
              // Set store ReadyState to true
              vm.$store.commit(readyMutation, {pcmContext:tableContext, ReadyState:true})
            })

            // Call custom function
            table.custom(responseData.tables[tableName])
          }
        })

        // Update local DataSyncTimestamp
        const timestamp = responseData.timestamp
        vm.$store.commit('pcmState/UPDATE_DataSyncTimestamp', {timestamp})

      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETConnectors() {

      const vm = this

      vm.$http.get('/api/port-connectors').then(response => {
        vm.$store.commit('pcmProps/SET_Connectors', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop: 'connectors', ReadyState: true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETMedium() {

      const vm = this

      vm.$http.get('/api/medium').then(response => {
        vm.$store.commit('pcmProps/SET_Medium', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop: 'medium', ReadyState: true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETOrientations() {

      const vm = this

      vm.$http.get('/api/port-orientation').then(response => {
        vm.$store.commit('pcmProps/SET_Orientations', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop: 'orientations', ReadyState: true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETFloorplanTemplates: function() {

      const vm = this

      vm.$http.get('/api/floorplan-templates').then(function(response){
        vm.$store.commit('pcmFloorplanTemplates/SET_FloorplanTemplates', {data: response.data})
        vm.$store.commit('pcmFloorplanTemplates/SET_Ready', {ReadyState:true})
      })
    },
    SetDefaultCategory() {

      const vm = this
      const Context = 'workspace'
      const Categories = vm.Categories
      const WorkspaceStandardID = vm.$store.state.pcmProps.WorkspaceStandardID
      const WorkspaceInsertID = vm.$store.state.pcmProps.WorkspaceInsertID
      const StandardTemplateIndex = vm.GetTemplateIndex(WorkspaceStandardID, Context)
      const InsertTemplateIndex = vm.GetTemplateIndex(WorkspaceInsertID, Context)

      const DefaultCategoryIndex = Categories[Context].findIndex((category) => category.default)
      let DefaultCategoryID

      if(DefaultCategoryIndex !== -1) {
        DefaultCategoryID = Categories[Context][DefaultCategoryIndex].id
      } else {
        DefaultCategoryID = Categories[Context][0].id
      }

      const StandardTemplateUpdated = JSON.parse(JSON.stringify(vm.Templates[Context][StandardTemplateIndex]), function(key, value){
        if(key == 'category_id') {
          return DefaultCategoryID
        } else {
          return value
        }
      })

      const InsertTemplateUpdated = JSON.parse(JSON.stringify(vm.Templates[Context][InsertTemplateIndex]), function(key, value){
        if(key == 'category_id') {
          return DefaultCategoryID
        } else {
          return value
        }
      })

      vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:StandardTemplateUpdated})
      vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:InsertTemplateUpdated})

    },
    GenerateWorkspaceLocations() {

      const vm = this

      // Create workspace locations
      const WorkspaceIDs = {
        standard: vm.$store.state.pcmProps.WorkspaceStandardID,
        insert: vm.$store.state.pcmProps.WorkspaceInsertID
      }
      let WorkspaceCabinet
      Object.entries(WorkspaceIDs).forEach(function([Type, ID]){
        WorkspaceCabinet = JSON.parse(JSON.stringify(vm.$store.state.pcmProps.GenericCabinet), function (Key, Value) {
          if(Key == 'id') {
            return ID
          } else {
            return Value
          }
        })
        vm.$store.commit('pcmLocations/ADD_Location', {pcmContext:'workspace', data:WorkspaceCabinet})
      })
      vm.$store.commit('pcmLocations/SET_Ready', {pcmContext:'workspace', ReadyState:true})
    },
    GenerateWorkspaceObjects() {

      const vm = this

      // Create workspace objects
      const WorkspaceIDs = {
        standard: vm.$store.state.pcmProps.WorkspaceStandardID,
        insert: vm.$store.state.pcmProps.WorkspaceInsertID
      }
      let WorkspaceObject
      Object.entries(WorkspaceIDs).forEach(function([Type, ID]){
        WorkspaceObject = JSON.parse(JSON.stringify(vm.$store.state.pcmProps.GenericObject), function (Key, Value) {
          if(Key == 'id') {
            return ID
          } else if(Key == 'name') {
            return "New_Object"
          } else if(Key == 'template_id') {
            return ID
          } else if(Key == 'location_id') {
            return ID
          } else if(Key == 'cabinet_ru') {
            return (Type == 'standard') ? 1 : Value
          } else if(Key == 'cabinet_front') {
            return (Type == 'standard') ? 'front' : Value
          } else {
            return Value
          }
        })
        vm.$store.commit('pcmObjects/ADD_Object', {pcmContext:'workspace', data:WorkspaceObject})
      })
      vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:'workspace', ReadyState:true})
    },
    GenerateWorkspaceTemplates() {

      const vm = this

      // Create workspace templates
      const WorkspaceIDs = {
        standard: vm.$store.state.pcmProps.WorkspaceStandardID,
        insert: vm.$store.state.pcmProps.WorkspaceInsertID
      }
      let WorkspaceTemplate
      Object.entries(WorkspaceIDs).forEach(function([Type, ID]){
        WorkspaceTemplate = JSON.parse(JSON.stringify(vm.$store.state.pcmProps.GenericTemplate), function (Key, Value) {
          if(Key == 'id') {
            return ID
          } else if(Key == 'name') {
            return "New_Template"
          } else if(Key == 'type') {
            if(Value === null) {
              return Type
            } else {
              return Value
            }
          } else if(Key == 'ru_size') {
            return (Type == 'standard') ? 1 : Value
          } else if(Key == 'function') {
            return 'endpoint'
          } else if(Key == 'mount_config') {
            return (Type == 'standard') ? '2-post' : Value
          } else if(Key == 'insert_constraints') {
            return (Type == 'standard') ? Value : [{part_layout:{height:2,width:24},enc_layout:{cols:1,rows:1}}]
          } else {
            return Value
          }
        })
        vm.$store.commit('pcmTemplates/ADD_Template', {pcmContext:'workspace', data:WorkspaceTemplate})
      })
      vm.$store.commit('pcmTemplates/SET_Ready', {pcmContext:'workspace', ReadyState:true})
    },
  },
  beforeCreate() {
    // Set colors in theme
    const colors = ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark']

    // eslint-disable-next-line no-plusplus
    for (let i = 0, len = colors.length; i < len; i++) {
      $themeColors[colors[i]] = useCssVar(`--${colors[i]}`, document.documentElement).value.trim()
    }

    // Set Theme Breakpoints
    const breakpoints = ['xs', 'sm', 'md', 'lg', 'xl']

    // eslint-disable-next-line no-plusplus
    for (let i = 0, len = breakpoints.length; i < len; i++) {
      $themeBreakpoints[breakpoints[i]] = Number(useCssVar(`--breakpoint-${breakpoints[i]}`, document.documentElement).value.slice(0, -2))
    }

    // Set RTL
    const { isRTL } = $themeConfig.layout
    document.documentElement.setAttribute('dir', isRTL ? 'rtl' : 'ltr')
  },
  watch: {
    $route (to, from){
      const vm = this
      const UserData = localStorage.getItem('userData')
      if(UserData !== null && vm.StateDataSyncIntervalID === null) {

        // Get static data
        vm.GETConnectors()
        vm.GETMedium()
        vm.GETOrientations()
        vm.GETFloorplanTemplates()

        //vm.SetDefaultCategory()
        vm.GenerateWorkspaceLocations()
        vm.GenerateWorkspaceObjects()
        vm.GenerateWorkspaceTemplates()

        // Get dynamic data
        vm.DataSync()

        // Get dynamic data at an interval
        const IntervalID = window.setInterval(() => {
          vm.DataSync()
        }, 5000)

        // Set Data Sync interval ID
        vm.$store.commit('pcmState/UPDATE_DataSyncIntervalID', {IntervalID})
      }
    }
  },
  mounted() {

    console.log('mounted')
    /*
    const vm = this

    const UserData = localStorage.getItem('userData')
    if(UserData !== null) {

    // Get static data
    vm.GETConnectors()
    vm.GETMedium()
    vm.GETOrientations()

    // Get dynamic data
    vm.DataSync()

    // Get dynamic data at an interval
    window.setInterval(() => {
      
      
        vm.DataSync()
      
    }, 5000)
  }*/
  },
  setup() {
    const { skin, skinClasses } = useAppConfig()

    // If skin is dark when initialized => Add class to body
    if (skin.value === 'dark') document.body.classList.add('dark-layout')

    // Provide toast for Composition API usage
    // This for those apps/components which uses composition API
    // Demos will still use Options API for ease
    provideToast({
      hideProgressBar: true,
      closeOnClick: false,
      closeButton: false,
      icon: false,
      timeout: 3000,
      transition: 'Vue-Toastification__fade',
    })

    // Set Window Width in store
    store.commit('app/UPDATE_WINDOW_WIDTH', window.innerWidth)
    const { width: windowWidth } = useWindowSize()
    watch(windowWidth, val => {
      store.commit('app/UPDATE_WINDOW_WIDTH', val)
    })

    return {
      skinClasses,
    }
  },
}
</script>

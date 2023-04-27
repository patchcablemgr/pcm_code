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
import { watch } from 'vue'
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
    CablePaths() {
      return this.$store.state.pcmCablePaths.CablePaths
    },
    Cables() {
      return this.$store.state.pcmCables.Cables
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Organization() {
      return this.$store.state.pcmOrganization.Organization
    },
    Ports() {
      return this.$store.state.pcmPorts.Ports
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Trunks() {
      return this.$store.state.pcmTrunks.Trunks
    },
  },
  methods: {
    DataSync(Initial) {

      const vm = this
      const timestamp = vm.StateDataSyncTimestamp
      const tables = [
        {
          'name': 'categories',
          'update_mutation': 'pcmCategories/UPDATE_Category',
          'add_mutation': 'pcmCategories/ADD_Category',
          'ready_mutation': 'pcmCategories/SET_Ready',
          'delete_mutation': 'pcmCategories/DELETE_Category',
          'object': vm.Categories,
          'contexts': ['actual', 'workspace', 'template'],
          'onAdd': false,
          'onComplete': function(responseData){
            if(Initial) {
              vm.SetDefaultCategory()
            }
          }
        },{
          'name': 'locations',
          'update_mutation': 'pcmLocations/UPDATE_Location',
          'add_mutation': 'pcmLocations/ADD_Location',
          'ready_mutation': 'pcmLocations/SET_Ready',
          'delete_mutation': 'pcmLocations/REMOVE_Location',
          'object': vm.Locations,
          'contexts': ['actual', 'template'],
          'onAdd': false,
          'onComplete': false
        },
        {
          'name': 'objects',
          'update_mutation': 'pcmObjects/UPDATE_Object',
          'add_mutation': 'pcmObjects/ADD_Object',
          'ready_mutation': 'pcmObjects/SET_Ready',
          'delete_mutation': 'pcmObjects/REMOVE_Object',
          'object': vm.Objects,
          'contexts': ['actual'],
          'onAdd': false,
          'onComplete': false
        },
        {
          'name': 'templates',
          'update_mutation': 'pcmTemplates/UPDATE_Template',
          'add_mutation': 'pcmTemplates/ADD_Template',
          'ready_mutation': 'pcmTemplates/SET_Ready',
          'delete_mutation': 'pcmTemplates/REMOVE_Template',
          'object': vm.Templates,
          'contexts': ['actual', 'template'],
          'onAdd': function(tableContext, entry){
            if(tableContext == 'template') {
              vm.GeneratePseudoData(tableContext, entry)
            }
          },
          'onComplete': function(responseData){
            vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:'template', ReadyState:true})
          }
        },
        {
          'name': 'connections',
          'update_mutation': 'pcmConnections/UPDATE_Connection',
          'add_mutation': 'pcmConnections/ADD_Connection',
          'ready_mutation': 'pcmConnections/SET_Ready',
          'delete_mutation': 'pcmConnections/REMOVE_Connection',
          'object': vm.Connections,
          'contexts': [false],
          'onAdd': false,
          'onComplete': false
        },
        {
          'name': 'cable-paths',
          'update_mutation': 'pcmCablePaths/UPDATE_CablePath',
          'add_mutation': 'pcmCablePaths/ADD_CablePath',
          'ready_mutation': 'pcmCablePaths/SET_Ready',
          'delete_mutation': 'pcmCablePaths/REMOVE_CablePath',
          'object': vm.CablePaths,
          'contexts': ['actual'],
          'onAdd': false,
          'onComplete': false
        },
        {
          'name': 'trunks',
          'update_mutation': 'pcmTrunks/UPDATE_Trunk',
          'add_mutation': 'pcmTrunks/ADD_Trunk',
          'ready_mutation': 'pcmTrunks/SET_Ready',
          'delete_mutation': 'pcmTrunks/REMOVE_Trunk',
          'object': vm.Trunks,
          'contexts': [false],
          'onAdd': false,
          'onComplete': false
        },
        {
          'name': 'cables',
          'update_mutation': 'pcmCables/UPDATE_Cable',
          'add_mutation': 'pcmCables/ADD_Cable',
          'ready_mutation': 'pcmCables/SET_Ready',
          'delete_mutation': 'pcmCables/REMOVE_Cable',
          'object': vm.Cables,
          'contexts': [false],
          'onAdd': false,
          'onComplete': false
        },
        {
          'name': 'ports',
          'update_mutation': 'pcmPorts/UPDATE_Port',
          'add_mutation': 'pcmPorts/ADD_Port',
          'ready_mutation': 'pcmPorts/SET_Ready',
          'delete_mutation': 'pcmPorts/REMOVE_Port',
          'object': vm.Ports,
          'contexts': [false],
          'onAdd': false,
          'onComplete': false
        }
      ]

      // Loop over tables
      let CurrentIDs = {}
      tables.forEach(function(table){
          
          CurrentIDs[table.name] = []

          const tableContexts = table.contexts
          if(tableContexts[0]) {
            table.object.actual.forEach(function(entry){
              CurrentIDs[table.name].push(entry.id)
            })
          } else {
            table.object.forEach(function(entry){
              CurrentIDs[table.name].push(entry.id)
            })
          }
      })

      const data = {
        'timestamp':timestamp,
        'entries':CurrentIDs
      }

      vm.$http.post('/api/data-sync', data).then(response => {

        const responseData = response.data

        // Loop over tables
        tables.forEach(function(table){

          // Store table variables
          const tableName = table.name
          const updateMutation = table.update_mutation
          const addMutation = table.add_mutation
          const readyMutation = table.ready_mutation
          const deleteMutation = table.delete_mutation
          const tableObject = table.object
          const tableContexts = table.contexts

          // Evaluate if table is included in returnData
          if(tableName in responseData.tables) {
            // Loop over table contexts
            tableContexts.forEach(function(tableContext){

              // Loop over present items
              responseData.tables[tableName].present.forEach(function(tableEntry){
              
                // Look for table item in local store
                const tableObjectContext = (tableContext) ? tableObject[tableContext] : tableObject
                const entryIndex = tableObjectContext.findIndex((entry) => entry.id == tableEntry.id);

                if(entryIndex == -1) {
                  // Add if it does not exist
                  vm.$store.commit(addMutation, {pcmContext:tableContext, data:tableEntry})

                  // Call custom function
                  if(table.onAdd) {
                    table.onAdd(tableContext, tableEntry)
                  }

                } else {
                  // Update if it does exist
                  vm.$store.commit(updateMutation, {pcmContext:tableContext, data:tableEntry})
                }
                
              })

              // Loop over absent items
              responseData.tables[tableName].absent.forEach(function(tableEntry){
                // Delete old records no longer present in the database
                vm.$store.commit(deleteMutation, {pcmContext:tableContext, data:{'id': tableEntry}})
              })

              // Set store ReadyState to true
              vm.$store.commit(readyMutation, {pcmContext:tableContext, ReadyState:true})
            })

            // Call custom function
            if(table.onComplete) {
              table.onComplete(responseData.tables[tableName].present)
            }
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
    GETCableConnectors() {

      const vm = this

      vm.$http.get('/api/cable-connectors').then(response => {
        vm.$store.commit('pcmProps/SET_CableConnectors', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop: 'cableConnectors', ReadyState: true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETMedia() {

      const vm = this

      vm.$http.get('/api/media').then(response => {
        vm.$store.commit('pcmProps/SET_Media', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop: 'media', ReadyState: true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETMediaType() {

      const vm = this

      vm.$http.get('/api/media-type').then(response => {
        vm.$store.commit('pcmProps/SET_MediaType', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop: 'mediaType', ReadyState: true})
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
    GETOrganization() {

      const vm = this

      vm.$http.get('/api/organization').then(function(response){

        vm.$store.commit('pcmOrganization/SET_Organization', {data: response.data})
        vm.$store.commit('pcmOrganization/SET_Ready', {ReadyState:true})
      })
    },
    GETUsers() {

      const vm = this

      // GET users
      vm.$http.get('/api/users').then(response => {
        vm.$store.commit('pcmUsers/SET_Users', {data:response.data})
        vm.$store.commit('pcmUsers/SET_Ready', {ReadyState:true})
      }).catch(error => {
        vm.DisplayError(error)
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
        vm.GETCableConnectors()
        vm.GETMedia()
        vm.GETMediaType()
        vm.GETOrientations()
        vm.GETOrganization()
        vm.GETUsers()

        vm.GenerateWorkspaceLocations()
        vm.GenerateWorkspaceObjects()
        vm.GenerateWorkspaceTemplates()

        // Get dynamic data
        vm.DataSync(true)

        // Get dynamic data at an interval
        const IntervalID = window.setInterval(() => {
          vm.DataSync(false)
        }, 5000)

        // Set Data Sync interval ID
        vm.$store.commit('pcmState/UPDATE_DataSyncIntervalID', {IntervalID})
      }
    }
  },
  mounted() {},
  setup() {
    const { skin, skinClasses } = useAppConfig()

    // If skin is dark when initialized => Add class to body
    if (skin.value === 'dark') document.body.classList.add('dark-layout')

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

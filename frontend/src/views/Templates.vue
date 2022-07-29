<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid="xs">
      <b-row
        cols="1"
        cols-md="2"
        cols-xl="3"
      >
        <b-col>
          <b-card
            title="Properties"
          >
            <b-card-body>
              <templates-form
                Context="workspace"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :PartitionTypeDisabled="PartitionTypeDisabled"
                SetLocationID
                @SetLocationID="SetLocationID($event)"
                @SetPartitionAddressSelected="SetPartitionAddressSelected($event)"
                @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
              />
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>

          <b-card
            title="Preview"
            :class="{ pcm_sticky: IsSticky, pcm_scroll: IsSticky }"
          >
            <b-card-body
              v-if=" PreviewDisplay == 'cabinet' "
            >
              <div class="demo-inline-spacing">
                
                <b-form-radio
                  v-model="TemplateFaceSelected.workspace"
                  value="front"
                  :disabled="TemplateFaceToggleIsDisabled"
                >Front
                </b-form-radio>

                <b-form-radio
                  v-model="TemplateFaceSelected.workspace"
                  value="rear"
                  :disabled="TemplateFaceToggleIsDisabled"
                >Rear
                </b-form-radio>

                <b-form-checkbox
                  v-model="IsSticky"
                  switch
                >Sticky
                </b-form-checkbox>
              </div>
              <component-cabinet
                :LocationID="LocationID"
                Context="workspace"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :PartitionAddressHovered="PartitionAddressHovered"
              />
            </b-card-body>
            <b-card-body
              v-if=" PreviewDisplay == 'insertInstructions' "
            >
              Select enclosure
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>

          <component-template-object-details
            CardTitle="Template Details"
						Context="template"
						:TemplateFaceSelected="TemplateFaceSelected"
						:PartitionAddressSelected="PartitionAddressSelected"
            :DetailsAreEditable="true"
            @SetPartitionAddressSelected="SetPartitionAddressSelected($event)"
            @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
					/>

          <component-templates
            Context="template"
            :TemplateFaceSelected="TemplateFaceSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
          />

        </b-col>
      </b-row>
    </b-container>

    <!-- Toast -->
    <toast-general/>

  </div>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BCardBody, BCardText, BFormCheckbox, BFormRadio, } from 'bootstrap-vue'
import TemplatesForm from './templates/TemplatesForm.vue'
import ToastGeneral from './templates/ToastGeneral.vue'
import ComponentCabinet from './templates/ComponentCabinet.vue'
import ComponentTemplateObjectDetails from './templates/ComponentTemplateObjectDetails.vue'
import ComponentTemplates from './templates/ComponentTemplates.vue'
import ModalTemplatesEdit from './templates/ModalTemplatesEdit.vue'
import { PCM } from '@/mixins/PCM.js'

const IsSticky = false
const CategoriesWatcherActive = true
const LocationID = 1
const StandardTemplateID = 1
const InsertTemplateID = 2
const TemplateFaceSelected = {
  'workspace': 'front',
  'template': 'front',
}
const PartitionAddressSelected = {
  'workspace': {
    'object_id': StandardTemplateID,
    'object_face': 'front',
    'template_id': StandardTemplateID,
    'front': [0],
    'rear': [0],
    'port_id': {
      'front': null,
      'rear': null,
    }
  },
  'template': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': [0],
    'rear': [0],
    'port_id': {
      'front': null,
      'rear': null,
    }
  },
  'object': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': [0],
    'rear': [0],
    'port_id': {
      'front': null,
      'rear': null,
    }
  }
}
const PartitionAddressHovered = {
  'workspace': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': false,
    'rear': false,
    'port_id': {
      'front': null,
      'rear': null,
    }
  },
  'template': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': false,
    'rear': false,
    'port_id': {
      'front': null,
      'rear': null,
    }
  }
}

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardText,
    BFormCheckbox,
    BFormRadio,

    TemplatesForm,
    ToastGeneral,
    ComponentCabinet,
    ComponentTemplateObjectDetails,
    ComponentTemplates,
    ModalTemplatesEdit,
  },
  data() {
    return {
      IsSticky,
      CategoriesWatcherActive,
      LocationID,
      TemplateFaceSelected,
      PartitionAddressSelected,
      PartitionAddressHovered,
      StandardTemplateID,
      InsertTemplateID,
    }
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.CategoriesReady,
        vm.TemplatesReady.template,
        vm.TemplatesReady.workspace,
        vm.ObjectsReady.template,
        vm.ObjectsReady.workspace,
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    CategoriesReady: function() {
      return this.$store.state.pcmCategories.CategoriesReady
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
    PreviewDisplay: function(){

      const vm = this
      const Template = vm.GetTemplateSelected('workspace')
      let PreviewDisplay = 'cabinet'

      if(Template.type == 'insert') {

        if(Template.hasOwnProperty('clone')) {

          PreviewDisplay = 'cabinet'
        } else {

          const Partition = vm.GetPartitionSelected('template')
          if(Partition) {
            if(Partition.type == 'enclosure') {
              PreviewDisplay = 'cabinet'
            } else {
              PreviewDisplay = 'insertInstructions'
            }
          } else {
            PreviewDisplay = 'insertInstructions'
          }
        }
      }

      return PreviewDisplay
    },
    TemplateFaceToggleIsDisabled: function() {

      const vm = this
      const Template = vm.GetTemplateSelected('workspace')
      const MountConfig = Template.mount_config
      const TemplateType = Template.type

      return MountConfig === '2-post'  || TemplateType === 'insert'
    },
    PartitionTypeDisabled: function() {
      // Store variables
      const vm = this
      const TemplateFaceSelected = vm.TemplateFaceSelected.workspace
      const PartitionAddressSelected = vm.PartitionAddressSelected.workspace[TemplateFaceSelected]

      return !PartitionAddressSelected.length
    },
  },
  methods: {
    SetLocationID: function(newValue) {

      const vm = this
      vm.LocationID = newValue
    },
    SetPartitionAddressSelected: function({Context, object_id, front, rear}) {

      const vm = this
      vm.PartitionAddressSelected[Context].object_id = object_id
      vm.PartitionAddressSelected[Context].front = front
      vm.PartitionAddressSelected[Context].rear = rear
    },
    SetTemplateFaceSelected: function({Context, Face}) {

      const vm = this
      vm.TemplateFaceSelected[Context] = Face
    },
    GETTemplates: function () {

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

      // Create actual/template templates
      vm.$http.get('/api/templates').then(response => {

        vm.$store.commit('pcmTemplates/SET_Templates', {pcmContext: 'template', data: response.data})
        response.data.forEach(function(template) {
          vm.GeneratePseudoData('template', template)
        })
        vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:'template', ReadyState:true})
        vm.$store.commit('pcmTemplates/SET_Ready', {pcmContext:'template', ReadyState:true})
      }).catch(error => { vm.DisplayError(error) })
    },
    SetDefaultCategory: function() {

      const vm = this
      const Context = 'workspace'
      const Categories = vm.Categories
      const WorkspaceStandardID = vm.$store.state.pcmProps.WorkspaceStandardID
      const WorkspaceInsertID = vm.$store.state.pcmProps.WorkspaceInsertID
      const StandardTemplateIndex = vm.GetTemplateIndex(WorkspaceStandardID, Context)
      const InsertTemplateIndex = vm.GetTemplateIndex(WorkspaceInsertID, Context)

      const DefaultCategoryIndex = Categories.findIndex((category) => category.default)
      let DefaultCategoryID

      if(DefaultCategoryIndex !== -1) {
        DefaultCategoryID = Categories[DefaultCategoryIndex].id
      } else {
        DefaultCategoryID = Categories[0].id
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
    GETLocations() {

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

      // Create template locations
      vm.$http.get('/api/locations').then(response => {
        vm.$store.commit('pcmLocations/SET_Locations', {pcmContext:'template', data:response.data})
        vm.$store.commit('pcmLocations/SET_Ready', {pcmContext:'template', ReadyState:true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETCategories() {

      const vm = this
      vm.$http.get('/api/categories')
      .then(response => {
        vm.$store.commit('pcmCategories/SET_Categories', response.data)
        vm.SetDefaultCategory()
        vm.$store.commit('pcmCategories/SET_Ready', true)
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETObjects() {

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

      vm.$http.get('/api/objects').then(response => {
        vm.$store.commit('pcmObjects/SET_Objects', response.data)
        vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:'actual', ReadyState:true})
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
    }
  },
  watch: {
  },
  mounted() {

    const vm = this

    vm.$store.commit('pcmCategories/SET_Ready', false)
    
    vm.GETObjects()
    vm.GETLocations()
    vm.GETCategories()
    vm.GETTemplates()
    vm.GETConnectors()
    vm.GETMedium()
    vm.GETOrientations()
  },
}
</script>

<style>

</style>

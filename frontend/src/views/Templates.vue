<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid="xs">
      <b-row>
        <b-col>
          <b-card
            title="Properties"
          >
            <b-card-body>
              <templates-form
                :SelectedCategoryID="SelectedCategoryID"
                :PortConnectorData="PortConnectorData"
                :PortOrientationData="PortOrientationData"
                :MediaData="MediaData"
                Context="workspace"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :SelectedPortFormatIndex="SelectedPortFormatIndex"
                :AddSiblingPartitionDisabled="AddSiblingPartitionDisabled"
                :RemovePartitionDisabled="RemovePartitionDisabled"
                :PartitionTypeDisabled="PartitionTypeDisabled"
                :SelectedPartition="SelectedPartition"
                :SelectedPortFormat="SelectedPortFormat"
                SetLocationID
                @SetLocationID="SetLocationID($event)"
                @SetPartitionAddressSelected="SetPartitionAddressSelected($event)"
                @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"

                @TemplatePartitionSizeUpdated="TemplatePartitionSizeUpdated($event)"
              />
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>

          <b-card
            title="Preview"
          >
            <b-card-body
              v-if=" PreviewDisplay == 'cabinet' "
            >
              <b-form-radio
                v-model="TemplateFaceSelected.workspace"
                plain
                value="front"
                :disabled="TemplateFaceToggleIsDisabled"
              >Front
              </b-form-radio>
              <b-form-radio
                v-model="TemplateFaceSelected.workspace"
                plain
                value="rear"
                :disabled="TemplateFaceToggleIsDisabled"
              >
                Rear
              </b-form-radio>
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

          <component-template-Object-details
            CardTitle="Template Details"
						Context="template"
						:TemplateFaceSelected="TemplateFaceSelected"
						:PartitionAddressSelected="PartitionAddressSelected"
            @TemplateObjectCloneClicked="TemplateObjectCloneClicked()"
            @SetPartitionAddressSelected="SetPartitionAddressSelected($event)"
					/>

          <component-templates
            Context="template"
            :TemplateFaceSelected="TemplateFaceSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @TemplateFaceChanged="TemplateFaceChanged($event)"
          />

        </b-col>
      </b-row>
    </b-container>

    <!-- Toast -->
    <toast-general/>

  </div>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BCardBody, BCardText, BFormRadio, } from 'bootstrap-vue'
import TemplatesForm from './templates/TemplatesForm.vue'
import ToastGeneral from './templates/ToastGeneral.vue'
import ComponentCabinet from './templates/ComponentCabinet.vue'
import ComponentTemplateObjectDetails from './templates/ComponentTemplateObjectDetails.vue'
import ComponentTemplates from './templates/ComponentTemplates.vue'
import ModalTemplatesEdit from './templates/ModalTemplatesEdit.vue'
import { PCM } from '@/mixins/PCM.js'

const TemplatesWatcherActive = true
const CategoriesWatcherActive = true
const LocationID = 1
const StandardTemplateID = 1
const InsertTemplateID = 2
const PortConnectorData = [
  {
    "value": 1,
    "name": "RJ45",
    "category_type_id": 1,
    "default": 1,
  },
]
const PortOrientationData = [
  {
    "value": 1,
    "name": "Top-Left to Right",
    "default": 1,
  },
]
const MediaData = [
  {
    "value": 1,
    "name": "placeholder",
    "category_id": 1,
    "type_id": 1,
    "display": 1,
    "default": 1,
  }
]
const SelectedCategoryID = null
const SelectedPortFormatIndex = {
  'workspace': 0,
  'template': 0,
}
const TemplateFaceSelected = {
  'workspace': 'front',
  'template': 'front',
}
const PartitionAddressSelected = {
  'workspace': {
    'object_id': StandardTemplateID,
    'template_id': StandardTemplateID,
    'front': [0],
    'rear': [0]
  },
  'template': {
    'object_id': null,
    'template_id': null,
    'front': [0],
    'rear': [0]
  },
  'object': {
    'object_id': null,
    'template_id': null,
    'front': [0],
    'rear': [0]
  }
}
const PartitionAddressHovered = {
  'workspace': {
    'object_id': null,
    'template_id': null,
    'front': false,
    'rear': false
  },
  'template': {
    'object_id': null,
    'template_id': null,
    'front': false,
    'rear': false
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
      TemplatesWatcherActive,
      CategoriesWatcherActive,
      LocationID,
      SelectedCategoryID,
      SelectedPortFormatIndex,
      MediaData,
      PortConnectorData,
      PortOrientationData,
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
    CategoriesReady: function() {
      return this.$store.state.pcmCategories.CategoriesReady
    },
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
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
      const Template = vm.GetPreviewData()
      const MountConfig = Template.mount_config
      const TemplateType = Template.type

      return MountConfig === '2-post'  || TemplateType === 'insert'
    },
    AddChildPartitionDisabled: function() {
      // Store variables
      const vm = this

      // Get Partition
      const Partition = vm.GetSelectedPreviewPartition()

      return (!vm.GetPartitionUnitsAvailable() || Partition.type != 'generic')
    },
    AddSiblingPartitionDisabled: function() {
      // Store variables
      const vm = this
      const TemplateFaceSelected = vm.TemplateFaceSelected.workspace
      const PartitionAddress = vm.PartitionAddressSelected.workspace[TemplateFaceSelected]
      const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)

      return !vm.GetPartitionUnitsAvailable(PartitionParentAddress)

    },
    RemovePartitionDisabled: function() {
      
      // Store variables
      const vm = this
      const PreviewData = vm.GetPreviewData()
      const TemplateFaceSelected = vm.TemplateFaceSelected.workspace
      const PartitionAddressSelected = vm.PartitionAddressSelected.workspace[TemplateFaceSelected]
      const Layer1Partitions = PreviewData.blueprint[TemplateFaceSelected]
      let RemovePartitionDisabled = false

      if(PartitionAddressSelected.length == 1) {
        if(Layer1Partitions.length == 1) {
          RemovePartitionDisabled = true
        }
      }

      return RemovePartitionDisabled
    },
    PartitionTypeDisabled: function() {
      // Store variables
      const vm = this
      const TemplateFaceSelected = vm.TemplateFaceSelected.workspace
      const PartitionAddressSelected = vm.PartitionAddressSelected.workspace[TemplateFaceSelected]

      return !PartitionAddressSelected.length
    },
    SelectedPartition: function(){
      // Store variables
      const vm = this

      // Get Partition
      const Partition = vm.GetSelectedPreviewPartition()

      return Partition
    },
    SelectedPortFormat: function() {
      // Store variables
      const vm = this

      // Get Partition
      const Partition = vm.GetSelectedPreviewPartition()

      let SelectedPortFormat = []

      if(Partition.type == 'connectable') {
        SelectedPortFormat = Partition.port_format
      }

      return SelectedPortFormat
    },
  },
  methods: {
    TemplateFaceChanged: function(EmitData) {

      // Store variables
      const vm = this
      const TemplateFace = EmitData.TemplateFace
      const Context = EmitData.Context
      vm.TemplateFaceSelected[Context] = TemplateFace

    },
    TemplateEdited: function(EmitData) {

      // Store data
      const vm = this
      const Context = 'template'
      const TemplateID = vm.PartitionAddressSelected[Context].template_id
      const url = '/api/templates/'+TemplateID
      const data = EmitData

      // PATCH category form data
      this.$http.patch(url, data).then(function(response){
        
        const Template = response.data
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
				
        // Append new template to template array
        vm.$set(vm.Templates[Context], TemplateIndex, Template)

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
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
    TemplatePartitionSizeUpdated: function(newValue) {

      // Get Partition
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()
      
      SelectedPartition.units = newValue
    },
    TemplateObjectCloneClicked: function() {

      const vm = this
      const TemplateContext = 'template'
      const PreviewContext = 'workspace'

      // Get cloned template
      const TemplateID = vm.PartitionAddressSelected[TemplateContext].template_id
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, TemplateContext)
      const Template = vm.Templates[TemplateContext][TemplateIndex]

      // Set active preview template
      const PreviewTemplateID = (Template.type == 'standard') ? vm.StandardTemplateID : vm.InsertTemplateID

      vm.TemplateFaceSelected[PreviewContext] = 'front'

      // Create a copy of cloned template
      const TemplateClone = JSON.parse(JSON.stringify(Template), function(TemplateKey, TemplateValue){
        if (TemplateKey == 'id') {
          return PreviewTemplateID
        } else {
          return TemplateValue
        }
      })
      TemplateClone.clone = true

      // Copy cloned template to active preview template
      const PreviewTemplateIndex = vm.GetTemplateIndex(PreviewTemplateID, PreviewContext)
      vm.$set(vm.Templates[PreviewContext], PreviewTemplateIndex, TemplateClone)

      if(Template.type == 'insert') {

        // Remove preview pseudo objects/templates
        vm.RemovePreviewPseudoData()

        // Create pseudo template clone parent
        const PseudoTemplateID = "pseudo-" + (vm.Templates[PreviewContext].length)
        const InsertConstraints = Template.insert_constraints
        const TemplateCloneParent = JSON.parse(JSON.stringify(vm.GenericTemplate), function (GenericTemplateKey, GenericTemplateValue) {
          if (GenericTemplateKey == 'id') {
            return PseudoTemplateID
          } else if (GenericTemplateKey == 'name') {
            return Template.name
          } else if (GenericTemplateKey == 'category_id') {
            return Template.category_id
          } else if (GenericTemplateKey == 'type') {
            // Set pseudo template type, but avoid setting partition type
            if (GenericTemplateValue === null) {
              return 'standard'
            } else {
              return GenericTemplateValue
            }
          } else if (GenericTemplateKey == 'ru_size') {
            // Set pseudo template RU size if this is the insert constraint origin ('standard' template type)
            return Math.ceil(InsertConstraints.part_layout.height / 2)
          } else if (GenericTemplateKey == 'blueprint') {
            // Set pseudo template partition attributes
            GenericTemplateValue.front[0].units = InsertConstraints.part_layout.width
            GenericTemplateValue.front[0].children[0].units = InsertConstraints.part_layout.height
            GenericTemplateValue.front[0].children[0].enc_layout.cols = InsertConstraints.enc_layout.cols
            GenericTemplateValue.front[0].children[0].enc_layout.rows = InsertConstraints.enc_layout.rows
            return GenericTemplateValue

          } else {
              return GenericTemplateValue
          }
        })
        vm.Templates[PreviewContext].push(TemplateCloneParent)

        // Generate pseudo object and constraining templates/objects if necessary
        const PseudoObjectID = vm.GeneratePseudoData(TemplateCloneParent, 'workspace')
        vm.Objects.workspace[PreviewTemplateIndex].parent_id = PseudoObjectID
      }
    },
    GetGlobalPartitionMax: function(Template, PartitionAddress) {

      const vm = this
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)

      // Get working max
      let WorkingMax
      if (PartitionDirection == 'col') {

        if (Template.insert_constraints !== null) {

          // Partition is an insert with constraints
          WorkingMax = Template.insert_constraints.part_layout.width
        } else {

          // Partition is standard
          WorkingMax = 24
        }
      } else {

        if (Template.insert_constraints !== null) {

          // Partition is an insert with constraints
          WorkingMax = Template.insert_constraints.part_layout.height
        } else {

          // Partition is standard
          WorkingMax = Template.ru_size * 2
        }
      }

      return WorkingMax

    },
    GetPartitionUnitsAvailable: function(PartAddr = false) {

      // Store variables
      const vm = this
      const PreviewData = vm.GetPreviewData()

      // Get Partition
      const Face = vm.TemplateFaceSelected.workspace
      const PartitionAddress = (PartAddr !== false) ? PartAddr : vm.PartitionAddressSelected.workspace[Face]
      const Blueprint = PreviewData.blueprint[Face]
      const Partition = vm.GetPartition(Blueprint, PartitionAddress)

      let UnitsAvailable = vm.GetGlobalPartitionMax(PreviewData, PartitionAddress)
      let PartitionChildren

      if(PartitionAddress.length > 1) {
        const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
        const PartitionParent = vm.GetPartition(Blueprint, PartitionParentAddress)
        UnitsAvailable = PartitionParent.units
      }

      if(PartitionAddress.length > 0) {
        PartitionChildren = Partition.children
      } else {
        PartitionChildren = Partition
      }

      PartitionChildren.forEach(function(PartitionChild) {
        UnitsAvailable = UnitsAvailable - PartitionChild.units
      })

      return UnitsAvailable
    },
    GetSelectedPreviewPartition: function(){

      // Store variables
      const vm = this
      const PreviewData = vm.GetPreviewData()

      // Get Partition
      const Face = vm.TemplateFaceSelected.workspace
      const PartAddr = vm.PartitionAddressSelected.workspace[Face]
      const Blueprint = PreviewData.blueprint[Face]

      let SelectedPartition = vm.GetPartition(Blueprint, PartAddr)
      
      return SelectedPartition
    },
    GetPreviewData: function() {

      // Initial variables
      const vm = this
      const Context = 'workspace'

      // Get template index
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
      const TemplateID = vm.GetTemplateID(ObjectID, Context)
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)

      // Get template
      const Template = vm.Templates[Context][TemplateIndex]

      // Return template
      return Template
    },
    RemovePreviewPseudoData: function() {

      const vm = this

      const PseudoObjects = vm.Objects.workspace.filter(function(Object){
        return Object.id.toString().includes('pseudo')
      })
      PseudoObjects.forEach(function(object){
        vm.$store.commit('pcmObjects/REMOVE_Object', {pcmContext:'workspace', data:object})
      })

      const PseudoTemplates = vm.Templates.workspace.filter(function(Template){
        return Template.id.toString().includes('pseudo')
      })
      PseudoTemplates.forEach(function(template){
        vm.$store.commit('pcmTemplates/REMOVE_Template', {pcmContext:'workspace', data:template})
      })

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
            return (Type == 'standard') ? Value : {part_layout:{height:2,width:24},enc_layout:{cols:1,rows:1}}
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
      }).catch(error => {
        vm.DisplayError(error)
      })
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
    Categories: {
      handler() {

        const vm = this

        if(vm.CategoriesWatcherActive) {

          vm.CategoriesWatcherActive = false
          vm.SetDefaultCategory()
          
        }
      }
    },
  },
  mounted() {

    const vm = this
    
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

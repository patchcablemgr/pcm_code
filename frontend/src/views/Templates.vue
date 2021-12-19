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
                @TemplatePartitionPortFormatFieldSelected="TemplatePartitionPortFormatFieldSelected($event)"
                @TemplatePartitionPortFormatValueUpdated="TemplatePartitionPortFormatValueUpdated($event)"
                @TemplatePartitionPortFormatTypeUpdated="TemplatePartitionPortFormatTypeUpdated($event)"
                @TemplatePartitionPortFormatCountUpdated="TemplatePartitionPortFormatCountUpdated($event)"
                @TemplatePartitionPortFormatOrderUpdated="TemplatePartitionPortFormatOrderUpdated($event)"
                @TemplatePartitionPortFormatFieldMove="TemplatePartitionPortFormatFieldMove($event)"
                @TemplatePartitionPortFormatFieldCreate="TemplatePartitionPortFormatFieldCreate($event)"
                @TemplatePartitionPortFormatFieldDelete="TemplatePartitionPortFormatFieldDelete($event)"
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
            :PortConnectorData="PortConnectorData"
            :MediaData="MediaData"
						Context="template"
						:TemplateFaceSelected="TemplateFaceSelected"
						:PartitionAddressSelected="PartitionAddressSelected"
            :PortOrientationData="PortOrientationData"
            @TemplateObjectCloneClicked="TemplateObjectCloneClicked()"
						@TemplateObjectDeleteClicked="TemplateObjectDeleteClicked()"
            @TemplateEdited="TemplateEdited($event)"
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
import { PCM } from '../mixins/PCM.js'

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
      const Context = 'template'
			const PreviewData = vm.GetPreviewData()
      const TemplateType = PreviewData.type
      let PreviewDisplay = 'cabinet'

      if(TemplateType == 'insert') {
        const PartitionAddressSelected = vm.PartitionAddressSelected[Context]
        const TemplateID = PartitionAddressSelected.template_id

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
          const Blueprint = vm.Templates[Context][TemplateIndex].blueprint[TemplateFaceSelected]
          const PartitionAddress = PartitionAddressSelected[TemplateFaceSelected]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)
          const PartitionType = Partition.type
          const PreviewTemplate = vm.GetPreviewData()
          const PreviewTemplateClone = PreviewTemplate.hasOwnProperty('clone')

          if(PartitionType == 'enclosure') {
            PreviewDisplay = 'cabinet'
          } else if(PreviewTemplateClone) {
            PreviewDisplay = 'cabinet'
          } else {
            PreviewDisplay = 'insertInstructions'
          }
        } else {
          PreviewDisplay = 'insertInstructions'
        }
      } else {
        PreviewDisplay = 'cabinet'
      }

      return PreviewDisplay
    },
    PreviewPortID: function(){
      
      const vm = this

      // Get Partition
      const Partition = vm.GetSelectedPreviewPartition()

      let PortID = ''
      let PreviewPortIDArray = []

      if(Partition.type == 'connectable') {
        const PortFormat = JSON.parse(JSON.stringify(vm.SelectedPortFormat))
        let PortTotal = Partition.port_layout.cols * Partition.port_layout.rows
        let Truncated = false

        // Limit port preview to 5
        if(PortTotal > 5) {
          PortTotal = 5
          Truncated = true
        }

        // Generate port IDs
        for(let i=0; i<PortTotal; i++){
          PortID = vm.GeneratePortID(i, PortTotal, PortFormat)
          PreviewPortIDArray.push(PortID)
        }

        // Append ellipses if port preview is truncated
        if(Truncated) {
          PreviewPortIDArray.push('...')
        }
      }

      return PreviewPortIDArray.join(', ')

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
    TemplatePartitionPortFormatFieldSelected: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = EmitData.index
      vm.SelectedPortFormatIndex[Context] = PortFormatIndex
    },
    TemplatePartitionPortFormatValueUpdated: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = vm.SelectedPortFormatIndex[Context]
      const PortFormatValue = EmitData.value

      if(Context == 'template') {

        const PartitionAddressSelected = vm.PartitionAddressSelected[Context]
        const TemplateID = PartitionAddressSelected.template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const PartitionAddress = PartitionAddressSelected[TemplateFace]

        const UpdateData = {
          "port_format": {
            "template_id": TemplateID,
            "template_face": TemplateFace,
            "template_partition": PartitionAddress,
            "port_format_index": PortFormatIndex,
            "port_format_attr": 'value',
            "port_format_value": PortFormatValue,
          }
        }

        vm.TemplateEdited(UpdateData)

      } else {

        vm.SelectedPortFormat[PortFormatIndex].value = PortFormatValue
      }

    },
    TemplatePartitionPortFormatTypeUpdated: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = vm.SelectedPortFormatIndex[Context]
      const SelectedPortFormat = vm.SelectedPortFormat
      const TypeNew = EmitData.value
      let OrderNew = 0
      let ValueNew = ''

      if(Context == 'template') {

        const PartitionAddressSelected = vm.PartitionAddressSelected[Context]
        const TemplateID = PartitionAddressSelected.template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const PartitionAddress = PartitionAddressSelected[TemplateFace]

        const UpdateData = {
          "port_format": {
            "template_id": TemplateID,
            "template_face": TemplateFace,
            "template_partition": PartitionAddress,
            "port_format_index": PortFormatIndex,
            "port_format_attr": 'type',
            "port_format_value": TypeNew,
          }
        }

        vm.TemplateEdited(UpdateData)

      } else {

        // Determine field order
        if(TypeNew == 'series' || TypeNew == 'incremental') {

          OrderNew = 1
          let WorkingOrderArray = []

          // Gather all incrementable field orders
          SelectedPortFormat.forEach(function(PortFormatField, PortFormatFieldIndex){

            const PortFormatFieldType = PortFormatField.type
            
            if((PortFormatFieldType == 'series' || PortFormatFieldType == 'incremental') && PortFormatFieldIndex != PortFormatIndex) {
              const PortFormatFieldOrder = PortFormatField.order
              WorkingOrderArray.push(PortFormatFieldOrder)
            }
          })

          // Sort incrementable field orders
          WorkingOrderArray.sort(function(a, b){return a - b})

          // Find first available
          WorkingOrderArray.forEach(function(WorkingOrder){
            if(WorkingOrder == OrderNew) {
              OrderNew = OrderNew + 1
            }
          })
        }

        // Determine field value
        if(TypeNew == 'series') {
          ValueNew = 'A,B,C'
        } else if(TypeNew == 'incremental') {
          ValueNew = '1'
        } else if(TypeNew == 'static') {
          ValueNew = 'Port'
        }

        // Apply new values
        vm.SelectedPortFormat[PortFormatIndex].value = ValueNew
        vm.SelectedPortFormat[PortFormatIndex].type = TypeNew
        vm.SelectedPortFormat[PortFormatIndex].count = 0
        vm.SelectedPortFormat[PortFormatIndex].order = OrderNew

      }

    },
    TemplatePartitionPortFormatCountUpdated: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = vm.SelectedPortFormatIndex[Context]
      const SelectedPortFormat = vm.SelectedPortFormat
      const CountNew = EmitData.value

      if(Context == 'template') {

        const PartitionAddressSelected = vm.PartitionAddressSelected[Context]
        const TemplateID = PartitionAddressSelected.template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const PartitionAddress = PartitionAddressSelected[TemplateFace]

        const UpdateData = {
          "port_format": {
            "template_id": TemplateID,
            "template_face": TemplateFace,
            "template_partition": PartitionAddress,
            "port_format_index": PortFormatIndex,
            "port_format_attr": 'count',
            "port_format_value": CountNew,
          }
        }

        vm.TemplateEdited(UpdateData)

      } else {

        // Apply new value
        SelectedPortFormat[PortFormatIndex].count = CountNew
      }

    },
    TemplatePartitionPortFormatOrderUpdated: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const SelectedTemplateID = vm.PartitionAddressSelected[Context].template_id
      const SelectedTemplateFace = vm.TemplateFaceSelected[Context]
      const SelectedTemplatePartitionAddress = vm.PartitionAddressSelected[Context][SelectedTemplateFace]
      const SelectedTemplateIndex = vm.GetTemplateIndex(SelectedTemplateID, Context)
      const SelectedTemplate = vm.Templates[Context][SelectedTemplateIndex]
      const SelectedBlueprint = SelectedTemplate.blueprint[SelectedTemplateFace]
      const SelectedPartition = vm.GetPartition(SelectedBlueprint, SelectedTemplatePartitionAddress)
      const SelectedPortFormat = SelectedPartition.port_format
      const PortFormatIndex = vm.SelectedPortFormatIndex[Context]
      const PortFormatValue = EmitData.value
      const PortFormatValueOrig = SelectedPortFormat[PortFormatIndex].order

      if(Context == 'template') {

        const PartitionAddressSelected = vm.PartitionAddressSelected[Context]
        const TemplateID = PartitionAddressSelected.template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const PartitionAddress = PartitionAddressSelected[TemplateFace]

        const UpdateData = {
          "port_format": {
            "template_id": TemplateID,
            "template_face": TemplateFace,
            "template_partition": PartitionAddress,
            "port_format_index": PortFormatIndex,
            "port_format_attr": 'order',
            "port_format_value": PortFormatValue,
          }
        }

        vm.TemplateEdited(UpdateData)

      } else {

        SelectedPortFormat.forEach(function(PortFormatField, index){

          if(index != PortFormatIndex) {

            // Is field incrementable?
            const PortFormatFieldType = PortFormatField.type
            if(PortFormatFieldType == 'incremental' || PortFormatFieldType == 'series') {
              
              // Adjust field order
              const PortFormatFieldOrder = PortFormatField.order
              if(PortFormatFieldOrder > PortFormatValue && PortFormatFieldOrder < PortFormatValueOrig) {

                // Increment
                vm.SelectedPortFormat[index].order = PortFormatFieldOrder + 1
              } else if(PortFormatFieldOrder < PortFormatValue && PortFormatFieldOrder > PortFormatValueOrig) {

                // Decrement
                vm.SelectedPortFormat[index].order = PortFormatFieldOrder - 1
              } else if(PortFormatFieldOrder == PortFormatValue) {
                if(PortFormatValue > PortFormatValueOrig) {

                  // Decrement
                  vm.SelectedPortFormat[index].order = PortFormatFieldOrder - 1
                } else if(PortFormatValue < PortFormatValueOrig) {

                  // Increment
                  vm.SelectedPortFormat[index].order = PortFormatFieldOrder + 1
                }
              }
            }
          }
        })

        // Update field order
        SelectedPortFormat[PortFormatIndex].order = PortFormatValue
      }

    },
    TemplatePartitionPortFormatFieldMove: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = vm.SelectedPortFormatIndex[Context]
      const SelectedPortFormat = vm.SelectedPortFormat
      const MoveDirection = EmitData.direction
      
      // Determine new position
      let PortFormatIndexTo
      if(MoveDirection == 'left') {
        PortFormatIndexTo = (PortFormatIndex == 0) ? 0 : PortFormatIndex - 1
      } else {
        PortFormatIndexTo = (PortFormatIndex == (SelectedPortFormat.length - 1)) ? PortFormatIndex : PortFormatIndex + 1
      }
      
      if(Context == 'template') {

        const PartitionAddressSelected = vm.PartitionAddressSelected[Context]
        const TemplateID = PartitionAddressSelected.template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const PartitionAddress = PartitionAddressSelected[TemplateFace]

        const UpdateData = {
          "port_format": {
            "template_id": TemplateID,
            "template_face": TemplateFace,
            "template_partition": PartitionAddress,
            "port_format_index": PortFormatIndex,
            "port_format_attr": 'position',
            "port_format_value": PortFormatIndexTo,
          }
        }

        vm.TemplateEdited(UpdateData)
      } else {

        // Move field to new position
        SelectedPortFormat.splice(PortFormatIndexTo, 0, SelectedPortFormat.splice(PortFormatIndex, 1)[0])
      }

      // Adjust port format index
      vm.SelectedPortFormatIndex[Context] = (MoveDirection == 'left') ? PortFormatIndex - 1 : PortFormatIndex + 1

    },
    TemplatePartitionPortFormatFieldCreate: function(EmitData) {
      
      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = vm.SelectedPortFormatIndex[Context]
      const SelectedPortFormat = vm.SelectedPortFormat
      const Position = EmitData.position
      const InsertPosition = (Position == 'before') ? PortFormatIndex : PortFormatIndex + 1
      const DefaultPortFormatField = {'type': 'static', 'value': 'Port', 'count': 0, 'order': 0}

      // Limit number of fields to 5
      if(SelectedPortFormat.length < 5) {

        if(Context == 'template') {

          const PartitionAddressSelected = vm.PartitionAddressSelected[Context]
          const TemplateID = PartitionAddressSelected.template_id
          const TemplateFace = vm.TemplateFaceSelected[Context]
          const PartitionAddress = PartitionAddressSelected[TemplateFace]

          const UpdateData = {
            "port_format": {
              "template_id": TemplateID,
              "template_face": TemplateFace,
              "template_partition": PartitionAddress,
              "port_format_index": PortFormatIndex,
              "port_format_attr": 'create',
              "port_format_value": InsertPosition,
            }
          }

          vm.TemplateEdited(UpdateData)
        } else {

          // Insert new field
          SelectedPortFormat.splice(InsertPosition, 0, DefaultPortFormatField)
        }

        // Adjust port format index
        vm.SelectedPortFormatIndex[Context] = (Position == 'before') ? PortFormatIndex + 1 : PortFormatIndex
      }
    },
    TemplatePartitionPortFormatFieldDelete: function(EmitData) {
      
      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = vm.SelectedPortFormatIndex[Context]
      const SelectedPortFormatLength = vm.SelectedPortFormat.length
      
      if(Context == 'template') {

        const PartitionAddressSelected = vm.PartitionAddressSelected[Context]
        const TemplateID = PartitionAddressSelected.template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const PartitionAddress = PartitionAddressSelected[TemplateFace]

        const UpdateData = {
          "port_format": {
            "template_id": TemplateID,
            "template_face": TemplateFace,
            "template_partition": PartitionAddress,
            "port_format_index": PortFormatIndex,
            "port_format_attr": 'delete',
            "port_format_value": PortFormatIndex,
          }
        }

        vm.TemplateEdited(UpdateData)
      } else {

        const SelectedPortFormat = vm.SelectedPortFormat
        const FieldType = SelectedPortFormat[PortFormatIndex].type
        const FieldOrder = SelectedPortFormat[PortFormatIndex].order

        // Adjust incremental order
        if(FieldType == 'series' || FieldType == 'incremental') {
          SelectedPortFormat.forEach(function(PortFormatField){
            const PortFormatFieldType = PortFormatField.type
            if(PortFormatFieldType == 'series' || PortFormatFieldType == 'incremental') {
              const PortFormatFieldOrder = PortFormatField.order
              if(PortFormatFieldOrder > FieldOrder) {
                PortFormatField.order = PortFormatFieldOrder - 1
              }
            }
          })
        }

        // Delete selected field
        if(SelectedPortFormat.length > 1) {
          SelectedPortFormat.splice(PortFormatIndex, 1)
        }
      }

      // Adjust port format index
      if(PortFormatIndex >= (SelectedPortFormatLength - 1)) {
        vm.SelectedPortFormatIndex[Context] = PortFormatIndex - 1
      }

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
    TemplateObjectDeleteClicked: function() {
      
			const vm = this
			const TemplateID = vm.PartitionAddressSelected.template.template_id
			const Context = 'template'
			const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
			const TemplateName = vm.Templates[Context][TemplateIndex].name
			
      vm.$bvModal
        .msgBoxConfirm('Delete '+TemplateName+'?', {
          title: 'Confirm',
          size: 'sm',
          okVariant: 'primary',
          okTitle: 'Yes',
          cancelTitle: 'No',
          cancelVariant: 'outline-secondary',
          hideHeaderClose: false,
          centered: true,
        })
        .then(value => {
					
					if(value) {
						
						// Store data
						const url = '/api/templates/'+TemplateID

						// DELETE template ID
						this.$http.delete(url).then(function(response){

							// Default selected template
							vm.PartitionAddressSelected[Context].template_id = null

              // Get template object data
              let TemplateObjectIndex = vm.Objects[Context].findIndex((object) => object.template_id == TemplateID)
              let TemplateObject = vm.Objects[Context][TemplateObjectIndex]
              let TemplateObjectParentID = TemplateObject.parent_id

              // Delete template and object
              vm.Objects[Context].splice(TemplateObjectIndex,1)
              vm.Templates[Context].splice(TemplateIndex,1)

              // Delete parent template(s) and object(s) if necessary
              while (TemplateObjectParentID != null) {

                TemplateObjectIndex = vm.Objects[Context].findIndex((object) => object.id == TemplateObjectParentID)
                TemplateObject = vm.Objects[Context][TemplateObjectIndex]
                TemplateObjectParentID = TemplateObject.parent_id

                let TemplateObjectTemplateID = TemplateObject.template_id
                let TemplateObjectTemplateIndex = vm.GetTemplateIndex(TemplateObjectTemplateID, Context)

                vm.Objects[Context].splice(TemplateObjectIndex,1)
                vm.Templates[Context].splice(TemplateObjectTemplateIndex,1)
              }

						}).catch(error => {

							// Display error to user via toast
							vm.$bvToast.toast(JSON.stringify(error.response.data), {
								title: 'Error',
								variant: 'danger',
							})

						});
					}
        })
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
    templatesGET: function () {

      const vm = this
			const Context = 'template'
      
      this.$http.get('/api/templates').then(function(response){

        vm.Templates.template = response.data
      });
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
    mediumGET: function() {

      const vm = this;

      vm.$http.get('/api/medium').then(function(response){
        vm.MediaData = response.data;
      });
    },
    GETPortConnectors: function() {

      const vm = this;

      vm.$http.get('/api/port-connectors').then(function(response){
        vm.PortConnectorData = response.data;
      });
    },
    portOrientationGET: function() {

      const vm = this;

      vm.$http.get('/api/port-orientation').then(function(response){
        vm.PortOrientationData = response.data;
      });
    },
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
    Templates: {
      deep: true,
      handler() {

        const vm = this
        if(vm.TemplatesWatcherActive && vm.TemplatesReady.template) {

          vm.TemplatesWatcherActive = false
          const Context = 'template'
          const WorkspaceStandardID = vm.$store.state.pcmProps.WorkspaceStandardID

          vm.Templates[Context].forEach(function(template) {
            vm.GeneratePseudoData(Context, template)
          })
          vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:Context, ReadyState:true})

          // Set standard object as selected
          vm.PartitionAddressSelected['workspace'].object_id = WorkspaceStandardID
          
        }
      }
    },
  },
  mounted() {

    const vm = this
    
    vm.templatesGET()
    vm.mediumGET()
    vm.portOrientationGET()
    vm.GETPortConnectors()
  },
}
</script>

<style>

</style>

<template>
  <div>
    <b-container class="bv-example-row" fluid="xs">
      <b-row>
        <b-col>
          <b-card
            title="Properties"
          >
            <b-card-body>
              <templates-form
                :CategoryData="CategoryData"
                :PortConnectorData="PortConnectorData"
                :PortOrientationData="PortOrientationData"
                :MediaData="MediaData"
                :TemplateData="TemplateData"
                :CabinetFace="CabinetFace"
                :SelectedPortFormatIndex="SelectedPortFormatIndex"
                :SelectedPartitionAddress="SelectedPartitionAddress"
                :AddChildPartitionDisabled="AddChildPartitionDisabled"
                :AddSiblingPartitionDisabled="AddSiblingPartitionDisabled"
                :RemovePartitionDisabled="RemovePartitionDisabled"
                :PartitionTypeDisabled="PartitionTypeDisabled"
                :SelectedPortFormat="SelectedPortFormat"
                :PortIDPreview="PortIDPreview"
                @TemplateNameUpdated="TemplateNameUpdated($event)"
                @TemplateCategoriesUpdated="TemplateCategoriesUpdated($event)"
                @TemplateCategoryUpdated="TemplateCategoryUpdated($event)"
                @TemplateTypeUpdated="TemplateTypeUpdated($event)"
                @TemplateRUSizeUpdated="TemplateRUSizeUpdated($event)"
                @TemplateMountConfigUpdated="TemplateMountConfigUpdated($event)"
                @TemplatePartitionTypeUpdated="TemplatePartitionTypeUpdated($event)"
                @TemplatePartitionAdd="TemplatePartitionAdd($event)"
                @TemplatePartitionRemove="TemplatePartitionRemove()"
                @TemplatePartitionSizeUpdated="TemplatePartitionSizeUpdated($event)"
                @TemplatePartitionPortFormatFieldSelected="TemplatePartitionPortFormatFieldSelected($event)"
                @TemplatePartitionPortFormatValueUpdated="TemplatePartitionPortFormatValueUpdated($event)"
                @TemplatePartitionPortFormatTypeUpdated="TemplatePartitionPortFormatTypeUpdated($event)"
                @TemplatePartitionPortFormatCountUpdated="TemplatePartitionPortFormatCountUpdated($event)"
                @TemplatePartitionPortFormatOrderUpdated="TemplatePartitionPortFormatOrderUpdated($event)"
                @TemplatePartitionPortFormatFieldMove="TemplatePartitionPortFormatFieldMove($event)"
                @TemplatePartitionPortFormatFieldCreate="TemplatePartitionPortFormatFieldCreate($event)"
                @TemplatePartitionPortFormatFieldDelete="TemplatePartitionPortFormatFieldDelete($event)"
                @TemplatePartitionPortLayoutColsUpdated="TemplatePartitionPortLayoutColsUpdated($event)"
                @TemplatePartitionPortLayoutRowsUpdated="TemplatePartitionPortLayoutRowsUpdated($event)"
                @TemplatePartitionMediaUpdated="TemplatePartitionMediaUpdated($event)"
                @TemplatePartitionPortConnectorUpdated="TemplatePartitionPortConnectorUpdated($event)"
                @TemplatePartitionPortOrientationUpdated="TemplatePartitionPortOrientationUpdated($event)"
                @TemplatePartitionEncLayoutColsUpdated="TemplatePartitionEncLayoutColsUpdated($event)"
                @TemplatePartitionEncLayoutRowsUpdated="TemplatePartitionEncLayoutRowsUpdated($event)"
                @FormSubmit="FormSubmit($event)"
                @FormReset="FormReset($event)"
              />
            </b-card-body>
          </b-card>
        </b-col>
        <b-col>
          <b-card
            title="Preview"
          >
            <b-card-body>
              <b-form-radio
                v-model="CabinetFace"
                plain
                value="front"
                :disabled="CabinetFaceToggleIsDisabled"
              >Front
              </b-form-radio>
              <b-form-radio
                v-model="CabinetFace"
                plain
                value="rear"
                :disabled="CabinetFaceToggleIsDisabled"
              >
                Rear
              </b-form-radio>
              <component-cabinet
                :CabinetData="CabinetData"
                :CategoryData="CategoryData"
                :ObjectData="ObjectData"
                :TemplateData="TemplateData"
                :CabinetFace="CabinetFace"
                :SelectedPartitionAddress="SelectedPartitionAddress"
                :HoveredPartitionAddress="HoveredPartitionAddress"
                @PartitionClicked=" PartitionClicked($event) "
                @PartitionHovered=" PartitionHovered($event) "
              />
            </b-card-body>
          </b-card>
        </b-col>
        <b-col>
          <b-card
            title="Details"
          >
            <b-card-text>Bear claw sesame snaps gummies chocolate.</b-card-text>
          </b-card>
        </b-col>
      </b-row>
    </b-container>
    <toast-general/>
  </div>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BCardBody, BCardText, BFormRadio, } from 'bootstrap-vue'
import TemplatesForm from './templates/TemplatesForm.vue'
import ToastGeneral from './templates/ToastGeneral.vue'
import ComponentCabinet from './templates/ComponentCabinet.vue'

const CategoryData = [
  {
    "id": 0,
    "color": "#FFFFFFFF",
  }
]
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
const CabinetFace = "front"
const SelectedPortFormatIndex = 0
const SelectedPartitionAddress = {
  "front": [],
  "rear": []
}
const HoveredPartitionAddress = {
  "front": false,
  "rear": false
}
const CabinetData = {
  "id": 1,
  "size": 25,
  "name": "Cabinet",
}
const ObjectData = [
  {
    "id": 0,
    "location_id": 1,
    "cabinet_ru": 1,
    "cabinet_face": "front",
    "template_id": 0,
    "name": "Object",
  }
]
const TemplateData = [
  {
    "id": 0,
    "name": "New_Template",
    "category_id": 0,
    "type": "standard",
    "ru_size": 2,
    "function": "endpoint",
    "mount_config": "4-post",
    "blueprint": {
      "front": {
        "type": "generic",
        "children": [
          {
            "type":"connectable",
            "units":1,
            "children":[],
            "port_format":[
              {
                "type":"static",
                "value":"Port",
                "count":0,
                "order":0
              },{
                "type":"incremental",
                "value":"1",
                "count":0,
                "order":2
              },{
                "type":"series",
                "value":"a,b,c",
                "count":0,
                "order":1
              }
            ],
            "port_layout":{
              "cols":1,
              "rows":1
            },
            "media":1,
            "port_connector":1,
            "port_orientation":1
          }
        ],
      },
      "rear": {
        "children": [
          {
            "type": "generic",
            "units": 1,
            "children": [],
          },
        ],
      },
    }
  }
]

export default {
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
  },
  data() {
    return {
      CategoryData,
      MediaData,
      PortConnectorData,
      PortOrientationData,
      CabinetData,
      ObjectData,
      TemplateData,
      TemplateDataOriginal: JSON.parse(JSON.stringify(TemplateData)),
      CabinetFace,
      SelectedPortFormatIndex,
      SelectedPartitionAddress,
      HoveredPartitionAddress,
    }
  },
  computed: {
    PortIDPreview: function(){
      
      const vm = this
      const Partition = vm.GetPartition()
      let PortID = ''
      let PortIDPreviewArray = []

      if(Partition.type == 'connectable') {
        let PortTotal = Partition.port_layout.cols * Partition.port_layout.rows
        let Truncated = false

        // Limit port preview to 5
        if(PortTotal > 5) {
          PortTotal = 5
          Truncated = true
        }

        // Generate port IDs
        for(let i=0; i<PortTotal; i++){
          PortID = vm.GeneratePortID(i, PortTotal)
          PortIDPreviewArray.push(PortID)
        }

        // Append ellipses if port preview is truncated
        if(Truncated) {
          PortIDPreviewArray.push('...')
        }
      }

      return PortIDPreviewArray.join(', ')

    },
    CabinetFaceToggleIsDisabled: function() {
      return this.TemplateData[0].mount_config === '2-post'
    },
    AddChildPartitionDisabled: function() {
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const Partition = vm.GetPartition(PartitionAddress)

      return (!vm.GetPartitionUnitsAvailable() || Partition.type != 'generic')

      //return !vm.TemplateSelectedPartitionRoomAvailable()
    },
    AddSiblingPartitionDisabled: function() {
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)

      return (!vm.GetPartitionUnitsAvailable(PartitionParentAddress) || PartitionAddress.length == 0)

      //return !vm.TemplateParentPartitionRoomAvailable()
    },
    RemovePartitionDisabled: function() {
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]

      return !SelectedPartitionAddress.length
    },
    PartitionTypeDisabled: function() {
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]

      return !SelectedPartitionAddress.length
    },
    SelectedPortFormat: function() {
      // Store variables
      const vm = this
      const Partition = vm.GetPartition()
      let SelectedPortFormat = []

      if(Partition.type == 'connectable') {
        SelectedPortFormat = Partition.port_format
      }

      return SelectedPortFormat
    },
  },
  methods: {
    PartitionClicked: function(PartitionAddress) {

      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace

      vm.SelectedPartitionAddress[CabinetFace] = PartitionAddress

    },
    PartitionHovered: function(HoverData) {

      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = HoverData.PartitionAddress
      const HoverState = HoverData.HoverState

      vm.HoveredPartitionAddress[CabinetFace] = (HoverState) ? PartitionAddress : false
    },
    TemplateNameUpdated: function(newValue) {
      this.TemplateData[0].name = newValue
    },
    TemplateCategoriesUpdated: function() {
      const vm = this;

      vm.categoryGET();
    },
    TemplateCategoryUpdated: function(newValue) {
      this.TemplateData[0].category_id = newValue
    },
    TemplateTypeUpdated: function(newValue) {
      this.TemplateData[0].type = newValue
    },
    TemplateRUSizeUpdated: function(newValue) {
      this.TemplateData[0].ru_size = newValue
    },
    TemplateMountConfigUpdated: function(newValue) {
      this.TemplateData[0].mount_config = newValue
      this.CabinetFace = this.TemplateData[0].mount_config == '2-post' ? 'front' : this.CabinetFace
    },
    TemplatePartitionTypeUpdated: function(newValue) {

      // Store variables
      const vm = this
      let SelectedPartition = vm.GetPartition()
      const PartitionTypeOriginal = SelectedPartition.type

      SelectedPartition.type = newValue

      if(PartitionTypeOriginal != newValue) {

        // Define port_layout if it doesn't exist
        if(newValue == 'connectable') {

          // Port Format
          const PortFormat = [
            {'type': 'static', 'value': 'Port', 'count': 0, 'order': 0},
            {'type': 'incremental', 'value': '1', 'count': 0, 'order': 2},
            {'type': 'series', 'value': 'A,B,C', 'count': 0, 'order': 1},
          ]

          // Port layout
          const PortLayout = {'cols': 1, 'rows': 1}

          // Port media type
          const defaultMediaIndex = vm.MediaData.findIndex((media) => media.default);
          const defaultMediaValue = vm.MediaData[defaultMediaIndex].value
          const Media = defaultMediaValue

          // Port connector
          const defaultPortConnectorIndex = vm.PortConnectorData.findIndex((PortConnector) => PortConnector.default);
          const defaultPortConnectorValue = vm.PortConnectorData[defaultPortConnectorIndex].value
          const PortConnector = defaultPortConnectorValue

          // Port orientation
          const defaultPortOrientationIndex = vm.PortOrientationData.findIndex((PortOrientation) => PortOrientation.default);
          const defaultPortOrientationValue = vm.PortOrientationData[defaultPortOrientationIndex].value
          const PortOrientation = defaultPortOrientationValue

          // Set connectable properties
          vm.$set(SelectedPartition, 'port_format', PortFormat)
          vm.$set(SelectedPartition, 'port_layout', PortLayout)
          vm.$set(SelectedPartition, 'media', Media)
          vm.$set(SelectedPartition, 'port_connector', PortConnector)
          vm.$set(SelectedPartition, 'port_orientation', PortOrientation)

        } else if(newValue == 'enclosure') {
          
          const EncLayout = {'cols': 1, 'rows': 1}
          vm.$set(SelectedPartition, 'enc_layout', EncLayout)

        }
      }
    },
    TemplatePartitionAdd: function(InsertPosition) {

      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      let PartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const PartitionParentAddress = (PartitionAddress.length > 0) ? PartitionAddress.slice(0, PartitionAddress.length - 1) : PartitionAddress
      const PartitionIndex = PartitionAddress[PartitionAddress.length - 1]
      let SelectedPartition = vm.GetPartition()
      let SelectedPartitionParent = vm.GetPartition(PartitionParentAddress)
      let PartitionBlank = {
        "type": "generic",
        "units": 1,
        "children": [],
      }

      if(InsertPosition == 'after' || InsertPosition == 'before') {

        // Determine if partition has space available
        if(vm.GetPartitionUnitsAvailable(PartitionParentAddress)) {
          if(InsertPosition == 'after') {

            // Insert new partition after selected partition
            SelectedPartitionParent.children.splice(PartitionIndex + 1, 0, PartitionBlank)
          } else if (InsertPosition == 'before') {

            // Insert new partition before selected partition
            SelectedPartitionParent.children.splice(PartitionIndex, 0, PartitionBlank)

            // Update SelectedPartitionAddress as it is shifted right
            PartitionAddress[PartitionAddress.length - 1] = PartitionIndex + 1
          }
        }
      } else if (InsertPosition == 'inside') {

        // Determine if partition has space available
        if(vm.GetPartitionUnitsAvailable()) {
          SelectedPartition.children.push(PartitionBlank)
        }
      }
    },
    TemplatePartitionRemove: function() {
      
      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const SelectedPartitionAddressCopy = JSON.parse(JSON.stringify(SelectedPartitionAddress))
      let SelectedPartitionParent = TemplateData.blueprint[CabinetFace]
      const SelectedPartitionIndex = SelectedPartitionAddressCopy.pop()
      
      // Traverse blueprint until selected partition is reached
      if(SelectedPartitionIndex !== 'undefined') {

        // Repoint selected partition address to next available
        if(SelectedPartitionIndex > 0) {
          SelectedPartitionAddress[SelectedPartitionAddress.length - 1] = SelectedPartitionIndex - 1
        } else {
          SelectedPartitionAddress.pop()
        }

        // Identify selected partition parent
        SelectedPartitionAddressCopy.some(function(AddressIndex) {
          SelectedPartitionParent = SelectedPartitionParent.children[AddressIndex]
        })
      }

      // Remove selected partition from parent
      vm.$delete(SelectedPartitionParent.children, SelectedPartitionIndex)
    },
    TemplatePartitionSizeUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.units = newValue
    },
    TemplatePartitionPortFormatValueUpdated: function(newValue) {

      const vm = this
      const PortFormatIndex = newValue.index
      const PortFormatValue = newValue.value
      vm.SelectedPortFormat[PortFormatIndex].value = PortFormatValue

    },
    TemplatePartitionPortFormatTypeUpdated: function(EmitData) {

      const vm = this
      const SelectedPortFormat = vm.SelectedPortFormat
      const SelectedPortFormatIndex = vm.SelectedPortFormatIndex
      const TypeNew = EmitData.value
      let OrderNew = 0
      let ValueNew = ''

      // Determine field order
      if(TypeNew == 'series' || TypeNew == 'incremental') {

        OrderNew = 1
        let WorkingOrderArray = []

        // Gather all incrementable field orders
        SelectedPortFormat.forEach(function(PortFormatField, PortFormatFieldIndex){

          const PortFormatFieldType = PortFormatField.type
          
          if((PortFormatFieldType == 'series' || PortFormatFieldType == 'incremental') && PortFormatFieldIndex != SelectedPortFormatIndex) {
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
      vm.SelectedPortFormat[SelectedPortFormatIndex].value = ValueNew
      vm.SelectedPortFormat[SelectedPortFormatIndex].type = TypeNew
      vm.SelectedPortFormat[SelectedPortFormatIndex].count = 0
      vm.SelectedPortFormat[SelectedPortFormatIndex].order = OrderNew

    },
    TemplatePartitionPortFormatCountUpdated: function(EmitData) {

      const vm = this
      const SelectedPortFormat = vm.SelectedPortFormat
      const PortFormatIndex = vm.SelectedPortFormatIndex
      const CountNew = EmitData.value

      // Apply new value
      SelectedPortFormat[PortFormatIndex].count = CountNew

    },
    TemplatePartitionPortFormatOrderUpdated: function(EmitData) {

      const vm = this
      const SelectedPortFormat = vm.SelectedPortFormat
      const PortFormatIndex = EmitData.index
      const PortFormatValue = EmitData.value
      const PortFormatValueOrig = SelectedPortFormat[PortFormatIndex].order

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

    },
    TemplatePartitionPortFormatFieldSelected: function(EmitData) {

      const vm = this
      const SelectedPortFormatIndexNew = EmitData.index
      vm.SelectedPortFormatIndex = SelectedPortFormatIndexNew

    },
    TemplatePartitionPortFormatFieldMove: function(EmitData) {

      const vm = this
      const SelectedPortFormat = vm.SelectedPortFormat
      const PortFormatIndexFrom = vm.SelectedPortFormatIndex
      const MoveDirection = EmitData.direction
      let PortFormatIndexTo
      
      // Determine new position
      if(MoveDirection == 'left') {
        PortFormatIndexTo = (PortFormatIndexFrom == 0) ? 0 : PortFormatIndexFrom - 1
      } else {
        PortFormatIndexTo = (PortFormatIndexFrom == (SelectedPortFormat.length - 1)) ? PortFormatIndexFrom : PortFormatIndexFrom + 1
      }

      // Move field to new position
      SelectedPortFormat.splice(PortFormatIndexTo, 0, SelectedPortFormat.splice(PortFormatIndexFrom, 1)[0])

      // Update selected fieldIndex
      vm.SelectedPortFormatIndex = PortFormatIndexTo

    },
    TemplatePartitionPortFormatFieldCreate: function(EmitData) {
      
      const vm = this
      const SelectedPortFormat = vm.SelectedPortFormat
      const SelectedPortFormatIndex = vm.SelectedPortFormatIndex
      const Direction = EmitData.direction
      const InsertPosition = (Direction == 'before') ? SelectedPortFormatIndex : SelectedPortFormatIndex + 1
      const SelectedPortFormatIndexNew = (Direction == 'before') ? SelectedPortFormatIndex + 1 : SelectedPortFormatIndex
      const DefaultPortFormatField = {'type': 'static', 'value': 'Port', 'count': 0, 'order': 0}

      // Limit number of fields to 5
      if(SelectedPortFormat.length < 5) {

        // Insert new field
        SelectedPortFormat.splice(InsertPosition, 0, DefaultPortFormatField)

        // Update selected field index
        vm.SelectedPortFormatIndex = SelectedPortFormatIndexNew
      }
    },
    TemplatePartitionPortFormatFieldDelete: function(EmitData) {
      
      const vm = this
      const SelectedPortFormat = vm.SelectedPortFormat
      const SelectedPortFormatIndex = vm.SelectedPortFormatIndex
      const FieldType = SelectedPortFormat[SelectedPortFormatIndex].type
      const FieldOrder = SelectedPortFormat[SelectedPortFormatIndex].order
      const SelectedPortFormatIndexNew = (SelectedPortFormatIndex == 0) ? 0 : SelectedPortFormatIndex - 1
      
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
        SelectedPortFormat.splice(SelectedPortFormatIndex, 1)
      }

      // Update selected field index
      vm.SelectedPortFormatIndex = SelectedPortFormatIndexNew

    },
    TemplatePartitionPortLayoutColsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.port_layout.cols = newValue
    },
    TemplatePartitionPortLayoutRowsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.port_layout.rows = newValue
    },
    TemplatePartitionMediaUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.media = newValue
    },
    TemplatePartitionPortConnectorUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.port_connector = newValue
    },
    TemplatePartitionPortOrientationUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.port_orientation = newValue
    },
    TemplatePartitionEncLayoutColsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.enc_layout.cols = newValue
    },
    TemplatePartitionEncLayoutRowsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.enc_layout.rows = newValue
    },
    GetPartitionDirection: function(PartAddr = false) {
      
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = (PartAddr) ? PartAddr : vm.SelectedPartitionAddress[CabinetFace]
      const PartitionDirection = (PartitionAddress.length % 2) ? 'column' : 'row'

      return PartitionDirection
    },
    GetPartitionUnitsAvailable: function(PartAddr = false) {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = (PartAddr) ? PartAddr : vm.SelectedPartitionAddress[CabinetFace]
      const Partition = vm.GetPartition(PartitionAddress)
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
      let UnitsAvailable = (PartitionDirection == 'row') ? 24 : TemplateData.ru_size * 2

      if(PartitionAddress.length > 1) {
        const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
        const PartitionParent = vm.GetPartition(PartitionParentAddress)
        UnitsAvailable = PartitionParent.units
      }

      Partition.children.forEach(function(PartitionChild) {
        UnitsAvailable = UnitsAvailable - PartitionChild.units
      })

      return UnitsAvailable
    },
    GetPartition: function(PartAddr = false) {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = (PartAddr) ? PartAddr : vm.SelectedPartitionAddress[CabinetFace]
      let WorkingPartition = TemplateData.blueprint[CabinetFace]

      // Traverse blueprint until selected partition is reached
      PartitionAddress.forEach(function(AddressIndex, Index) {
        WorkingPartition = WorkingPartition.children[AddressIndex]
      })

      // Return selected partition
      return WorkingPartition
    },
    categoryGET: function(SetCategoryToDefault = false) {

      const vm = this;

      this.$http.get('/api/category').then(function(response){
        vm.CategoryData = response.data;

        // Apply default category to template preview
        if(SetCategoryToDefault) {
          const defaultCategoryIndex = vm.CategoryData.findIndex((category) => category.default);
          const defaultCategoryID = vm.CategoryData[defaultCategoryIndex].id
          vm.TemplateData[0].category_id = defaultCategoryID
        }
      });
    },
    mediumGET: function() {

      const vm = this;

      this.$http.get('/api/medium').then(function(response){
        vm.MediaData = response.data;
      });
    },
    portOrientationGET: function() {

      const vm = this;

      this.$http.get('/api/port-orientation').then(function(response){
        vm.PortOrientationData = response.data;
      });
    },
    GeneratePortID: function(Index, PortTotal){

      // Store variables
      const vm = this
      const SelectedPortFormat = JSON.parse(JSON.stringify(vm.SelectedPortFormat))
      let PortIDPreview = ''
      let IncrementalCount = 0

      // Create character arrays
      let LowercaseArray = []
      let UppercaseArray = []
      for(let x=97; x<=122; x++) {
        LowercaseArray.push(String.fromCharCode(x))
      }
      for(let x=65; x<=90; x++) {
        UppercaseArray.push(String.fromCharCode(x))
      }

      // Account for infinite count incrementables
      SelectedPortFormat.forEach(function(Field, FieldIndex){
        const FieldType = Field.type
        let FieldCount = Field.count

        // Increment IncrementalCount
        if(FieldType == 'incremental' || FieldType == 'series') {
          IncrementalCount++

          // Adjust field count
          if(FieldType == 'incremental' && FieldCount == 0) {
            SelectedPortFormat[FieldIndex].count = PortTotal
          } else if(FieldType == 'series') {
            let CurrentFieldValue = Field.value
            SelectedPortFormat[FieldIndex].count = CurrentFieldValue.split(',').length
          }
        }
      })

      SelectedPortFormat.forEach(function(Field){
        const FieldType = Field.type
        const FieldValue = Field.value
        const FieldOrder = Field.order
        const FieldCount = Field.count
        let HowMuchToIncrement
        let RollOver
        let Numerator = 1

        if(FieldType == 'static') {
          PortIDPreview = PortIDPreview + FieldValue
        } else {

          SelectedPortFormat.forEach(function(LoopField){
            const LoopFieldType = LoopField.type
            const LoopFieldOrder = LoopField.order
            const LoopFieldCount = LoopField.count

            if(LoopFieldType == 'incremental' || LoopFieldType == 'series') {
              if(LoopFieldOrder < FieldOrder) {
                Numerator *= LoopFieldCount
              }
            }
          })

          HowMuchToIncrement = Math.floor(Index / Numerator)

          if(HowMuchToIncrement >= FieldCount) {
            RollOver = Math.floor(HowMuchToIncrement / FieldCount)
            HowMuchToIncrement = HowMuchToIncrement - (RollOver * FieldCount)
            
          }

          if(FieldType == 'incremental') {

            if(!isNaN(FieldValue)) {
              PortIDPreview = PortIDPreview + (parseInt(FieldValue) + HowMuchToIncrement)
            } else {
              PortIDPreview = PortIDPreview + '-'
            }

          } else if(FieldType == 'series') {

            const FieldValueArray = FieldValue.split(',')
            PortIDPreview = PortIDPreview + FieldValueArray[HowMuchToIncrement]

          }
        }
      })
      
      return PortIDPreview
    },
    FormSubmit: function() {

      // Store data
      const vm = this;
      const url = '/api/template'
      const data = vm.TemplateData[0]

      // POST category form data
      this.$http.post(url, data).then(function(response){

        // Update categories
        console.log('Form submitted')

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
    FormReset: function() {
      const vm = this
      for (const [key] of Object.entries(vm.TemplateData[0])) {
        console.log('key: '+key)
        console.log('original: '+vm.TemplateDataOriginal[0][key])
        vm.TemplateData[0][key] = vm.TemplateDataOriginal[0][key]
      }
    },
  },
  mounted() {

    const vm = this;
    const SetCategoryToDefault = true

    vm.categoryGET(SetCategoryToDefault)
    vm.mediumGET()
    vm.portOrientationGET()
  },
}
</script>

<style>

</style>

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
                :TemplateData="TemplateData"
                :CategoryData="CategoryData"
                :SelectedCategoryID="SelectedCategoryID"
                :PortConnectorData="PortConnectorData"
                :PortOrientationData="PortOrientationData"
                :MediaData="MediaData"
                :PreviewData="PreviewData"
                :SelectedPortFormatIndex="SelectedPortFormatIndex"
                Context="preview"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :AddChildPartitionDisabled="AddChildPartitionDisabled"
                :AddSiblingPartitionDisabled="AddSiblingPartitionDisabled"
                :RemovePartitionDisabled="RemovePartitionDisabled"
                :PartitionTypeDisabled="PartitionTypeDisabled"
                :SelectedPartition="SelectedPartition"
                :SelectedPortFormat="SelectedPortFormat"
                :PreviewPortID="PreviewPortID"
                @TemplateNameUpdated="TemplateNameUpdated($event)"
                @TemplateCategoriesUpdated="TemplateCategoriesUpdated($event)"
                @TemplateCategoryUpdated="TemplateCategoryUpdated($event)"
                @TemplateCategorySubmitted="TemplateCategorySubmitted($event)"
                @TemplateCategoryDeleted="TemplateCategoryDeleted($event)"
                @TemplateCategorySelected="TemplateCategorySelected($event)"
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
                v-model="TemplateFaceSelected.preview"
                plain
                value="front"
                :disabled="TemplateFaceToggleIsDisabled"
              >Front
              </b-form-radio>
              <b-form-radio
                v-model="TemplateFaceSelected.preview"
                plain
                value="rear"
                :disabled="TemplateFaceToggleIsDisabled"
              >
                Rear
              </b-form-radio>
              <component-cabinet
                :CabinetData="CabinetData"
                :CategoryData="CategoryData"
                :ObjectData="ObjectData"
                :PreviewData="PreviewData"
                Context="preview"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :PartitionAddressHovered="PartitionAddressHovered"
                @PartitionClicked=" PartitionClicked($event) "
                @PartitionHovered=" PartitionHovered($event) "
              />
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>

          <b-card
            title="Template Details"
          >
            <b-card-body>
              <component-template-Object-details
                :TemplateData="TemplateData"
                :CategoryData="CategoryData"
                Context="template"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :TemplatePartitionPortRange="TemplatePartitionPortRange"
              />
            </b-card-body>
          </b-card>

          <b-card
            title="Templates"
          >
            <b-card-body>
              <component-templates
                :TemplateData="TemplateData"
                :CategoryData="CategoryData"
                Context="template"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :PartitionAddressHovered="PartitionAddressHovered"
                @PartitionClicked="PartitionClicked($event)"
                @PartitionHovered="PartitionHovered($event)"
                @TemplateFaceChanged="TemplateFaceChanged($event)"
              />
            </b-card-body>
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
import ComponentTemplateObjectDetails from './templates/ComponentTemplateObjectDetails.vue'
import ComponentTemplates from './templates/ComponentTemplates.vue'

const TemplateData = []
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
const SelectedCategoryID = null
const SelectedPortFormatIndex = 0
const TemplateFaceSelected = {
  'preview': 'front',
  'template': 'front',
}
const PartitionAddressSelected = {
  'preview': {
    'template_id': null,
    'front': [0],
    'rear': [0]
  },
  'template': {
    'template_id': null,
    'front': [0],
    'rear': [0]
  }
  ,
  'object': {
    'object_id': null,
    'front': [0],
    'rear': [0]
  }
}
const PartitionAddressHovered = {
  'preview': {
    'template_id': null,
    'front': false,
    'rear': false
  },
  'template': {
    'template_id': null,
    'front': false,
    'rear': false
  }
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
const PreviewData = [
  {
    "id": 0,
    "name": "New_Template",
    "category_id": 0,
    "type": "standard",
    "ru_size": 1,
    "function": "endpoint",
    "mount_config": "2-post",
    "blueprint": {
      "front": [{
        "type": "generic",
        "units":24,
        "children": [],
      }],
      "rear": [{
        "type": "generic",
        "units":24,
        "children": [],
      }],
    }
  }
]
const PreviewDataIndex = 0

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
    ComponentTemplateObjectDetails,
    ComponentTemplates,
  },
  data() {
    return {
      TemplateData,
      CategoryData,
      SelectedCategoryID,
      MediaData,
      PortConnectorData,
      PortOrientationData,
      CabinetData,
      ObjectData,
      PreviewData,
      PreviewDataIndex,
      PreviewDataOriginal: JSON.parse(JSON.stringify(PreviewData)),
      SelectedPortFormatIndex,
      TemplateFaceSelected,
      PartitionAddressSelected,
      PartitionAddressHovered,
    }
  },
  computed: {
    TemplatePartitionPortRange: function(){
      
      // Initialize some variables
      const vm = this
      const TemplateID = vm.PartitionAddressSelected.template.template_id
      let PortRangeString = '-'

      if(TemplateID) {

        // Get template partition address
        const TemplateFace = vm.TemplateFaceSelected.template
        const TemplatePartition = vm.PartitionAddressSelected.template[TemplateFace]

        // Get template blueprint
        const TemplateIndex = vm.GetTemplateIndex(TemplateID)
        const Template = vm.TemplateData[TemplateIndex]
        const TemplateBlueprint = Template.blueprint[TemplateFace]

        // Get template partition
        const Partition = vm.GetPartition(TemplateBlueprint, TemplatePartition)
        const PartitionType = Partition.type

        if(PartitionType == 'connectable') {

          // Calculate port numbers
          const PortFormat = Partition.port_format
          const PortLayoutCols = Partition.port_layout.cols
          const PortLayoutRows = Partition.port_layout.rows
          const PortTotal = PortLayoutCols * PortLayoutRows
          const FirstPortID = 0
          const LastPortID = PortTotal - 1

          const FirstPortIDString = vm.GeneratePortID(FirstPortID, PortTotal, PortFormat)
          const LastPortIDString = vm.GeneratePortID(LastPortID, PortTotal, PortFormat)

          PortRangeString = FirstPortIDString+' - '+LastPortIDString
        }
      }

      return PortRangeString

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
      const PreviewDataIndex = vm.PreviewDataIndex
      return this.PreviewData[PreviewDataIndex].mount_config === '2-post'
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
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddress = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)

      return !vm.GetPartitionUnitsAvailable(PartitionParentAddress)

    },
    RemovePartitionDisabled: function() {
      
      // Store variables
      const vm = this
      const PreviewDataIndex = vm.PreviewDataIndex
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddressSelected = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const Layer1Partitions = vm.PreviewData[PreviewDataIndex].blueprint[TemplateFaceSelected]
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
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddressSelected = vm.PartitionAddressSelected.preview[TemplateFaceSelected]

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
    GetTemplateIndex: function(TemplateID) {

      const vm = this;
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    PartitionClicked: function(EmitData) {

      // Store variables
      const vm = this
      const Context = EmitData.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddress = EmitData.PartitionAddress
      const TemplateID = EmitData.TemplateID

      vm.PartitionAddressSelected[Context][TemplateFaceSelected] = PartitionAddress
      vm.PartitionAddressSelected[Context].template_id = TemplateID

      if(Context == 'template') {

        // Initialize the ID to be used for insert parent template
        const InsertParentTemplateID = 1
        const InsertTemplateID = 2

        // Get selected template data
        const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);
        const Template = vm.TemplateData[TemplateIndex]
        const TemplateRUSize = Template.ru_size
        const TemplateFunction = Template.function
        const TemplateCategoryID = Template.category_id

        // Get selected partition data
        const Blueprint = Template.blueprint[TemplateFaceSelected]
        const Partition = JSON.parse(JSON.stringify(vm.GetPartition(Blueprint, PartitionAddress)))
        const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
        const PartitionType = Partition.type
        const PartitionUnits = Partition.units

        // Get parent partition data
        let ParentPartition
        if(PartitionAddress.length > 1) {
          const ParentPartitionAddress = PartitionAddress.splice(PartitionAddress.length-1, 1)
          ParentPartition = JSON.parse(JSON.stringify(vm.GetPartition(Blueprint, ParentPartitionAddress)))
          const ParentPartitionUnits = ParentPartition.units
        } else {
          ParentPartition = false
        }

        // Prepare insert parent partition
        let InsertParentTemplateRUSize
        let InsertParentBlueprint
        let InsertPartitionUnits
        if(PartitionDirection == 'column') {

          // Set insert parent template ru_size
          InsertParentTemplateRUSize = (ParentPartition) ? Math.ceil(ParentPartitionUnits / 2) : TemplateRUSize
          
          // Set insert parent partition blueprint
          InsertParentBlueprint = Partition

          // Set insert partition units
          InsertPartitionUnits = PartitionUnits

        } else {

          // Set insert parent template ru_size
          InsertParentTemplateRUSize = Math.ceil(PartitionUnits / 2)

          // Adjust insert parent partition blueprint
          InsertParentBlueprint = ParentPartition

          // Set insert partition units
          InsertPartitionUnits = ParentPartitionUnits
        }

        const InsertParentTemplate = {
          "id": InsertParentTemplateID,
          "name": "New_Template",
          "category_id": TemplateCategoryID,
          "type": "standard",
          "ru_size": InsertParentTemplateRUSize,
          "function": "",
          "blueprint": {
            "front": [InsertParentBlueprint],
            "rear": [],
          }
        }

        const InsertTemplate = {
          "id": InsertTemplateID,
          "name": "New_Template",
          "category_id": TemplateCategoryID,
          "type": "insert",
          "function": TemplateFunction,
          "blueprint": {
            "front": [{
              "type": "generic",
              "units":InsertPartitionUnits,
              "children": [],
            }],
            "rear": [],
          }
        }

        if(PartitionType == 'enclosure') {
          vm.PreviewData[1] = InsertParentTemplate
          vm.PreviewData[2] = InsertTemplate
          vm.ObjectData[0].template_id = InsertParentTemplateID
        } else {
          vm.PreviewData.splice(1, 2)
          vm.ObjectData[0].template_id = 0
        }

        console.log('Debug (PartitionClicked-template-PreviewData): '+JSON.stringify(vm.PreviewData))
      }

    },
    PartitionHovered: function(EmitData) {

      // Store variables
      const vm = this
      const Context = EmitData.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddress = EmitData.PartitionAddress
      const HoverState = EmitData.HoverState
      const TemplateID = EmitData.TemplateID

      vm.PartitionAddressHovered[Context][TemplateFaceSelected] = (HoverState) ? PartitionAddress : false
      vm.PartitionAddressHovered[Context].template_id = TemplateID
      //console.log('Debug (TemplatePartitionHovered): '+JSON.stringify(EmitData))

    },
    TemplateFaceChanged: function(EmitData) {

      // Store variables
      const vm = this
      const TemplateFace = EmitData.TemplateFace
      const Context = EmitData.Context
      vm.TemplateFaceSelected[Context] = TemplateFace

    },
    TemplateNameUpdated: function(newValue) {

      const vm = this
      const PreviewDataIndex = vm.PreviewDataIndex
      this.PreviewData[PreviewDataIndex].name = newValue
    },
    TemplateCategoriesUpdated: function() {
      const vm = this;

      vm.categoryGET();
    },
    TemplateCategoryUpdated: function(newValue) {

      const vm = this
      const PreviewDataIndex = vm.PreviewDataIndex
      this.PreviewData[PreviewDataIndex].category_id = newValue
    },
    TemplateCategoryDeleted: function(newValue) {

      // Store data
      const vm = this;
      const CategoryID = newValue.CategoryID
      const url = '/api/category/'+CategoryID

      // DELETE category form data
      vm.$http.delete(url).then(function(response){

        // Clear SelectedCategoryID
        vm.SelectedCategoryID = null

        // Delete category from CategoryData variable
        const returnedCategoryID = response.data.id
        const categoryIndex = vm.CategoryData.findIndex((category) => category.id == returnedCategoryID);
        vm.CategoryData.splice(categoryIndex, 1)
        
      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
    TemplateCategorySubmitted: function(newValue) {

      // Store data
      const vm = this
      const CategoryID = vm.SelectedCategoryID
      const data = {
        name: newValue.name,
        color: newValue.color,
        default: newValue.default,
      };

      if(CategoryID) {

        const url = '/api/category/'+CategoryID

        // PUT category form data
        vm.$http.put(url, data).then(function(response){

          // Update categories
          const CategoryDataIndex = vm.CategoryData.findIndex((category) => category.id == CategoryID);

          // If this category is default, clear any previously default category
          if(response.data.default) {
            const CategoryDataDefaultIndex = vm.CategoryData.findIndex((category) => category.default);
            if(CategoryDataDefaultIndex !== -1) {
              vm.CategoryData[CategoryDataDefaultIndex].default = false
            }
          }

          vm.CategoryData[CategoryDataIndex].name = response.data.name
          vm.CategoryData[CategoryDataIndex].default = response.data.default
          vm.CategoryData[CategoryDataIndex].color = response.data.color

        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response.data), {
            title: 'Error',
            variant: 'danger',
          })

        });

      } else {

        const url = '/api/category'

        // POST category form data
        this.$http.post(url, data).then(function(response){

          // Update categories
          vm.CategoryData.push(response.data)

        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response.data), {
            title: 'Error',
            variant: 'danger',
          })

        });

      }
    },
    TemplateCategorySelected: function(newValue) {

      // Store data
      const vm = this;
      const CategoryID = newValue.CategoryID
      vm.SelectedCategoryID = CategoryID
      
    },
    TemplateTypeUpdated: function(newValue) {

      const vm = this
      const PreviewDataIndex = vm.PreviewDataIndex
      this.PreviewData[PreviewDataIndex].type = newValue
    },
    TemplateRUSizeUpdated: function(newValue) {

      const vm = this
      const PreviewDataIndex = vm.PreviewDataIndex
      this.PreviewData[PreviewDataIndex].ru_size = newValue
    },
    TemplateMountConfigUpdated: function(newValue) {

      const vm = this
      const PreviewDataIndex = vm.PreviewDataIndex
      vm.PreviewData[PreviewDataIndex].mount_config = newValue
      vm.TemplateFaceSelected.preview = newValue == '2-post' ? 'front' : vm.TemplateFaceSelected.preview
    },
    TemplatePartitionTypeUpdated: function(newValue) {

      // Store variables
      const vm = this

      // Get Partition
      const SelectedPartition = vm.GetSelectedPreviewPartition()

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
      const PreviewDataIndex = vm.PreviewDataIndex
      
      // Get Partition
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddress = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const Blueprint = vm.PreviewData[PreviewDataIndex].blueprint[TemplateFaceSelected]
      let SelectedPartition = vm.GetPartition(Blueprint, PartitionAddress)

      // Get Parent Partition
      const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
      let SelectedPartitionParent = vm.GetPartition(Blueprint, PartitionParentAddress)

      const PartitionIndex = PartitionAddress[PartitionAddress.length - 1]
      let PartitionBlank = {
        "type": "generic",
        "units": 1,
        "children": [],
      }

      if(InsertPosition == 'after' || InsertPosition == 'before') {

        // Determine if partition has space available
        if(vm.GetPartitionUnitsAvailable(PartitionParentAddress)) {

          const SelectedPartitionParentChildren = (PartitionParentAddress.length) ? SelectedPartitionParent.children : SelectedPartitionParent

          if(InsertPosition == 'after') {

            // Insert new partition after selected partition
            SelectedPartitionParentChildren.splice(PartitionIndex + 1, 0, PartitionBlank)
          } else if (InsertPosition == 'before') {

            // Insert new partition before selected partition
            SelectedPartitionParentChildren.splice(PartitionIndex, 0, PartitionBlank)

            // Update PartitionAddressSelected as it is shifted right
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
      const PreviewDataIndex = vm.PreviewDataIndex
      const PreviewData = vm.PreviewData[PreviewDataIndex]
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddressSelected = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const PartitionAddressSelectedCopy = JSON.parse(JSON.stringify(PartitionAddressSelected))
      let SelectedPartitionParent = PreviewData.blueprint[TemplateFaceSelected]
      const SelectedPartitionIndex = PartitionAddressSelectedCopy.pop()
      
      // Traverse blueprint until selected partition is reached
      if(SelectedPartitionIndex !== 'undefined') {

        // Repoint selected partition address to next available
        if(SelectedPartitionIndex > 0) {
          PartitionAddressSelected[PartitionAddressSelected.length - 1] = SelectedPartitionIndex - 1
        } else {
          PartitionAddressSelected.pop()
        }

        // Identify selected partition parent
        PartitionAddressSelectedCopy.some(function(AddressIndex) {
          SelectedPartitionParent = SelectedPartitionParent[AddressIndex].children
        })
      }

      // Remove selected partition from parent
      vm.$delete(SelectedPartitionParent, SelectedPartitionIndex)
    },
    TemplatePartitionSizeUpdated: function(newValue) {

      // Get Partition
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()
      
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

      // Get Partition
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()

      SelectedPartition.port_layout.cols = newValue
    },
    TemplatePartitionPortLayoutRowsUpdated: function(newValue) {

      // Get Partition
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()

      SelectedPartition.port_layout.rows = newValue
    },
    TemplatePartitionMediaUpdated: function(newValue) {

      // Get Partition
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()

      SelectedPartition.media = newValue
    },
    TemplatePartitionPortConnectorUpdated: function(newValue) {

      // Get Partition
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()

      SelectedPartition.port_connector = newValue
    },
    TemplatePartitionPortOrientationUpdated: function(newValue) {

      // Store variables
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()
      SelectedPartition.port_orientation = newValue
    },
    TemplatePartitionEncLayoutColsUpdated: function(newValue) {

      // Store variables
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()
      SelectedPartition.enc_layout.cols = newValue
    },
    TemplatePartitionEncLayoutRowsUpdated: function(newValue) {

      // Store variables
      const vm = this
      let SelectedPartition = vm.GetSelectedPreviewPartition()
      SelectedPartition.enc_layout.rows = newValue
    },
    GetPartitionDirection: function(PartitionAddress) {

      const PartitionDirection = (PartitionAddress.length % 2) ? 'column' : 'row'

      return PartitionDirection
    },
    GetPartitionUnitsAvailable: function(PartAddr = false) {

      // Store variables
      const vm = this
      const PreviewDataIndex = vm.PreviewDataIndex
      const PreviewData = vm.PreviewData[PreviewDataIndex]

      // Get Partition
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddress = (PartAddr !== false) ? PartAddr : vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const Blueprint = PreviewData.blueprint[TemplateFaceSelected]
      const Partition = vm.GetPartition(Blueprint, PartitionAddress)

      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
      let UnitsAvailable = (PartitionDirection == 'row') ? 24 : PreviewData.ru_size * 2
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
      const PreviewDataIndex = vm.PreviewDataIndex

      // Get Partition
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartAddr = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const Blueprint = vm.PreviewData[PreviewDataIndex].blueprint[TemplateFaceSelected]

      let SelectedPartition = vm.GetPartition(Blueprint, PartAddr)
      
      return SelectedPartition
    },
    GetPartition: function(Blueprint, PartitionAddress) {

      // Store variables
      let WorkingPartitionChildren = Blueprint
      let SelectedPartition = WorkingPartitionChildren

      // Traverse blueprint until selected partition is reached
      PartitionAddress.forEach(function(AddressIndex, Index) {
        SelectedPartition = WorkingPartitionChildren[AddressIndex]
        WorkingPartitionChildren = SelectedPartition.children
      })

      // Return selected partition
      return SelectedPartition
    },
    templatesGET: function() {

      const vm = this;

      this.$http.get('/api/template').then(function(response){

        vm.TemplateData = response.data;

      });
    },
    categoryGET: function(SetCategoryToDefault = false) {

      const vm = this;
      const PreviewDataIndex = vm.PreviewDataIndex

      this.$http.get('/api/category').then(function(response){

        vm.CategoryData = response.data;

        // Apply default category to template preview
        if(SetCategoryToDefault) {
          const DefaultCategoryIndex = vm.CategoryData.findIndex((category) => category.default);
          let DefaultCategoryID

          if(DefaultCategoryIndex !== -1) {
            DefaultCategoryID = vm.CategoryData[DefaultCategoryIndex].id
          } else {
            DefaultCategoryID = vm.CategoryData[0].id
          }

          vm.PreviewData[PreviewDataIndex].category_id = DefaultCategoryID
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
    GeneratePortID: function(Index, PortTotal, PortFormat){

      // Store variables
      const vm = this
      let PreviewPortID = ''
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
      PortFormat.forEach(function(Field, FieldIndex){
        const FieldType = Field.type
        let FieldCount = Field.count

        // Increment IncrementalCount
        if(FieldType == 'incremental' || FieldType == 'series') {
          IncrementalCount++

          // Adjust field count
          if(FieldType == 'incremental' && FieldCount == 0) {
            PortFormat[FieldIndex].count = PortTotal
          } else if(FieldType == 'series') {
            let CurrentFieldValue = Field.value
            PortFormat[FieldIndex].count = CurrentFieldValue.split(',').length
          }
        }
      })

      PortFormat.forEach(function(Field){
        const FieldType = Field.type
        const FieldValue = Field.value
        const FieldOrder = Field.order
        const FieldCount = Field.count
        let HowMuchToIncrement
        let RollOver
        let Numerator = 1

        if(FieldType == 'static') {
          PreviewPortID = PreviewPortID + FieldValue
        } else {

          PortFormat.forEach(function(LoopField){
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
              PreviewPortID = PreviewPortID + (parseInt(FieldValue) + HowMuchToIncrement)
            } else {
              PreviewPortID = PreviewPortID + '-'
            }

          } else if(FieldType == 'series') {

            const FieldValueArray = FieldValue.split(',')
            PreviewPortID = PreviewPortID + FieldValueArray[HowMuchToIncrement]

          }
        }
      })
      
      return PreviewPortID
    },
    FormSubmit: function() {

      // Store data
      const vm = this;
      const PreviewDataIndex = vm.PreviewDataIndex
      const url = '/api/template'
      const data = vm.PreviewData[PreviewDataIndex]

      // POST category form data
      this.$http.post(url, data).then(function(response){

        // convert JSON to object
        response.data.blueprint = JSON.parse(response.data.blueprint)
        
        // Append new template to template array
        vm.TemplateData.push(response.data)

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
      const PreviewDataIndex = vm.PreviewDataIndex
      for (const [key] of Object.entries(vm.PreviewData[PreviewDataIndex])) {
        console.log('key: '+key)
        console.log('original: '+vm.PreviewDataOriginal[0][key])
        vm.PreviewData[PreviewDataIndex][key] = vm.PreviewDataOriginal[0][key]
      }
    },
  },
  mounted() {

    const vm = this;
    const SetCategoryToDefault = true

    vm.templatesGET()
    vm.categoryGET(SetCategoryToDefault)
    vm.mediumGET()
    vm.portOrientationGET()
  },
}
</script>

<style>

</style>

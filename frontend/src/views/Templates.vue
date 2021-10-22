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
                :PreviewDataIndex="GetTemplateIndex(ActivePreviewTemplateID, 'preview')"
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
                @TemplateFunctionUpdated="TemplateFunctionUpdated($event)"
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
            <b-card-body
              v-if=" PreviewDisplay == 'cabinet' "
            >
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
                :TemplateData="TemplateData"
                :CabinetData="CabinetData"
                :CategoryData="CategoryData"
                :ObjectData="ObjectData"
                Context="preview"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :PartitionAddressHovered="PartitionAddressHovered"
                @PartitionClicked=" PartitionClicked($event) "
                @PartitionHovered=" PartitionHovered($event) "
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
						:TemplateData="TemplateData"
						:CategoryData="CategoryData"
            :ObjectData="ObjectData"
						Context="template"
						:TemplateFaceSelected="TemplateFaceSelected"
						:PartitionAddressSelected="PartitionAddressSelected"
						:TemplatePartitionPortRange="TemplatePartitionPortRange"
            :PortOrientationData="PortOrientationData"
            @TemplateObjectEditClicked="TemplateObjectEditClicked()"
            @TemplateObjectCloneClicked="TemplateObjectCloneClicked()"
						@TemplateObjectDeleteClicked="TemplateObjectDeleteClicked()"
					/>

          <component-templates
            :TemplateData="TemplateData"
            :CategoryData="CategoryData"
            :ObjectData="ObjectData"
            Context="template"
            :TemplateFaceSelected="TemplateFaceSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @PartitionClicked="PartitionClicked($event)"
            @PartitionHovered="PartitionHovered($event)"
            @TemplateFaceChanged="TemplateFaceChanged($event)"
          />

        </b-col>
      </b-row>
    </b-container>

    <!-- Toast -->
    <toast-general/>

    <!-- Template Edit Modal -->
    <modal-templates-edit
      :TemplateData="TemplateData"
      :CategoryData="CategoryData"
      :ObjectData="ObjectData"
      Context="template"
      :TemplateFaceSelected="TemplateFaceSelected"
      :PartitionAddressSelected="PartitionAddressSelected"
      :TemplatePartitionPortRange="TemplatePartitionPortRange"
      :SelectedPortFormat="SelectedPortFormat"
      PreviewPortID="test"
      @TemplateEdited="TemplateEdited($event)"
      @TemplatePartitionPortFormatValueUpdated="TemplatePartitionPortFormatValueUpdated($event)"
      @TemplatePartitionPortFormatTypeUpdated="TemplatePartitionPortFormatTypeUpdated($event)"
      @TemplatePartitionPortFormatCountUpdated="TemplatePartitionPortFormatCountUpdated($event)"
      @TemplatePartitionPortFormatOrderUpdated="TemplatePartitionPortFormatOrderUpdated($event)"
      @TemplatePartitionPortFormatFieldMove="TemplatePartitionPortFormatFieldMove($event)"
      @TemplatePartitionPortFormatFieldCreate="TemplatePartitionPortFormatFieldCreate($event)"
      @TemplatePartitionPortFormatFieldDelete="TemplatePartitionPortFormatFieldDelete($event)"
    />
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

const StandardTemplateID = 1
const InsertTemplateID = 2
const GenericInsertBlueprint = {
  "front": [{
    "type": "generic",
    "units":24,
    "children": [],
  }],
  "rear": [],
}
const TemplateData = {
  'preview': [
    {
      "id": StandardTemplateID,
      "name": "New_Template",
      "category_id": 0,
      "type": "standard",
      "ru_size": 1,
      "function": "endpoint",
      "mount_config": "2-post",
      "insert_constraints": null,
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
    },
    {
      "id": InsertTemplateID,
      "name": "New_Template",
      "category_id": 0,
      "type": "insert",
      "function": "endpoint",
      "insert_constraints": null,
			"parent_template": null,
      "blueprint": GenericInsertBlueprint
    }
  ],
  'template': []
}
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
const TemplateFaceSelected = {
  'preview': 'front',
  'template': 'front',
}
const PartitionAddressSelected = {
  'preview': {
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
  'preview': {
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
const GenericCabinet = {
  "id": StandardTemplateID,
  "size": 25,
  "name": "Cabinet",
}
const ObjectData = {
  'preview': [
    {
      "id": StandardTemplateID,
      "name": "Standard",
      "template_id": StandardTemplateID,
      "cabinet_id": 1,
      "cabinet_ru": 1,
      "cabinet_face": "front",
    },
    {
      "id": InsertTemplateID,
      "name": "Insert",
      "template_id": InsertTemplateID,
      "cabinet_id": null,
      "cabinet_ru": null,
      "cabinet_face": null,
      "parent_id": null,
      "parent_face": "front",
      "parent_part_addr": null,
      "parent_enc_addr": [0,0],
    }
  ],
  'template': [],
}
const ActivePreviewTemplateID = StandardTemplateID
const GenericObject = {
    "id": null,
    "pseudo": true,
    "name": null,
    "template_id": null,
    "cabinet_id": null,
    "cabinet_ru": null,
    "cabinet_face": null,
    "parent_id": null,
    "parent_face": null,
    "parent_part_addr": null,
    "parent_enc_addr": null,
}
const GenericTemplate = {
    "id": null,
    "pseudo": true,
    "name": "PseudoTemplate",
    "category_id": null,
    "type": null,
    "ru_size": null,
    "function": null,
    "mount_config": "2-post",
    "insert_constraints": null,
    "blueprint": {
        "front": [{
                "type": "generic",
                "units": null,
                "children": [{
                        "type": "enclosure",
                        "units": null,
                        "children": [],
                        "enc_layout": {
                            "cols": null,
                            "rows": null,
                        },
                    },
                ],
            },
        ],
        "rear": []
    },
}

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
    ModalTemplatesEdit,
  },
  data() {
    return {
      TemplateData,
      CategoryData,
      SelectedCategoryID,
      MediaData,
      PortConnectorData,
      PortOrientationData,
      GenericCabinet,
      ObjectData,
      ActivePreviewTemplateID,
      TemplateFaceSelected,
      PartitionAddressSelected,
      PartitionAddressHovered,
      StandardTemplateID,
      InsertTemplateID,
      GenericObject,
      GenericTemplate,
      GenericInsertBlueprint,
    }
  },
  computed: {
    CabinetData: function(){
      
      const vm = this
      const Context = 'preview'
      const StandardTemplateID = vm.StandardTemplateID
      const InsertTemplateID = vm.InsertTemplateID
      const GenericCabinet = vm.GenericCabinet
      const ActivePreviewTemplateID = vm.ActivePreviewTemplateID
      const ActivePreviewTemplateIndex = vm.GetTemplateIndex(ActivePreviewTemplateID, Context)
      const ActivePreviewTemplate = vm.TemplateData[Context][ActivePreviewTemplateIndex]

      const CabinetData =  JSON.parse(JSON.stringify(GenericCabinet), function(key, value) {
        if(key == 'id') {
          return (ActivePreviewTemplate.type == 'insert') ? InsertTemplateID : StandardTemplateID
        } else {
          return value
        }
      })

      return CabinetData

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
          const Blueprint = vm.TemplateData[Context][TemplateIndex].blueprint[TemplateFaceSelected]
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
    TemplatePartitionPortRange: function(){
      
      // Initialize some variables
      const vm = this
      const Context = 'template'
      const TemplateID = vm.PartitionAddressSelected[Context].template_id
      let PortRangeString = '-'

      if(TemplateID) {

        // Get template partition address
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const TemplatePartition = vm.PartitionAddressSelected[Context][TemplateFace]

        // Get template blueprint
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
        const Template = vm.TemplateData[Context][TemplateIndex]
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
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddress = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)

      return !vm.GetPartitionUnitsAvailable(PartitionParentAddress)

    },
    RemovePartitionDisabled: function() {
      
      // Store variables
      const vm = this
      const PreviewData = vm.GetPreviewData()
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddressSelected = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
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

      console.log('Debug (Partition): '+JSON.stringify(Partition))
      if(Partition.type == 'connectable') {
        console.log('Debug: here1')
        SelectedPortFormat = Partition.port_format
      }

      return SelectedPortFormat
    },
  },
  methods: {
    GetTemplateIndex: function(TemplateID, Context) {

      const vm = this
      const TemplateIndex = vm.TemplateData[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
		GetObjectIndex: function(ObjectID, Context) {

      const vm = this
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);

      return ObjectIndex
    },
    PartitionClicked: function(EmitData) {

      // Store variables
      const vm = this
      const Context = EmitData.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddress = EmitData.PartitionAddress
      const TemplateID = EmitData.TemplateID
      const ObjectID = EmitData.ObjectID
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
      const Template = vm.TemplateData[Context][TemplateIndex]
      const Blueprint = Template.blueprint[TemplateFaceSelected]
      const Partition = vm.GetPartition(Blueprint, PartitionAddress)
      const PartitionType = Partition.type
			
			// Clicked partition should not be highlighted if it is a preview insert parent
			
      const HonorClick = (vm.TemplateData[Context][TemplateIndex].hasOwnProperty('pseudo')) ? false : true

			if(HonorClick) {
				vm.PartitionAddressSelected[Context][TemplateFaceSelected] = PartitionAddress
				vm.PartitionAddressSelected[Context].template_id = TemplateID
        vm.PartitionAddressSelected[Context].object_id = ObjectID
			}

      if(HonorClick && Context == 'template' && PartitionType == 'enclosure') {

        // Get selected template data
        const TemplateFunction = Template.function
        const TemplateCategoryID = Template.category_id

        // Remove preview pseudo objects/templates
        vm.RemovePreviewPseudoData()

        // Copy selected template to insert parent template and correct id
        const InsertParentTemplateID = 'pseudo-'+(vm.TemplateData.preview.length)
        const InsertParentTemplate = JSON.parse(JSON.stringify(Template), function(key, value) {
          if(key == 'id') {
            return InsertParentTemplateID
          } else {
            return value
          }
        })
        InsertParentTemplate.pseudo = true
        InsertParentTemplate.pseudoParentTemplate = true
        vm.TemplateData.preview.push(InsertParentTemplate)

				// Generate pseudo object and constraining templates/objects if necessary
        const PseudoObjectID = vm.GeneratePseudoData(InsertParentTemplate, 'preview')

        // Get insert constraints
        const InsertConstraints = vm.GetInsertConstraints(Template, TemplateFaceSelected, PartitionAddress)

        // Adjust insert template properties
        const InsertTemplateIndex = vm.GetTemplateIndex(vm.InsertTemplateID, 'preview')
        vm.TemplateData.preview[InsertTemplateIndex].blueprint = JSON.parse(JSON.stringify(vm.GenericInsertBlueprint), function(key, value) {
          if(key == 'units') {
            return InsertConstraints.part_layout.width
          } else {
            return value
          }
        })
        vm.TemplateData.preview[InsertTemplateIndex].category_id = TemplateCategoryID
        vm.TemplateData.preview[InsertTemplateIndex].function = TemplateFunction
        vm.TemplateData.preview[InsertTemplateIndex].insert_constraints = InsertConstraints

        // Adjust insert object properties
        const InsertObjectIndex = vm.GetObjectIndex(vm.InsertTemplateID, 'preview')
        vm.ObjectData.preview[InsertObjectIndex].parent_id = PseudoObjectID
      }

    },
    GetInsertConstraints: function(Template, TemplateFace, PartitionAddress) {

      const vm = this
      const Blueprint = Template.blueprint[TemplateFace]
      const Partition = vm.GetPartition(Blueprint, PartitionAddress)
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
      let Width
      let Height
      let InsertConstraints = {
        "part_layout": {
          "height": null,
          "width": null
        },
        "enc_layout": {
          "cols": Partition.enc_layout.cols,
          "rows": Partition.enc_layout.rows
        }
      }

      if (PartitionDirection == 'col') {

        // Store height
        Height = Partition.units

        // Store width
        if (PartitionAddress.length > 1) {
          
          // Partition is deeper than 1st level, take parent partition units
          const ParentPartitionAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
          let ParentPartition = vm.GetPartition(Blueprint, ParentPartitionAddress)
          Width = ParentPartition.units

        } else {

          // Partition is 1st level and has no parent partition
          if (Template.insert_constraints !== null) {

            // Get width from constraints if they exist
            Width = Template.insert_constraints.part_layout.width
          } else {

            // Get width from template RU size
            Width = 24
          }
        }
      } else {

        // Store width
        Width = Partition.units

        // Store height
        if (PartitionAddress.length > 1) {
          
          // Partition is deeper than 1st level, take parent partition units
          const ParentPartitionAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
          let ParentPartition = vm.GetPartition(Blueprint, ParentPartitionAddress)
          Height = ParentPartition.units

        } else {

          // Partition is 1st level and has no parent partition
          if (Template.insert_constraints !== null) {

            // Get height from constraints if they exist
            Height = Template.insert_constraints.part_layout.height
          } else {

            // Get height from template RU size
            Height = Template.ru_size * 2
          }
        }
      }

      InsertConstraints.part_layout.height = Height
      InsertConstraints.part_layout.width = Width

      return InsertConstraints

    },
    PartitionHovered: function(EmitData) {

      // Store variables
      const vm = this
      const Context = EmitData.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddress = EmitData.PartitionAddress
      const HoverState = EmitData.HoverState
      const TemplateID = EmitData.TemplateID
      const ObjectID = EmitData.ObjectID
			
			// Hovered partition should not be highlighted if it is a preview insert parent
			const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
      const HonorHover = (vm.TemplateData[Context][TemplateIndex].hasOwnProperty('pseudo')) ? false : true

			if(HonorHover) {
				vm.PartitionAddressHovered[Context][TemplateFaceSelected] = (HoverState) ? PartitionAddress : false
        vm.PartitionAddressHovered[Context].object_id = ObjectID
        vm.PartitionAddressHovered[Context].template_id = TemplateID
			}

    },
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
        vm.$set(vm.TemplateData[Context], TemplateIndex, Template)

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
    TemplateNameUpdated: function(newValue) {

      const vm = this
      const PreviewData = vm.GetPreviewData()
      PreviewData.name = newValue
    },
    TemplateCategoriesUpdated: function() {
      const vm = this;

      vm.categoryGET();
    },
    TemplateCategoryUpdated: function(newValue) {

      const vm = this
      const PreviewData = vm.GetPreviewData()
      PreviewData.category_id = newValue
    },
    TemplateCategoryDeleted: function(newValue) {

      // Store data
      const vm = this;
      const CategoryID = newValue.CategoryID
      const url = '/api/categories/'+CategoryID

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

        const url = '/api/categories/'+CategoryID

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

        const url = '/api/categories'

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
			const StandardPreviewObjectIndex = vm.GetObjectIndex(vm.StandardTemplateID, 'preview')
			
			// Set active preview template index
			const ActivePreviewTemplateID = (newValue == 'insert') ? vm.InsertTemplateID : vm.StandardTemplateID
      vm.ActivePreviewTemplateID = ActivePreviewTemplateID

      // Update cabinet ID to display appropriate preview template
      vm.CabinetData.id = (newValue == 'insert') ? vm.InsertTemplateID : vm.StandardTemplateID
			
			// Reset PartitionAddressSelected
			vm.PartitionAddressSelected.preview.template_id = ActivePreviewTemplateID
			vm.PartitionAddressSelected.preview.front = [0]
			vm.PartitionAddressSelected.preview.rear = [0]
    },
    TemplateRUSizeUpdated: function(newValue) {

      const vm = this
      const PreviewData = vm.GetPreviewData()
      PreviewData.ru_size = newValue
    },
    TemplateFunctionUpdated: function(newValue) {

      const vm = this
      const PreviewData = vm.GetPreviewData()
      PreviewData.function = newValue
    },
    TemplateMountConfigUpdated: function(newValue) {

      const vm = this
      const PreviewData = vm.GetPreviewData()
      PreviewData.mount_config = newValue
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
      const PreviewData = vm.GetPreviewData()
      
      // Get Partition
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddress = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const Blueprint = PreviewData.blueprint[TemplateFaceSelected]
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
      const PreviewData = vm.GetPreviewData()
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
    TemplatePartitionPortFormatValueUpdated: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = EmitData.index
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
      const PortFormatIndex = EmitData.index
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
      const PortFormatIndex = EmitData.index
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
      const SelectedPortFormat = vm.SelectedPortFormat
      const PortFormatIndex = EmitData.index
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
      const PortFormatIndex = EmitData.index
      const SelectedPortFormat = vm.SelectedPortFormat
      const MoveDirection = EmitData.direction
      let PortFormatIndexTo
      
      // Determine new position
      if(MoveDirection == 'left') {
        PortFormatIndexTo = (PortFormatIndex == 0) ? 0 : PortFormatIndex - 1
      } else {
        PortFormatIndexTo = (PortFormatIndex == (SelectedPortFormat.length - 1)) ? PortFormatIndex : PortFormatIndex + 1
      }

      // Move field to new position
      SelectedPortFormat.splice(PortFormatIndexTo, 0, SelectedPortFormat.splice(PortFormatIndex, 1)[0])

    },
    TemplatePartitionPortFormatFieldCreate: function(EmitData) {
      
      const vm = this
      const PortFormatIndex = EmitData.index
      const SelectedPortFormat = vm.SelectedPortFormat
      const Position = EmitData.position
      const InsertPosition = (Position == 'before') ? PortFormatIndex : PortFormatIndex + 1
      const DefaultPortFormatField = {'type': 'static', 'value': 'Port', 'count': 0, 'order': 0}

      // Limit number of fields to 5
      if(SelectedPortFormat.length < 5) {

        // Insert new field
        SelectedPortFormat.splice(InsertPosition, 0, DefaultPortFormatField)
      }
    },
    TemplatePartitionPortFormatFieldDelete: function(EmitData) {
      
      const vm = this
      const Context = EmitData.context
      const PortFormatIndex = EmitData.index
      
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
            "port_format_attr": 'field',
            "port_format_value": 'delete',
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
    TemplateObjectEditClicked: function() {

      const vm = this
      vm.$bvModal.show('modal-templates-edit')

    },
    TemplateObjectCloneClicked: function() {

      const vm = this
      const TemplateContext = 'template'
      const PreviewContext = 'preview'

      // Get cloned template
      const TemplateID = vm.PartitionAddressSelected[TemplateContext].template_id
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, TemplateContext)
      const Template = vm.TemplateData[TemplateContext][TemplateIndex]

      // Set active preview template
      const PreviewTemplateID = (Template.type == 'standard') ? vm.StandardTemplateID : vm.InsertTemplateID
      vm.ActivePreviewTemplateID = PreviewTemplateID

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
      vm.$set(vm.TemplateData[PreviewContext], PreviewTemplateIndex, TemplateClone)

      if(Template.type == 'insert') {

        // Remove preview pseudo objects/templates
        vm.RemovePreviewPseudoData()

        // Create pseudo template clone parent
        const PseudoTemplateID = "pseudo-" + (vm.TemplateData[PreviewContext].length)
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
        vm.TemplateData[PreviewContext].push(TemplateCloneParent)

        // Generate pseudo object and constraining templates/objects if necessary
        const PseudoObjectID = vm.GeneratePseudoData(TemplateCloneParent, 'preview')
        vm.ObjectData.preview[PreviewTemplateIndex].parent_id = PseudoObjectID
      }
    },
    TemplateObjectDeleteClicked: function() {
      
			const vm = this
			const TemplateID = vm.PartitionAddressSelected.template.template_id
			const Context = 'template'
			const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
			const TemplateName = vm.TemplateData[Context][TemplateIndex].name
			
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
              let TemplateObjectIndex = vm.ObjectData[Context].findIndex((object) => object.template_id == TemplateID)
              let TemplateObject = vm.ObjectData[Context][TemplateObjectIndex]
              let TemplateObjectParentID = TemplateObject.parent_id

              // Delete template and object
              vm.ObjectData[Context].splice(TemplateObjectIndex,1)
              vm.TemplateData[Context].splice(TemplateIndex,1)

              // Delete parent template(s) and object(s) if necessary
              while (TemplateObjectParentID != null) {

                TemplateObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == TemplateObjectParentID)
                TemplateObject = vm.ObjectData[Context][TemplateObjectIndex]
                TemplateObjectParentID = TemplateObject.parent_id

                let TemplateObjectTemplateID = TemplateObject.template_id
                let TemplateObjectTemplateIndex = vm.GetTemplateIndex(TemplateObjectTemplateID, Context)

                vm.ObjectData[Context].splice(TemplateObjectIndex,1)
                vm.TemplateData[Context].splice(TemplateObjectTemplateIndex,1)
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
	  GetPartitionDirection: function(PartitionAddress) {

      const PartitionDirection = (PartitionAddress.length % 2) ? 'row' : 'col'

      return PartitionDirection
    },
    GetPartitionUnitsAvailable: function(PartAddr = false) {

      // Store variables
      const vm = this
      const PreviewData = vm.GetPreviewData()

      // Get Partition
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartitionAddress = (PartAddr !== false) ? PartAddr : vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const Blueprint = PreviewData.blueprint[TemplateFaceSelected]
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
      const TemplateFaceSelected = vm.TemplateFaceSelected.preview
      const PartAddr = vm.PartitionAddressSelected.preview[TemplateFaceSelected]
      const Blueprint = PreviewData.blueprint[TemplateFaceSelected]

      let SelectedPartition = vm.GetPartition(Blueprint, PartAddr)
      
      return SelectedPartition
    },
    GetPartition: function(Blueprint, PartitionAddress) {
			
			// Locate template partition
			let Partition = Blueprint;
			let PartitionCollection = Blueprint
			PartitionAddress.forEach(function(PartitionIndex) {
				if(typeof PartitionCollection[PartitionIndex] !== 'undefined') {
					Partition = PartitionCollection[PartitionIndex]
					PartitionCollection = PartitionCollection[PartitionIndex]['children']
				} else {
					return false
				}
			})
			
			return Partition
      
    },
    GetPreviewData: function() {

      // Initial variables
      const vm = this

      // Get template index
      const TemplateIndex = vm.GetTemplateIndex(vm.ActivePreviewTemplateID, 'preview')

      // Get template
      const ObjectPreviewData = vm.TemplateData.preview[TemplateIndex]

      // Return template
      return ObjectPreviewData
    },
    RemovePreviewPseudoData: function() {

      const vm = this

      vm.ObjectData.preview = vm.ObjectData.preview.filter(function(Object){
        return !Object.hasOwnProperty('pseudo')
      })

      vm.TemplateData.preview = vm.TemplateData.preview.filter(function(Template){
        return !Template.hasOwnProperty('pseudo')
      })

    },
	  GeneratePseudoData: function (Template, Context) {

      const vm = this
      let WorkingObjectData = []
      let WorkingTemplateData = []
      let PseudoObjectParentID = null
      let PseudoObjectParentFace = null
      let PseudoObjectParentPartitionAddress = null
      let PseudoObjectParentEnclosureAddress = null
      const TemplateID = Template.id
      const TemplateType = Template.type

      if (TemplateType == 'insert') {
        
        PseudoObjectParentFace = 'front'
        PseudoObjectParentPartitionAddress = [0, 0]
        PseudoObjectParentEnclosureAddress = [0, 0]
        const InsertConstraints = Template.insert_constraints

        // Generate pseudo IDs
        const PseudoTemplateID = "pseudo-" + (vm.TemplateData[Context].length + WorkingTemplateData.length)
        const PseudoObjectID = "pseudo-" + (vm.ObjectData[Context].length + WorkingObjectData.length)

        // Create pseudo object
        WorkingObjectData.push(JSON.parse(JSON.stringify(vm.GenericObject), function (GenericObjectKey, GenericObjectValue) {
          if (GenericObjectKey == 'id') {
            return PseudoObjectID
          } else if (GenericObjectKey == 'cabinet_face') {
            return 'front'
          } else if (GenericObjectKey == 'cabinet_id') {
            return (Context == 'preview') ? vm.InsertTemplateID : null
          } else if (GenericObjectKey == 'cabinet_ru') {
            return 1
          } else if (GenericObjectKey == 'template_id') {
            return PseudoTemplateID
          } else if (GenericObjectKey == 'parent_id') {
            return PseudoObjectParentID
          } else {
            return GenericObjectValue
          }
        }))

        // Create pseudo template
        WorkingTemplateData.push(JSON.parse(JSON.stringify(vm.GenericTemplate), function (GenericTemplateKey, GenericTemplateValue) {
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
        }))

        PseudoObjectParentID = PseudoObjectID
      }

      // Create pseudo object for template
      const PseudoObjectID = "pseudo-" + (vm.ObjectData[Context].length + WorkingObjectData.length)
      WorkingObjectData.push(JSON.parse(JSON.stringify(vm.GenericObject), function (GenericObjectKey, GenericObjectValue) {
        if (GenericObjectKey == 'id') {
            return PseudoObjectID
        } else if (GenericObjectKey == 'cabinet_id') {
            return (Context == 'preview' && TemplateType == 'standard') ? vm.InsertTemplateID : GenericObjectValue
        } else if (GenericObjectKey == 'cabinet_face') {
            return (Context == 'preview' && TemplateType == 'standard') ? 'front' : GenericObjectValue
        } else if (GenericObjectKey == 'cabinet_ru') {
            return (Context == 'preview' && TemplateType == 'standard') ? 1 : GenericObjectValue
        } else if (GenericObjectKey == 'parent_id') {
            return PseudoObjectParentID
        } else if (GenericObjectKey == 'parent_face') {
            return PseudoObjectParentFace
        } else if (GenericObjectKey == 'parent_part_addr') {
            return PseudoObjectParentPartitionAddress
        } else if (GenericObjectKey == 'parent_enc_addr') {
            return PseudoObjectParentEnclosureAddress
        } else if (GenericObjectKey == 'template_id') {
            return TemplateID
        } else {
            return GenericObjectValue
        }
      }))

      vm.TemplateData[Context] = vm.TemplateData[Context].concat(WorkingTemplateData)
      vm.ObjectData[Context] = vm.ObjectData[Context].concat(WorkingObjectData)

      return PseudoObjectID
    },
    templatesGET: function () {

      const vm = this;
			const Context = 'template'
      
      this.$http.get('/api/templates').then(function(response){

        vm.TemplateData.template = response.data

        response.data.forEach(function(Template, TemplateIndex){
					
					vm.GeneratePseudoData(Template, Context)
        })
      });
    },
    categoryGET: function(SetCategoryToDefault = false) {

      const vm = this
      const StandardTemplateIndex = vm.GetTemplateIndex(vm.StandardTemplateID, 'preview')
      const InsertTemplateIndex = vm.GetTemplateIndex(vm.InsertTemplateID, 'preview')

      this.$http.get('/api/categories').then(function(response){

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


          vm.TemplateData.preview[StandardTemplateIndex].category_id = DefaultCategoryID
          vm.TemplateData.preview[InsertTemplateIndex].category_id = DefaultCategoryID
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
      const vm = this
      const PreviewData = vm.GetPreviewData()
      const url = '/api/templates'
      const data = PreviewData

      // POST category form data
      this.$http.post(url, data).then(function(response){
        
        const Template = response.data
				
        // Append new template to template array
        vm.TemplateData.template.push(Template)

        vm.GeneratePseudoData(Template, 'template')

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
      const PreviewData = vm.GetPreviewData()

      console.log('Form Reset')
    },
  },
  mounted() {

    const vm = this
    const SetCategoryToDefault = true

    vm.categoryGET(SetCategoryToDefault)
    vm.templatesGET()
    vm.mediumGET()
    vm.portOrientationGET()
  },
}
</script>

<style>

</style>

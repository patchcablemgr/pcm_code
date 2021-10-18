<template>

  <drag
    :transfer-data="{ object_id: ObjectID, template_id: GetTemplateID(ObjectID), template_face: TemplateFaceSelected[Context], context: Context }"
    :class="{
      pcm_template_partition_selected: PartitionIsSelected(),
      pcm_template_partition_hovered: PartitionIsHovered(),
    }"
    :style="{
      'background-color': TemplateColor(ObjectID),
      'height': '100%',
    }"
    @click.stop=" $emit('PartitionClicked', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress}) "
    @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress, 'HoverState': true}) "
    @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress, 'HoverState': false}) "
  >
    <component-template
      :ObjectData="ObjectData"
      :TemplateData="TemplateData"
      :CategoryData="CategoryData"
      :TemplateRUSize="TemplateRUSize"
      :InitialPartitionAddress=" InitialPartitionAddress "
      :Context="Context"
      :ObjectID="ObjectID"
      :TemplateFaceSelected="TemplateFaceSelected"
      :PartitionAddressSelected="PartitionAddressSelected"
      :PartitionAddressHovered="PartitionAddressHovered"
      @PartitionClicked=" $emit('PartitionClicked', $event) "
      @PartitionHovered=" $emit('PartitionHovered', $event) "
    />
  </drag>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import ComponentTemplate from './ComponentTemplate.vue'
import { Drag, Drop } from 'vue-drag-drop'

export default {
  components: {
    BContainer,
    BRow,
    BCol,

    ComponentTemplate,
    Drag,
    Drop,
  },
  props: {
    ObjectData: {type: Object},
    TemplateData: {type: Object},
    CategoryData: {type: Array},
    TemplateRUSize: {type: Number},
    InitialPartitionAddress: {type: Array},
    Context: {type: String},
    ObjectID: {},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  methods: {
    GetTemplateID: function(ObjectID) {
      
      const vm = this
      const Context = vm.Context
      const ObjectData = vm.ObjectData[Context]
      let TemplateID = 0

      const ObjectIndex = ObjectData.findIndex((object) => object.id == ObjectID )

      if(ObjectIndex !== -1) {
        const Object = ObjectData[ObjectIndex]
        TemplateID = Object.template_id
      }

      return TemplateID

    },
    RackObjectSize: function(ObjectID) {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);

      // Get template index
      const TemplateID = vm.ObjectData[Context][ObjectIndex].template_id
      const TemplateIndex = TemplateData[Context].findIndex((template) => template.id == TemplateID);

      // Get template
      const ObjectPreviewData = TemplateData[Context][TemplateIndex]

      const ObjectSize = ObjectPreviewData.ru_size

      return ObjectSize
    },
    GetTemplateIndex: function(TemplateID) {

      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get object index
      const TemplateIndex = TemplateData[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    GetObjectIndex: function(ObjectID) {

      // Initial variables
      const vm = this
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);

      return ObjectIndex
    },
    GetPreviewData: function(ObjectID) {

      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.GetObjectIndex(ObjectID)

      // Get template index
      const TemplateID = vm.ObjectData[Context][ObjectIndex].template_id
      const TemplateIndex = vm.GetTemplateIndex(TemplateID)

      // Get template
      const ObjectPreviewData = TemplateData[Context][TemplateIndex]

      // Return template
      return ObjectPreviewData
    },
    TemplateColor: function(ObjectID) {

      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context
      let TemplateColor

      // Get Template
      const TemplateID = vm.GetTemplateID(ObjectID)
      const TemplateIndex = vm.GetTemplateIndex(TemplateID)
      const Template = TemplateData[Context][TemplateIndex]

      if (Template.hasOwnProperty('pseudo') && !Template.hasOwnProperty('pseudoParentTemplate')) {
        TemplateColor = '#FFFFFF00'
      } else {

        // Get category index
        const ObjectCategoryID = Template.category_id
        const ObjectCategoryIndex = vm.CategoryData.findIndex((category) => category.id == ObjectCategoryID);

        // Get category
        const ObjectCategoryData = vm.CategoryData[ObjectCategoryIndex]

        TemplateColor = ObjectCategoryData.color
      }

      // Return category
      return TemplateColor
    },
    PartitionIsSelected: function() {

      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressSelected = vm.PartitionAddressSelected[Context][TemplateFaceSelected]
      const PartitionAddress = vm.InitialPartitionAddress
      const TemplateIDSelected = vm.PartitionAddressSelected[Context].template_id
      const TemplateID = vm.GetTemplateID(vm.ObjectID)
      let PartitionIsSelected = false

      if(PartitionAddressSelected.length === PartitionAddress.length && PartitionAddressSelected.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsSelected = true
      }
      return PartitionIsSelected
    },
    PartitionIsHovered: function() {

      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressHovered = vm.PartitionAddressHovered[Context][TemplateFaceSelected]
      const PartitionAddress = vm.InitialPartitionAddress
      const TemplateIDSelected = vm.PartitionAddressHovered[Context].template_id
      const TemplateID = vm.GetTemplateID(vm.ObjectID)
      let PartitionIsHovered = false

      if(PartitionAddressHovered.length === PartitionAddress.length && PartitionAddressHovered.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsHovered = true
      }
      return PartitionIsHovered
    },
    GetPartitionGrid: function(units) {

      let GridArray = []
      for(let i=0; i<units; i++) {
        GridArray.push('1fr')
      }

      return GridArray.join(' ')
    },
    GetPartitionAreas: function(rows, cols) {

      let AreasArray = []
      let AreaCounter = 0
      for(let r=0; r<rows; r++) {

        AreasArray.push([])
        for(let c=0; c<cols; c++) {
          
          AreasArray[r].push([])
          AreasArray[r][c] = 'area'+AreaCounter
          AreaCounter++
        }
      }
      
      const AreasString = "'" + AreasArray.map(arr => arr.join(' ')).join("' '") + "'";

      return AreasString
    },
  },
}
</script>
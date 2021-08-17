<template>

  <div
    :class="{
      pcm_template_partition_selected: PartitionAddressSelected[Context][TemplateFaceSelected[Context]].length === 0,
      pcm_template_partition_hovered: PartitionAddressHovered[Context][TemplateFaceSelected[Context]].length === 0,
    }"
    :style="{
      'background-color': ObjectCategoryData( ObjectID ).color,
      'height': '100%',
    }"
    @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'PartitionAddress': InitialDepthCounter, 'HoverState': true}) "
    @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'PartitionAddress': InitialDepthCounter, 'HoverState': false}) "
  >
    <component-template
      :ObjectData="ObjectData"
      :TemplateBlueprint=" GetPreviewData( ObjectID ).blueprint[TemplateFaceSelected[Context]] "
      :TemplateBlueprintOriginal=" GetPreviewData( ObjectID ).blueprint[TemplateFaceSelected[Context]] "
      :TemplateRUSize=" RackObjectSize( ObjectID ) "
      :InitialDepthCounter=" InitialDepthCounter "
      :Context="Context"
      :ObjectID="ObjectID"
      :TemplateID="parseInt(0)"
      :TemplateFaceSelected="TemplateFaceSelected"
      :PartitionAddressSelected="PartitionAddressSelected"
      :PartitionAddressHovered="PartitionAddressHovered"
      @PartitionClicked=" $emit('PartitionClicked', $event) "
      @PartitionHovered=" $emit('PartitionHovered', $event) "
    />
  </div>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import ComponentTemplate from './ComponentTemplate.vue'

export default {
  components: {
    BContainer,
    BRow,
    BCol,

    ComponentTemplate,
  },
  props: {
    TemplateData: {type: Object},
    CabinetID: {type: Number},
    CabinetRU: {type: Number},
    ObjectData: {type: Object},
    PreviewData: {type: Array},
    CategoryData: {type: Array},
    TemplateRUSize: {type: Number},
    InitialDepthCounter: {type: Array},
    Context: {type: String},
    ObjectID: {type: Number},
    TemplateID: {type: Number},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  methods: {
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
    ObjectCategoryData: function(ObjectID) {

      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID)

      // Get template index
      const TemplateID = vm.ObjectData[Context][ObjectIndex].template_id
      const TemplateIndex = TemplateData[Context].findIndex((template) => template.id == TemplateID)

      // Get category index
      const ObjectCategoryID = TemplateData[Context][TemplateIndex].category_id
      const ObjectCategoryIndex = vm.CategoryData.findIndex((category) => category.id == ObjectCategoryID);

      // Get category
      const ObjectCategoryData = vm.CategoryData[ObjectCategoryIndex]

      // Return category
      return ObjectCategoryData
    },
    PartitionIsSelected: function(PartitionIndex) {
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressSelected = vm.PartitionAddressSelected[Context][TemplateFaceSelected]
      const PartitionAddress = vm.GetDepthCounter(PartitionIndex)
      const TemplateIDSelected = vm.PartitionAddressSelected[Context].template_id
      const TemplateID = vm.TemplateID
      let PartitionIsSelected = false

      if(PartitionAddressSelected.length === PartitionAddress.length && PartitionAddressSelected.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsSelected = true
      }
      return PartitionIsSelected
    },
    PartitionIsHovered: function(PartitionIndex) {
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressHovered = vm.PartitionAddressHovered[Context][TemplateFaceSelected]
      const PartitionAddress = vm.GetDepthCounter(PartitionIndex)
      const TemplateIDSelected = vm.PartitionAddressHovered[Context].template_id
      const TemplateID = vm.TemplateID
      let PartitionIsHovered = false

      if(PartitionAddressHovered.length === PartitionAddress.length && PartitionAddressHovered.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsHovered = true
      }
      return PartitionIsHovered
    },
    GetDepthCounter: function(PartitionIndex) {

      // Store variables
      const vm = this;
      return vm.InitialDepthCounter.concat([PartitionIndex])
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
    GetSelectedPartitionDirection: function() {

      const vm = this;
      const PartitionAddress = vm.InitialDepthCounter
      let PartitionDirection

      PartitionDirection = (PartitionAddress.length % 2) ? 'column' : 'row'
      
      return PartitionDirection
    },
    PartitionDirectionClass: function() {

      const vm = this;
      const PartitionAddress = vm.InitialDepthCounter

      return (PartitionAddress.length % 2) ? 'pcm_template_partition_horizontal' : 'pcm_template_partition_vertical'
    },
  },
}
</script>
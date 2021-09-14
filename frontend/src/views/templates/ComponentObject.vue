<template>

  <div
    :class="{
      pcm_template_partition_selected: PartitionIsSelected(),
      pcm_template_partition_hovered: PartitionIsHovered(),
    }"
    :style="{
      'background-color': ObjectCategoryData( ObjectID ).color,
      'height': '100%',
    }"
    @click.stop=" $emit('PartitionClicked', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialDepthCounter}) "
    @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialDepthCounter, 'HoverState': true}) "
    @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialDepthCounter, 'HoverState': false}) "
  >
    <component-template
      :ObjectData="ObjectData"
      :TemplateData="TemplateData"
      :CategoryData="CategoryData"
      :TemplateRUSize="TemplateRUSize"
      :InitialDepthCounter=" InitialDepthCounter "
      :Context="Context"
      :ObjectID="ObjectID"
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
    ObjectData: {type: Object},
    TemplateData: {type: Object},
    CategoryData: {type: Array},
    TemplateRUSize: {type: Number},
    InitialDepthCounter: {type: Array},
    Context: {type: String},
    ObjectID: {},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  methods: {
    GetTemplateID: function(ObjectID) {
      console.log('Debug (ComponentObject): GetTemplateID')
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
      console.log('Debug (ComponentObject): RackObjectSize')
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
console.log('Debug (ComponentObject): GetTemplateIndex')
      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get object index
      const TemplateIndex = TemplateData[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    GetObjectIndex: function(ObjectID) {
console.log('Debug (ComponentObject): GetObjectIndex')
      // Initial variables
      const vm = this
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);

      return ObjectIndex
    },
    GetPreviewData: function(ObjectID) {
console.log('Debug (ComponentObject): GetPreviewData')
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
console.log('Debug (ComponentObject): ObjectCategoryData')
console.log('Debug (ComponentObject-ObjectCategoryData): '+ObjectID)
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
    PartitionIsSelected: function() {
console.log('Debug (ComponentObject): PartitionIsSelected')
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressSelected = vm.PartitionAddressSelected[Context][TemplateFaceSelected]
      const PartitionAddress = vm.InitialDepthCounter
      const TemplateIDSelected = vm.PartitionAddressSelected[Context].template_id
      const TemplateID = vm.GetTemplateID(vm.ObjectID)
      let PartitionIsSelected = false

      if(PartitionAddressSelected.length === PartitionAddress.length && PartitionAddressSelected.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsSelected = true
      }
      return PartitionIsSelected
    },
    PartitionIsHovered: function() {
console.log('Debug (ComponentObject): PartitionIsHovered')
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressHovered = vm.PartitionAddressHovered[Context][TemplateFaceSelected]
      const PartitionAddress = vm.InitialDepthCounter
      const TemplateIDSelected = vm.PartitionAddressHovered[Context].template_id
      const TemplateID = vm.GetTemplateID(vm.ObjectID)
      let PartitionIsHovered = false

      if(PartitionAddressHovered.length === PartitionAddress.length && PartitionAddressHovered.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsHovered = true
      }
      return PartitionIsHovered
    },
    GetDepthCounter: function(PartitionIndex) {
console.log('Debug (ComponentObject): GetDepthCounter')
      // Store variables
      const vm = this
      return vm.InitialDepthCounter.concat([PartitionIndex])
    },
    GetPartitionGrid: function(units) {
console.log('Debug (ComponentObject): GetPartitionGrid')
      let GridArray = []
      for(let i=0; i<units; i++) {
        GridArray.push('1fr')
      }

      return GridArray.join(' ')
    },
    GetPartitionAreas: function(rows, cols) {
console.log('Debug (ComponentObject): GetPartitionAreas')
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
console.log('Debug (ComponentObject): GetSelectedPartitionDirection')
      const vm = this;
      const PartitionAddress = vm.InitialDepthCounter
      let PartitionDirection

      PartitionDirection = (PartitionAddress.length % 2) ? 'column' : 'row'
      
      return PartitionDirection
    },
    PartitionDirectionClass: function() {
console.log('Debug (ComponentObject): PartitionDirectionClass')
      const vm = this
      const PartitionAddress = vm.InitialDepthCounter

      return (PartitionAddress.length % 2) ? 'pcm_template_partition_horizontal' : 'pcm_template_partition_vertical'
    },
  },
  mounted() {

    const vm = this
    console.log('ComponentObject '+vm.Context)
  }
}
</script>
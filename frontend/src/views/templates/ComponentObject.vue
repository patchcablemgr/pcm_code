<template>

  <div
    class="pcm_cabinet_object_container pcm_template_partition_layered"
    :class="PartitionDirectionClass()"
  >
    
    <div
      v-for="(Partition, PartitionIndex) in TemplateBlueprint"
      :key=" GetDepthCounter(PartitionIndex).join('-') "
      class=" pcm_template_partition_box "
      :class="{
        pcm_template_partition_selected: PartitionIsSelected(PartitionIndex),
        pcm_template_partition_hovered: PartitionIsHovered(PartitionIndex),
      }"
      :style="{ 'flex-grow': GetPartitionFlexGrow(Partition.units, PartitionIndex) }"
      :DepthCounter=" GetDepthCounter(PartitionIndex) "
      @click.stop=" $emit('PartitionClicked', {'Context': Context, 'TemplateID': TemplateID, 'PartitionAddress': GetDepthCounter(PartitionIndex)}) "
      @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': TemplateID, 'PartitionAddress': GetDepthCounter(PartitionIndex), 'HoverState': true}) "
      @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': TemplateID, 'PartitionAddress': GetDepthCounter(PartitionIndex), 'HoverState': false}) "
    >
      <!-- Generic partition -->
      <Object
        v-if=" Partition.type == 'generic' "
        :TemplateBlueprint="Partition.children"
        :TemplateBlueprintOriginal="TemplateBlueprintOriginal"
        :TemplateRUSize="TemplateRUSize"
        :InitialDepthCounter="GetDepthCounter(PartitionIndex)"
        :Context="Context"
        :TemplateFaceSelected="TemplateFaceSelected"
        :PartitionAddressSelected="PartitionAddressSelected"
        :PartitionAddressHovered="PartitionAddressHovered"
        :TemplateID="TemplateID"
        @PartitionClicked=" $emit('PartitionClicked', $event) "
        @PartitionHovered=" $emit('PartitionHovered', $event) "
      />

      <!-- Connectable partition -->
      <div
        v-if=" Partition.type == 'connectable' "
        class=" pcm_template_connectable_container "
        :style="{
          'grid-template-rows': GetPartitionGrid(Partition.port_layout.rows),
          'grid-template-columns': GetPartitionGrid(Partition.port_layout.cols),
          'grid-template-areas': GetPartitionAreas(Partition.port_layout.rows, Partition.port_layout.cols),
        }"
      >
        <div
          v-for=" portIndex in (Partition.port_layout.rows * Partition.port_layout.cols) "
          :key=" portIndex "
          class=" pcm_template_connectable_port_unk "
          :style="{ 'grid-area': 'area'+(portIndex-1) }"
        >
        </div>
      </div>

      <!-- Enclosure partition -->
      <div
        v-if=" Partition.type == 'enclosure' "
        class=" pcm_template_enclosure_container "
        :style="{
          'grid-template-rows': GetPartitionGrid(Partition.enc_layout.rows),
          'grid-template-columns': GetPartitionGrid(Partition.enc_layout.cols),
          'grid-template-areas': GetPartitionAreas(Partition.enc_layout.rows, Partition.enc_layout.cols),
        }"
      >
        <div
          v-for=" encIndex in (Partition.enc_layout.rows * Partition.enc_layout.cols) "
          :key=" encIndex "
          class=" pcm_template_enclosure_area "
          :style="{ 'grid-area': 'area'+(encIndex-1) }"
        >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'

export default {
  name: "Object",
  components: {
    BContainer,
    BRow,
    BCol,
  },
  props: {
    TemplateBlueprint: {type: Array},
    TemplateBlueprintOriginal: {type: Array},
    TemplateRUSize: {type: Number},
    InitialDepthCounter: {type: Array},
    Context: {type: String},
    TemplateID: {type: Number},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  methods: {
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
    GetPartitionParentSize: function(PartitionAddress) {

      // Store variables
      const vm = this
      const PartitionDirection = (PartitionAddress.length % 2) ? 'column' : 'row'
      let WorkingMax = (PartitionDirection == 'column') ? 24 : vm.TemplateRUSize * 2
      let WorkingPartitionChildren = JSON.parse(JSON.stringify(vm.TemplateBlueprintOriginal))
      
      PartitionAddress.pop()
      PartitionAddress.forEach(function(PartitionAddressIndex, Depth){
          let WorkingPartitionDirection = ((Depth + 1) % 2) ? 'column' : 'row'
          if(WorkingPartitionDirection == PartitionDirection) {
            WorkingMax = WorkingPartitionChildren[PartitionAddressIndex].units
          }
          WorkingPartitionChildren = WorkingPartitionChildren[PartitionAddressIndex].children
      })

      return WorkingMax
    },
    GetPartitionFlexGrow: function(PartitionUnits, PartitionIndex) {

      const vm = this;
      let PartitionFlexGrow
      const PartitionAddress = vm.GetDepthCounter(PartitionIndex)
      const PartitionParentSize = vm.GetPartitionParentSize(PartitionAddress)

      PartitionFlexGrow = PartitionUnits / PartitionParentSize

      return PartitionFlexGrow
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
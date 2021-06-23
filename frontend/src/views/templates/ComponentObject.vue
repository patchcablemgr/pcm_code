<template>

  <div
    class="pcm_cabinet_object_container"
    :class="PartitionDirectionClass()"
  >
    
    <div
      v-for="(Partition, PartitionIndex) in TemplateBlueprint"
      :key=" GetDepthCounter(PartitionIndex) "
      class=" pcm_template_partition_box "
      :class="{ pcm_template_partition_selected: PartitionIsSelected(PartitionIndex) }"
      :style="{ 'flex-grow': GetPartitionFlexGrow(Partition.units) }"
      :DepthCounter=" GetDepthCounter(PartitionIndex) "
      @click.stop=" $emit('PartitionClicked', GetDepthCounter(PartitionIndex)) "
    >
      <!-- Generic partition -->
      <Object
        v-if=" Partition.type == 'generic' "
        :TemplateBlueprint="Partition.children"
        :TemplateRUSize="TemplateRUSize"
        :InitialDepthCounter="GetDepthCounter(PartitionIndex)"
        :SelectedPartitionAddress="SelectedPartitionAddress"
        :CabinetFace="CabinetFace"
        @PartitionClicked=" $emit('PartitionClicked', $event) "
      />

      <!-- Connectable partition -->
      <div
        v-if=" Partition.type == 'connectable' "
        class=" pcm_template_connectable_container "
        :style="{
          'grid-template-rows': GetConnectablePartitionGrid(Partition.port_layout.rows),
          'grid-template-columns': GetConnectablePartitionGrid(Partition.port_layout.cols),
          'grid-template-areas': GetConnectablePartitionAreas(Partition.port_layout.rows, Partition.port_layout.cols),
        }"

      >
        <div
          v-for=" portIndex in (Partition.port_layout.rows * Partition.port_layout.cols) "
          :key=" portIndex "
          class=" pcm_template_connectable_port_unk "
          :style="{ 'grid-area': 'port'+(portIndex-1) }"
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
    TemplateRUSize: {type: Number},
    InitialDepthCounter: {type: String},
    SelectedPartitionAddress: {type: Object},
    CabinetFace: {type: String},
  },
  methods: {
    PartitionIsSelected: function(PartitionIndex) {
      const vm = this
      const PartitionIsSelected = vm.SelectedPartitionAddress[vm.CabinetFace] === vm.GetDepthCounter(PartitionIndex)

      return PartitionIsSelected
    },
    GetDepthCounter: function(PartitionIndex) {

      // Store variables
      const vm = this;
      const InitialPartitionAddress = this.InitialDepthCounter
      let PartitionAddressArray = []

      // Split partition address if defined
      if(InitialPartitionAddress.length) {
        PartitionAddressArray = InitialPartitionAddress.split('-')
      }

      // Append current partition index
      PartitionAddressArray.push(PartitionIndex)

      // Return partition address string
      return PartitionAddressArray.join('-')
    },
    GetConnectablePartitionGrid: function(units) {

      let GridArray = []
      for(let i=0; i<units; i++) {
        GridArray.push('1fr')
      }

      return GridArray.join(' ')
    },
    GetConnectablePartitionAreas: function(rows, cols) {

      let AreasArray = []
      let AreaCounter = 0
      for(let r=0; r<rows; r++) {

        AreasArray.push([])
        for(let c=0; c<cols; c++) {
          
          AreasArray[r].push([])
          AreasArray[r][c] = 'port'+AreaCounter
          AreaCounter++
        }
      }
      console.log(AreasArray)
      const AreasString = "'" + AreasArray.map(arr => arr.join(' ')).join("' '") + "'";
      console.log(AreasString)

      return AreasString
    },
    GetPartitionFlexGrow: function(PartitionUnits) {

      const vm = this;
      const PartitionDirection = vm.PartitionDirection()
      const TemplateRUSize = vm.TemplateRUSize
      let PartitionFlexGrow

      if(PartitionDirection) {
        PartitionFlexGrow = PartitionUnits / (TemplateRUSize * 2)
      } else {
        PartitionFlexGrow = PartitionUnits / 24
      }

      return PartitionFlexGrow
    },
    PartitionDirection: function() {

      const vm = this;
      const InitialPartitionAddress = vm.InitialDepthCounter

      return InitialPartitionAddress.replace('-','').length % 2
    },
    PartitionDirectionClass: function() {

      const vm = this;
      const PartitionDirection = vm.PartitionDirection()

      return PartitionDirection ? 'pcm_template_partition_horizontal' : 'pcm_template_partition_vertical'
    },
  },
}
</script>
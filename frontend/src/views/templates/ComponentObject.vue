<template>

  <div
    class="pcm_cabinet_object_container"
    :class="PartitionDirectionClass()"
  >
    
    <div
      v-for="(Partition, PartitionIndex) in TemplateBlueprint"
      :key="PartitionIndex"
      class="pcm_template_partition_box"
      :class="{ pcm_template_partition_selected: PartitionIsSelected(PartitionIndex) }"
      :style="{ 'flex-grow': GetPartitionFlexGrow(Partition.units) }"
      :DepthCounter="GetDepthCounter(PartitionIndex)"
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
        class="b-container"
      >
        <div
          v-for=" (PortLayoutRow) in range(Partition.port_layout.rows) "
          :key=" PortLayoutRow "
          class="b-row"
        >
          <div
            v-for=" (PortLayoutCol) in range(Partition.port_layout.cols) "
            :key=" PortLayoutCol "
            class="b-col"
          >
          </div>
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
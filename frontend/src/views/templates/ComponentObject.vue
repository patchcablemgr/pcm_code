<template>
  <div
    class="pcm_cabinet_object_container"
    :class="PartitionDirectionClass()"
  >
    <!-- Template categories modal -->
    <div
      v-for="(Partition, PartitionIndex) in TemplateBlueprint"
      :key="PartitionIndex"
      class="pcm_template_partition_box"
      :style="{ 'flex-grow': GetPartitionFlexGrow(Partition.units) }"
      :DepthCounter="GetDepthCounter(PartitionIndex)"
    >
      <Object
        :TemplateBlueprint="Partition.children"
        :TemplateRUSize="TemplateRUSize"
        :InitialDepthCounter="GetDepthCounter(PartitionIndex)"
      />
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
  },
  methods: {
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
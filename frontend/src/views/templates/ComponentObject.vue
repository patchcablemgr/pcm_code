<template>
  <div class="pcm_cabinet_object_container">
    <!-- Template categories modal -->
    <div
      v-for="(Partition, PartitionIndex) in TemplateBlueprint"
      :key="PartitionIndex"
      class="pcm_debug_box"
      :DepthCounter="DepthCounter"
    >
      <Object
        :TemplateBlueprint="Partition.children"
        :InitialDepthCounter="DepthCounterProp"
        v-on:IncrementPartitionDepth="IncrementPartitionDepth()"
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
    InitialDepthCounter: {type: Number},
  },
  data() {
    return {
    }
  },
  methods: {
    IncrementPartitionDepth: function() {
      this.DepthCounterProp++
      this.$emit('IncrementPartitionDepth')
    },
  },
  created() {
    this.DepthCounter = this.InitialDepthCounter
    this.DepthCounterProp = this.InitialDepthCounter
    this.$emit('IncrementPartitionDepth')
  },
  mounted() {
    //this.$emit('IncrementPartitionDepth')
  },
}
</script>
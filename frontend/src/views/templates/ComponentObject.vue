<template>
  <div>
    <!-- Template categories modal -->
    <!--
    <div
      v-for="Partition in PartitionList"
      :key="Partition"
      :PartitionList="Partition.children"
    >
      {{ Partition.type }}
    </div>
    -->
    {{ RackObjectIndex }}
    {{ RackObject.id }}
  </div>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'

export default {
  components: {
    BContainer,
    BRow,
    BCol,
  },
  props: {
    TemplateData: {type: Array},
    ObjectData: {type: Array},
    RackObjectIndex: {type: Number},
    TemplateIndex: {type: Number},
  },
  data() {
    return {
      RackObject: ObjectData[this.RackObjectIndex]
    }
  },
  mounted() {
    console.log(this.RackObjectIndex);
  }
}
</script>
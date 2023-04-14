<!-- Template/Object Details -->

<template>
<div
  :title="GeneratePortName(Context, ObjectID, ObjectFace, PartitionAddress, PortID)"
  :class="{
    pcm_template_port_selected: PortSelected,
    pcm_template_port_hovered: PortHovered,
  }"
  style="display:flex"
  @click.stop=" PartitionClicked({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': PartitionAddress, 'PortID': PortID}) "
  @mouseover.stop="PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': PartitionAddress, 'PortID': PortID, 'HoverState': true}) "
  @mouseleave.stop="PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': PartitionAddress, 'PortID': PortID, 'HoverState': false}) "
>

<svg xmlns="http://www.w3.org/2000/svg"
  width="8"
  height="8">
  <rect
    width="100%"
    height="100%"
    :style="{'fill':PortDisposition}"
  />
</svg>
</div>
</template>

<script>
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
  },
	directives: {
	},
  props: {
    Context: {},
    ObjectID: {},
    ObjectFace: {},
    PartitionAddress: {},
    PortID: {},
    PortSelected: {type: Boolean},
    PortHovered: {type: Boolean},
  },
  data() {
    return {
    }
  },
  computed: {
    PortDisposition: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.ObjectID
        const ObjectFace = vm.ObjectFace
        const ObjectPartition = vm.PartitionAddress
        const PortID = vm.PortID

        return vm.GetPortDisposition(Context, ObjectID, ObjectFace, ObjectPartition, PortID)
      },
      set() {
        return true
      }
    },
  },
  methods: {
  }
}
</script>
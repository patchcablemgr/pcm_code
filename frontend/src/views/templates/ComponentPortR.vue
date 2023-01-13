<!-- Template/Object Details -->

<template>
<div
  :title="GeneratePortName(Context, ObjectID, ObjectFace, PartitionAddress, PortID)"
  :class="{
    pcm_template_port_selected: PartitionIsSelected({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': PartitionAddress, 'PortID': PortID}),
    pcm_template_port_hovered: PartitionIsHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': PartitionAddress, 'PortID': PortID}),
  }"
  style="display:flex"
  @click.stop=" PartitionClicked({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': PartitionAddress, 'PortID': PortID}) "
  @mouseover.stop=" PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': PartitionAddress, 'PortID': PortID, 'HoverState': true}) "
  @mouseleave.stop=" PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': PartitionAddress, 'PortID': PortID, 'HoverState': false}) "
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
    TemplateID: {},
    PartitionAddress: {},
    PortID: {},
  },
  data() {
    return {
    }
  },
  computed: {
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    Media() {
      return this.$store.state.pcmProps.Media
    },
    Connectors() {
      return this.$store.state.pcmProps.Connectors
    },
    Orientations() {
      return this.$store.state.pcmProps.Orientations
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Trunks() {
      return this.$store.state.pcmTrunks.Trunks
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    StateHovered() {
      return this.$store.state.pcmState.Hovered
    },
    PortIsSelected: {
      get() {
        return (this.SelectedPortIndex !== null) ? true : false
      }
    },
    SelectedPortIndex: {
      get() {

        const vm = this
        const Context = vm.Context
        const Face = vm.StateSelected[Context].object_face
        const PortID = vm.StateSelected[Context].port_id[Face]

        return PortID
      },
      set() {}
    },
    Populated: {
      get() {

        const vm = this
        const Context = vm.Context

        return ['yes']
      },
    },
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
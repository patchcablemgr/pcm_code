<!-- Template/Object Details -->

<template>
<div
  style="display:flex"
  @click.stop=" PartitionClicked({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'TemplateID': TemplateID, 'PartitionAddress': PartitionAddress, 'PortID': PortID}) "
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
    PartitionAddressSelected: {},
  },
  data() {
    return {
    }
  },
  computed: {
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    Medium() {
      return this.$store.state.pcmProps.Medium
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
    PortIsSelected: {
      get() {
        return (this.SelectedPortIndex !== null) ? true : false
      }
    },
    SelectedPortIndex: {
      get() {

        const vm = this
        const Context = vm.Context
        const Face = vm.PartitionAddressSelected[Context].object_face
        const PortID = vm.PartitionAddressSelected[Context].port_id[Face]

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
        const ObjectPartition = vm.PartitionAddressSelected[Context][ObjectFace]
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
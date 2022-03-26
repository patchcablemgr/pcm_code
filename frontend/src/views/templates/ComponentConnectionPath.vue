<!-- Template/Object Details -->

<template>
  <div>
	
  <b-card>
    <b-card-title>
      <div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing">
          {{CardTitle}}
        </div>
        <div class="demo-inline-spacing">
          <b-dropdown
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            right
            size="sm"
            text="Actions"
            variant="primary"
          >

          </b-dropdown>
        </div>
      </div>
    </b-card-title>
		<b-card-body
      ref="ConnectionCanvasParent"
    >
      <component-canvas
        v-if="Mounted"
        :CanvasHeight="ConnectionCanvasHeight"
        :CanvasWidth="ConnectionCanvasWidth"
      ></component-canvas>

      <div
        v-for="(Element) in ConnectionPath"
        v-bind:key="Element.id"
      >
        <div
          v-if="Element.id"
          v-bind:key="Element.id"
          class="pcm_box"
          :class="{
            pcm_template_partition_selected: PartitionIsSelected({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'PartitionAddress': Element.partition}),
            pcm_template_partition_hovered: PartitionIsHovered({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'PartitionAddress': Element.partition}),
          }"
          :style="{ background: GetCategory(Element.id).color}"
          @click.stop=" PartitionClicked({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'TemplateID': GetTemplateID(Element.id), 'PartitionAddress': Element.partition, 'PortID': null}) "
          @mouseover.stop="PartitionHovered({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'TemplateID': GetTemplateID(Element.id), 'PartitionAddress': Element.partition, 'HoverState': true})"
          @mouseleave.stop="PartitionHovered({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'TemplateID': GetTemplateID(Element.id), 'PartitionAddress': Element.partition, 'HoverState': false})"
        >
          {{GenerateDN('port', Element.id, Element.face, Element.partition, Element.port_id)}}
        </div>
      </div>

    </b-card-body>
  
  </b-card>

  </div>
</template>

<script>
import {
  BCard,
  BCardTitle,
  BCardBody,
  BDropdown,
  BDropdownItem,
  BDropdownDivider,
  BButton,
  BFormCheckbox,
  BFormSelect,
} from 'bootstrap-vue'
import ModalEditObjectName from './ModalEditObjectName.vue'
import ModalPortSelect from './ModalPortSelect.vue'
import ComponentCanvas from './ComponentCanvas.vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

const Mounted = false
const ConnectionCanvasHeight = 0
const ConnectionCanvasWidth = 0

export default {
  mixins: [PCM],
  components: {
		BCard,
		BCardTitle,
		BCardBody,
		BDropdown,
		BDropdownItem,
		BDropdownDivider,
    BButton,
    BFormCheckbox,
    BFormSelect,

    ModalEditObjectName,
    ModalPortSelect,
    ComponentCanvas,
  },
	directives: {
		Ripple,
	},
  props: {
    CardTitle: {type: String},
    Context: {type: String},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  data() {
    return {
      Mounted,
      ConnectionCanvasHeight,
      ConnectionCanvasWidth,
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
    ConnectionPath: {
      get(){

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.PartitionAddressSelected[Context].object_id
        const Face = vm.PartitionAddressSelected[Context].object_face
        const Partition = vm.PartitionAddressSelected[Context][Face]
        const PortID = vm.PartitionAddressSelected[Context].port_id[Face]

        return vm.GetConnectionPath(ObjectID, Face, Partition, PortID)
        
      },
      set(){
        return true
      }
    },
  },
  methods: {
    GetCategory: function(ObjectID){

      const vm = this
      const Context = vm.Context
      const TemplateID = vm.GetTemplateID(ObjectID, Context)
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
      const Template = vm.Templates[Context][TemplateIndex]
      const CategoryID = Template.category_id
      const CategoryIndex = vm.Categories.findIndex((cateogry) => cateogry.id == CategoryID )
      const Category = vm.Categories[CategoryIndex]

      return Category

    },
  },
  watch: {
    ConnectionPath: {
     immediate: true,
     handler: function (newVal, oldVal) {
       // Required to push handling of this to end of queue stack
       // Otherwise you will get the height before text replacement
       setTimeout(() => {
         this.ConnectionCanvasHeight = this.$refs.ConnectionCanvasParent.clientHeight
         this.ConnectionCanvasWidth = this.$refs.ConnectionCanvasParent.clientWidth
       }, 0);
      }
    }
  },
  mounted() {
    const vm = this
    vm.Mounted = true
  }
}
</script>
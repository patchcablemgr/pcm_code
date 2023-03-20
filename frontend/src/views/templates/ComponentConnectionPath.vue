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
        :ConnectionLineData="ConnectionLineData"
        :TrunkLineData="TrunkLineData"
      ></component-canvas>

      <table>
        <tr
          v-for="(Element) in ConnectionPath"
          v-bind:key="Element.uuid"
        >
          <td>
            <div
              v-if="Element.id"
              v-bind:key="Element.id"
              :data-trunk-pair="Element.trunk_pair"
              class="pcm_box ConnectionPathObject"
              :class="{
                pcm_template_partition_selected: PartitionIsSelected({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'PartitionAddress': Element.partition, 'PortID': Element.port_id}),
                pcm_template_partition_hovered: PartitionIsHovered({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'PartitionAddress': Element.partition, 'PortID': Element.port_id}),
              }"
              :style="{ background: GetCategory({ObjectID:Element.id, Context}).color}"
              @click.stop=" PartitionClicked({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'PartitionAddress': Element.partition, 'PortID': Element.port_id}) "
              @mouseover.stop="PartitionHovered({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'PartitionAddress': Element.partition, 'HoverState': true})"
              @mouseleave.stop="PartitionHovered({'Context': Context, 'ObjectID': Element.id, 'ObjectFace': Element.face, 'PartitionAddress': Element.partition, 'HoverState': false})"
            >
              {{GenerateDN('port', Element.id, Element.face, Element.partition, Element.port_id)}}
            </div>
          </td>
          <td>
            <component-port-r
              :data-port-pair="Element.port_pair"
              class="ConnectionPathPort"
              :Context="Context"
              :ObjectID="Element.id"
              :ObjectFace="Element.face"
              :TemplateID="GetTemplateID(Element.id)"
              :PartitionAddress="Element.partition"
              :PortID="Element.port_id"
            >
            </component-port-r>
          </td>
        </tr>
      </table>

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
import ComponentCanvas from './ComponentCanvas.vue'
import ComponentPortR from '@/views/templates/ComponentPortR.vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

const Mounted = false
const ConnectionCanvasHeight = 0
const ConnectionCanvasWidth = 0
const ConnectionLineData = []
const TrunkLineData = []

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

    ComponentCanvas,
    ComponentPortR,
  },
	directives: {
		Ripple,
	},
  props: {
    CardTitle: {type: String},
    Context: {type: String},
    PartitionAddressHovered: {type: Object},
  },
  data() {
    return {
      Mounted,
      ConnectionCanvasHeight,
      ConnectionCanvasWidth,
      ConnectionLineData,
      TrunkLineData,
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
    ConnectionPath: {
      get(){

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.StateSelected[Context].object_id
        const Face = vm.StateSelected[Context].object_face
        const Partition = vm.StateSelected[Context].partition[Face]
        const PortID = vm.StateSelected[Context].port_id[Face]

        return vm.GetConnectionPath(ObjectID, Face, Partition, PortID, Context)
        
      },
      set(){
        return true
      }
    },
  },
  methods: {
    /*
    GetCategory: function(ObjectID){

      const vm = this
      const Context = vm.Context
      const TemplateID = vm.GetTemplateID(ObjectID, Context)
      let Category

      return vm.GetCategory({ObjectID})
      
      if(TemplateID) {

        // Get template category
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
        const Template = vm.Templates[Context][TemplateIndex]
        const CategoryID = Template.category_id
        const CategoryIndex = vm.Categories[Context].findIndex((category) => category.id == CategoryID )
        Category = vm.Categories[Context][CategoryIndex]
      } else {

        // Get floorplan category
        const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
        const Object = vm.Objects[Context][ObjectIndex]
        const FloorplanObjectType = Object['floorplan_object_type']
        const CategoryIndex = vm.Categories['floorplan'].findIndex((category) => category.id == FloorplanObjectType )
        Category = vm.Categories['floorplan'][CategoryIndex]
      }

      return Category

    },
    */
  },
  watch: {
    ConnectionPath: {
      immediate: true,
      handler: function (newVal, oldVal) {

        // Required to push handling of this to end of queue stack
        // Otherwise you will get the height before text replacement
        setTimeout(() => {

          const vm = this

          vm.ConnectionLineData = []
          vm.TrunkLineData = []

          const ScrollLeft = document.documentElement.scrollLeft
          const ScrollTop = document.documentElement.scrollTop

          // Gather connection line data
          const PathPorts = document.querySelectorAll(".ConnectionPathPort")
          PathPorts.forEach(function(PathPort){

            const ElementLeft = PathPort.getBoundingClientRect().left
            const ElementTop = PathPort.getBoundingClientRect().top
            
            const PosX = ElementLeft + ScrollLeft
            const PosY = ElementTop + ScrollTop

            const PortPair = PathPort.dataset.portPair

            const LineIndex = vm.ConnectionLineData.findIndex(line => line.port_pair == PortPair)
            if(LineIndex !== -1) {
              vm.ConnectionLineData[LineIndex].line_coords.push([PosX, PosY])
            } else {
              vm.ConnectionLineData.push({'line_coords': [[PosX, PosY]], 'port_pair': PortPair})
            }
          })

          // Gather trunk line data
          const PathObjects = document.querySelectorAll(".ConnectionPathObject")
          PathObjects.forEach(function(PathObject){

            const ElementWidth = PathObject.offsetWidth
            const ElementHeight = PathObject.offsetHeight

            const ElementLeftOffset = ElementWidth/2

            const ElementLeft = PathObject.getBoundingClientRect().left
            const ElementTop = PathObject.getBoundingClientRect().top
            
            const PosX = ElementLeft + ScrollLeft + ElementLeftOffset
            const PosY = ElementTop + ScrollTop

            const TrunkPair = PathObject.dataset.trunkPair

            const LineIndex = vm.TrunkLineData.findIndex(line => line.trunk_pair == TrunkPair)
            if(LineIndex !== -1) {
              const ExistingElementTop = vm.TrunkLineData[LineIndex].element_top
              const ExistingElementHeight = vm.TrunkLineData[LineIndex].element_height
              if(ExistingElementTop < ElementTop) {
                vm.TrunkLineData[LineIndex].line_coords[0][1] = vm.TrunkLineData[LineIndex].line_coords[0][1] + ExistingElementHeight
              } else {
                PosY = PosY + ElementHeight
              }
              vm.TrunkLineData[LineIndex].line_coords.push([PosX, PosY])
            } else {
              vm.TrunkLineData.push({'line_coords': [[PosX, PosY]], 'trunk_pair': TrunkPair, 'element_top': ElementTop, 'element_height': ElementHeight})
            }
          })

          // Resize canvas
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
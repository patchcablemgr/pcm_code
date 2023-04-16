<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid>
      <b-row>
        <b-col>
          <b-card
            title="Locations and Cabinets"
          >
            <b-card-body>
              <component-location-tree
                Context="actual"
                TreeRef="LocationsAndCabinetsTree"
                :TreeIsContextual="TreeIsContextual"
              />
            </b-card-body>
          </b-card>

          <component-floorplan-object-details
            v-if=" PreviewDisplay == 'floorplan' "
            Context="actual"
            :DetailsAreEditable="DetailsAreEditable"
          />

          <component-floorplan-objects
            v-if=" PreviewDisplay == 'floorplan' "
            Context="actual"
            :PartitionAddressHovered="PartitionAddressHovered"
          />

        </b-col>
        <b-col
          v-if=" PreviewDisplay == 'floorplan' "
          col
          cols="12"
          sm="8"
        >
          <component-floorplan
            Context="actual"
            :FloorplanImage="FloorplanImage"
            :File="File"
            :PartitionAddressHovered="PartitionAddressHovered"
            :ObjectsAreDraggable="ObjectsAreDraggable"
            @FileSelected="FileSelected($event)"
            @FileSubmitted="FileSubmitted()"
          />
        </b-col>
        <b-col
          v-if=" PreviewDisplay == 'cabinet' || PreviewDisplay == 'none' "
          col
          cols="12"
          xl="4"
        >
          <card-cabinet
            Context="actual"
            CardTitle="Cabinet"
            :PreviewDisplay="PreviewDisplay"
            :ObjectsAreDraggable=false
            :CabinetFaceSelectIsDisabled=false
          />

        </b-col>
        <b-col
          v-if=" PreviewDisplay == 'cabinet' || PreviewDisplay == 'none' "
        >

          <component-template-Object-details
            CardTitle="Object Details"
						Context="actual"
						:TemplateFaceSelected="TemplateFaceSelected"
            :DetailsAreEditable="DetailsAreEditable"
            @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
					/>

          <component-port
            CardTitle="Port Details"
						Context="actual"
					/>

          <component-connection-path
            CardTitle="Connection Path"
						Context="actual"
            :PartitionAddressHovered="PartitionAddressHovered"
					/>

        </b-col>
      </b-row>
    </b-container>

    <!-- Toast -->
    <toast-general/>

  </div>
</template>

<script>
import {
  BContainer,
  BRow,
  BCol,
  BCard,
  BCardTitle,
  BCardBody,
  BCardText,
  BFormRadio,
  BFormCheckbox,
  BDropdown,
  BDropdownItem,
  BDropdownDivider,
  BButton,
  VBTooltip
} from 'bootstrap-vue'
import ToastGeneral from './templates/ToastGeneral.vue'
import ComponentLocationTree from '@/views/templates/ComponentLocationTree.vue'
import ComponentTemplateObjectDetails from './templates/ComponentTemplateObjectDetails.vue'
import ComponentTemplates from './templates/ComponentTemplates.vue'
import ModalTemplatesEdit from './templates/ModalTemplatesEdit.vue'
import { PCM } from '@/mixins/PCM.js'
import ComponentFloorplan from './templates/ComponentFloorplan.vue'
import ComponentFloorplanObjects from './templates/ComponentFloorplanObjects.vue'
import ComponentFloorplanObjectDetails from './templates/ComponentFloorplanObjectDetails.vue'
import ComponentPort from './templates/ComponentPort.vue'
import ComponentConnectionPath from './templates/ComponentConnectionPath.vue'
import CardCabinet from '@/views/templates/CardCabinet.vue'

const TemplateFaceSelected = {
  'actual': 'front',
  'template': 'front',
}

const PartitionAddressHovered = {
  'actual': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': false,
    'rear': false,
    'port_id': {
      'front': null,
      'rear': null,
    },
  }
}

const TreeIsContextual = false
const DetailsAreEditable = false
const ObjectsAreDraggable = false

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardTitle,
    BCardBody,
    BDropdown,
		BDropdownItem,
		BDropdownDivider,
    BCardText,
    BFormRadio,
    BFormCheckbox,
    BButton,

    ToastGeneral,
    ComponentLocationTree,
    ComponentTemplateObjectDetails,
    ComponentTemplates,
    ModalTemplatesEdit,
    ComponentFloorplan,
    ComponentFloorplanObjects,
    ComponentFloorplanObjectDetails,
    ComponentPort,
    ComponentConnectionPath,
    CardCabinet,
  },
  directives: {
    'b-tooltip': VBTooltip,
	},
  data() {
    return {
      TemplateFaceSelected,
      PartitionAddressHovered,
      TreeIsContextual,
      DetailsAreEditable,
      ObjectsAreDraggable,
      File: null,
    }
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.CategoriesReady.template,
        vm.CategoriesReady.actual,
        vm.TemplatesReady.template,
        vm.TemplatesReady.actual,
        vm.ObjectsReady.template,
        vm.LocationsReady.actual,
        vm.TrunksReady
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    LocationsReady: function() {
      return this.$store.state.pcmLocations.LocationsReady
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    CategoriesReady: function() {
      return this.$store.state.pcmCategories.CategoriesReady
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
    Trunks() {
      return this.$store.state.pcmTrunks.Trunks
    },
    TrunksReady: function() {
      return this.$store.state.pcmTrunks.TrunksReady
    },
    LocationID: function() {

      const vm = this
      const Context = 'actual'
      const LocationID = vm.StateSelected[Context].location_id

      return LocationID
    },
    FloorplanImage: function() {
      
      const vm = this
      const Context = 'actual'
      const LocationID = vm.LocationID
      let NodeFloorplanImage = null

      if(LocationID) {

        const NodeIndex = vm.GetLocationIndex(LocationID, Context)
        const Node = vm.Locations[Context][NodeIndex]
        NodeFloorplanImage = Node.img
      }

      return NodeFloorplanImage
    },
    PreviewDisplay: function() {

      const vm = this
      const Context = 'actual'
      const LocationID = vm.LocationID
      let PreviewDisplay = "none"

      if(LocationID) {

        const LocationIndex = vm.GetLocationIndex(LocationID, Context)
        const Location = vm.Locations[Context][LocationIndex]
        const NodeType = Location.type

        if(NodeType == 'location') {
          PreviewDisplay = "none"
        } else if(NodeType == 'pod') {
          PreviewDisplay = "none"
        } else if(NodeType == 'cabinet') {
          PreviewDisplay = "cabinet"
        } else if(NodeType == 'floorplan') {
          PreviewDisplay = "floorplan"
        }
      }

      return PreviewDisplay
    },
  },
  methods: {
    SetTemplateFaceSelected: function({Context, Face}) {

      const vm = this
      vm.TemplateFaceSelected[Context] = Face
    },
    FileSelected: function(EmitData) {

      const vm = this
      const FileEvent = EmitData.FileEvent
      
      vm.File = FileEvent.target.files[0]
    },
    FileSubmitted: function() {

      const vm = this
      const LocationID = vm.LocationID
      const url = '/api/locations/'+LocationID+'/image'
      let data = new FormData()
      const options = {
        'headers': {
          'Content-Type': 'multipart/form-data'
        }
      }
      data.append('file', vm.File)

      // POST floorplan image
      vm.$http.post(url, data, options).then(function(response){
        
        const Criteria = {
          "id": LocationID.toString()
        }
        const SelectedNode = vm.$refs.LocationTree.find(Criteria)[0]
        SelectedNode.data.img = response.data.img

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
  },
  watch: {
  },
  mounted() {},
}
</script>

<style>

</style>

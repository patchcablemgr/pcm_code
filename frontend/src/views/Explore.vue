<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid="xs">
      <b-row>
        <b-col>
          <b-card
            title="Locations and Cabinets"
          >
            <b-card-body>
              <component-location-tree
                Context="actual"
                TreeRef="LocationsAndCabinetsTree"
                :NodeIDSelected="NodeIDSelected"
                :TreeIsContextual="TreeIsContextual"
                @SetPartitionAddressSelected="SetPartitionAddressSelected($event)"
                @LocationNodeSelected="LocationNodeSelected($event)"
              />
            </b-card-body>
          </b-card>

          <component-floorplan-object-details
            v-if=" PreviewDisplay == 'floorplan' "
            Context="actual"
            :PartitionAddressSelected="PartitionAddressSelected"
            :DetailsAreEditable="DetailsAreEditable"
            @SetPartitionAddressSelected="SetPartitionAddressSelected($event)"
          />

          <component-floorplan-objects
            v-if=" PreviewDisplay == 'floorplan' "
            Context="actual"
            :NodeIDSelected="NodeIDSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
          />

        </b-col>
        <b-col
          cols="8"
          v-if=" PreviewDisplay == 'floorplan' "
        >
          <component-floorplan
            Context="actual"
            :FloorplanImage="FloorplanImage"
            :File="File"
            :NodeIDSelected="NodeIDSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            :ObjectsAreDraggable="ObjectsAreDraggable"
            @FileSelected="FileSelected($event)"
            @FileSubmitted="FileSubmitted()"
          />
        </b-col>
        <b-col
          v-if=" PreviewDisplay == 'cabinet' || PreviewDisplay == 'none' "
        >
          <b-card
            title="Cabinet"
          >

            <b-card-body
              v-if=" PreviewDisplay == 'none' "
            >
              Please select a cabinet from the Locations and Cabinets tree.
            </b-card-body>

            <b-card-body
              v-if=" PreviewDisplay == 'cabinet' "
            >
              <b-form-radio
                v-model="TemplateFaceSelected.actual"
                plain
                value="front"
              >Front
              </b-form-radio>
              <b-form-radio
                v-model="TemplateFaceSelected.actual"
                plain
                value="rear"
              >
                Rear
              </b-form-radio>
              <component-cabinet
                :LocationID="NodeIDSelected"
                Context="actual"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :PartitionAddressHovered="PartitionAddressHovered"
                :ObjectsAreDraggable="ObjectsAreDraggable"
                @StandardObjectDropped="StandardObjectDropped($event)"
                @InsertObjectDropped="InsertObjectDropped($event)"
                @LocationNodeSelected="LocationNodeSelected($event)"
              />
            </b-card-body>
          </b-card>

        </b-col>
        <b-col
          v-if=" PreviewDisplay == 'cabinet' || PreviewDisplay == 'none' "
        >

          <component-template-Object-details
            CardTitle="Object Details"
						Context="actual"
						:TemplateFaceSelected="TemplateFaceSelected"
						:PartitionAddressSelected="PartitionAddressSelected"
            :DetailsAreEditable="DetailsAreEditable"
            @SetPartitionAddressSelected="SetPartitionAddressSelected($event)"
            @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
					/>

          <component-port
            CardTitle="Port Details"
						Context="actual"
						:PartitionAddressSelected="PartitionAddressSelected"
					/>

          <component-connection-path
            CardTitle="Connection Path"
						Context="actual"
						:PartitionAddressSelected="PartitionAddressSelected"
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
  BDropdown,
  BDropdownItem,
  BDropdownDivider,
  BButton,
  VBTooltip
} from 'bootstrap-vue'
import ToastGeneral from './templates/ToastGeneral.vue'
import ComponentLocationTree from '@/views/templates/ComponentLocationTree.vue'
import ComponentCabinet from './templates/ComponentCabinet.vue'
import ComponentTemplateObjectDetails from './templates/ComponentTemplateObjectDetails.vue'
import ComponentTemplates from './templates/ComponentTemplates.vue'
import ModalTemplatesEdit from './templates/ModalTemplatesEdit.vue'
import { PCM } from '@/mixins/PCM.js'
import ComponentFloorplan from './templates/ComponentFloorplan.vue'
import ComponentFloorplanObjects from './templates/ComponentFloorplanObjects.vue'
import ComponentFloorplanObjectDetails from './templates/ComponentFloorplanObjectDetails.vue'
import ComponentPort from './templates/ComponentPort.vue'
import ComponentConnectionPath from './templates/ComponentConnectionPath.vue'

const TemplateFaceSelected = {
  'actual': 'front',
  'template': 'front',
}

const PartitionAddressSelected = {
  'actual': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': [0],
    'rear': [0],
    'port_id': {
      'front': null,
      'rear': null,
    },
  }
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

const NodeIDSelected = null

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
    BButton,

    ToastGeneral,
    ComponentLocationTree,
    ComponentCabinet,
    ComponentTemplateObjectDetails,
    ComponentTemplates,
    ModalTemplatesEdit,
    ComponentFloorplan,
    ComponentFloorplanObjects,
    ComponentFloorplanObjectDetails,
    ComponentPort,
    ComponentConnectionPath,
  },
  directives: {
    'b-tooltip': VBTooltip,
	},
  data() {
    return {
      TemplateFaceSelected,
      PartitionAddressSelected,
      PartitionAddressHovered,
      NodeIDSelected,
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
        vm.CategoriesReady,
        vm.TemplatesReady.template,
        vm.TemplatesReady.actual,
        vm.ObjectsReady.template,
        vm.LocationsReady,
        vm.TrunksReady
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    LocationsReady: function() {
      return this.$store.state.pcmLocations.LocationsReady.actual
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
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
    Trunks() {
      return this.$store.state.pcmTrunks.Trunks
    },
    TrunksReady: function() {
      return this.$store.state.pcmTrunks.TrunksReady
    },
    FloorplanImage: function() {
      
      const vm = this
      const Context = 'actual'
      const NodeID = vm.NodeIDSelected
      let NodeFloorplanImage = null

      if(NodeID) {

        const NodeIndex = vm.GetLocationIndex(NodeID, Context)
        const Node = vm.Locations[Context][NodeIndex]
        NodeFloorplanImage = Node.img
      }

      return NodeFloorplanImage
    },
    PreviewDisplay: function() {

      const vm = this
      const Context = 'actual'
      const NodeID = vm.NodeIDSelected
      let PreviewDisplay = "none"

      if(NodeID) {

        const LocationIndex = vm.GetLocationIndex(NodeID, Context)
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
    LocationNodeSelected: function(EmitData) {
      const vm = this
      const NodeID = EmitData.id
      vm.NodeIDSelected = NodeID
    },
    SetPartitionAddressSelected: function({Context, object_id, front, rear}) {

      const vm = this
      vm.PartitionAddressSelected[Context].object_id = object_id
      vm.PartitionAddressSelected[Context].front = front
      vm.PartitionAddressSelected[Context].rear = rear
    },
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
      const LocationID = vm.NodeIDSelected
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
    InsertObjectDropped: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const ParentID = EmitData.parent_id
      const ParentFace = EmitData.parent_face
      const ParentPartitionAddress = EmitData.parent_partition_address
      const ParentEnclosureAddress = EmitData.parent_enclosure_address
      let data = {}
      let url

      data.parent_id = ParentID
      data.parent_face = ParentFace
      data.parent_partition_address = ParentPartitionAddress
      data.parent_enclosure_address = ParentEnclosureAddress

      // POST new object
      if(Context == 'template') {

        const TemplateID = EmitData.template_id
        const TemplateFace = EmitData.template_face

        data.template_id = TemplateID
        data.template_face = TemplateFace

        url = '/api/objects/insert'

        // POST to objects
        vm.$http.post(url, data).then(function(response){

          vm.$store.commit('pcmObjects/ADD_Object', {pcmContext:'actual', data:response.data})

        }).catch(error => {vm.DisplayError(error)})
      } else {

        const ObjectID = EmitData.object_id

        data.object_id = ObjectID

        url = '/api/objects/'+ObjectID

        // POST to objects
        vm.$http.patch(url, data).then(function(response){

          vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:'actual', data:response.data})

        }).catch(error => {vm.DisplayError(error)})
      }
    },
    StandardObjectDropped: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const LocationID = EmitData.location_id
      const CabinetFace = EmitData.cabinet_face
      const CabinetRU = EmitData.cabinet_ru
      let data
      let url

      data = {
        "location_id": LocationID,
        "cabinet_face": CabinetFace,
        "cabinet_ru": CabinetRU,
      }

      // POST new object
      if(Context == 'template') {

        const TemplateID = EmitData.template_id
        const TemplateFace = EmitData.template_face

        data.template_id = TemplateID
        data.template_face = TemplateFace

        url = '/api/objects/standard'

        // POST to objects
        vm.$http.post(url, data).then(function(response){

          vm.$store.commit('pcmObjects/ADD_Object', {pcmContext:'actual', data:response.data})

        }).catch(error => {vm.DisplayError(error)})

      // PATCH existing object
      } else {

        const ObjectID = EmitData.object_id

        data.object_id = ObjectID

        url = '/api/objects/'+ObjectID

        // POST to objects
        vm.$http.patch(url, data).then(function(response){

          vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:'actual', data:response.data})

        }).catch(error => {vm.DisplayError(error)})
      }
    },
    GETCategories: function() {

      const vm = this
      vm.$http.get('/api/categories')
      .then(response => {
        vm.$store.commit('pcmCategories/SET_Categories', response.data)
        vm.$store.commit('pcmCategories/SET_Ready', true)
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETMedia: function() {

      const vm = this;

      vm.$http.get('/api/medium').then(function(response){
        vm.$store.commit('pcmProps/SET_Medium', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop:'medium', ReadyState:true})
      });
    },
    GETTemplates: function () {

      const vm = this
			const ContextTemplate = 'template'
      const ContextActual = 'actual'
      
      vm.$http.get('/api/templates').then(function(response){

        vm.$store.commit('pcmTemplates/SET_Templates', {pcmContext: ContextTemplate, data: response.data})
        vm.$store.commit('pcmTemplates/SET_Templates', {pcmContext: ContextActual, data: response.data})

        response.data.forEach(function(template) {
          vm.GeneratePseudoData(ContextTemplate, template)
        })
        vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:ContextTemplate, ReadyState:true})
        vm.$store.commit('pcmTemplates/SET_Ready', {pcmContext:ContextTemplate, ReadyState:true})
        vm.$store.commit('pcmTemplates/SET_Ready', {pcmContext:ContextActual, ReadyState:true})
      }).catch(error => { vm.DisplayError(error) })
    },
    GETObjects: function () {

      const vm = this
			const Context = 'actual'
      
      vm.$http.get('/api/objects').then(function(response){

        vm.$store.commit('pcmObjects/SET_Objects', {pcmContext: Context, data: response.data})
        vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:Context, ReadyState:true})
      })
    },
    GETPortOrientations: function() {

      const vm = this

      this.$http.get('/api/port-orientation').then(function(response){
        vm.$store.commit('pcmProps/SET_Orientations', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop:'orientations', ReadyState:true})
      })
    },
    GETPortConnectors: function() {

      const vm = this

      vm.$http.get('/api/port-connectors').then(function(response){
        vm.$store.commit('pcmProps/SET_Connectors', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop:'connectors', ReadyState:true})
      })
    },
    GETFloorplanTemplates: function() {

      const vm = this

      vm.$http.get('/api/floorplan-templates').then(function(response){
        vm.$store.commit('pcmFloorplanTemplates/SET_FloorplanTemplates', {data: response.data})
        vm.$store.commit('pcmFloorplanTemplates/SET_Ready', {ReadyState:true})
      })
    },
    GETLocations() {

      const vm = this
      const Context = 'actual'

      // GET locations
      vm.$http.get('/api/locations').then(response => {
        vm.$store.commit('pcmLocations/SET_Locations', {pcmContext:Context, data:response.data})
        vm.$store.commit('pcmLocations/SET_Ready', {pcmContext:Context, ReadyState:true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETTrunks() {

      const vm = this

      // GET trunks
      vm.$http.get('/api/trunks').then(response => {
        vm.$store.commit('pcmTrunks/SET_Trunks', {data:response.data})
        vm.$store.commit('pcmTrunks/SET_Ready', {ReadyState:true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETConnections() {

      const vm = this

      // GET connections
      vm.$http.get('/api/connections').then(response => {
        vm.$store.commit('pcmConnections/SET_Connections', {data:response.data})
        vm.$store.commit('pcmConnections/SET_Ready', {ReadyState:true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  },
  watch: {
  },
  mounted() {

    const vm = this

    vm.GETLocations()
    vm.GETCategories()
    vm.GETTemplates()
    vm.GETFloorplanTemplates()
    vm.GETMedia()
    vm.GETObjects()
    vm.GETPortOrientations()
    vm.GETPortConnectors()
    vm.GETTrunks()
    vm.GETConnections()

  },
}
</script>

<style>

</style>

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
                @LocationNodeSelected="LocationNodeSelected($event)"
              />
            </b-card-body>
          </b-card>

          <component-floorplan-object-details
            v-if=" PreviewDisplay == 'floorplan' "
            :FloorplanTemplateData="FloorplanTemplateData"
            :ObjectData="ObjectData"
            :TemplateData="TemplateData"
            :LocationData="LocationData"
            :PartitionAddressSelected="PartitionAddressSelected"
            @FloorplanClicked=" FloorplanClicked($event) "
            @FloorplanHovered=" FloorplanHovered($event) "
            @ObjectEdited="ObjectEdited($event, 'floorplan')"
          />

          <component-floorplan-objects
            v-if=" PreviewDisplay == 'floorplan' "
            :FloorplanTemplateData="FloorplanTemplateData"
            :ObjectData="ObjectData"
            :PartitionAddressSelected="PartitionAddressSelected"
            @FloorplanClicked=" FloorplanClicked($event) "
            @FloorplanHovered=" FloorplanHovered($event) "
          />

        </b-col>
        <b-col
          cols="8"
          v-if=" PreviewDisplay == 'floorplan' "
        >
          <component-floorplan
            :FloorplanImage="FloorplanImage"
            :File="File"
            :CabinetData="CabinetData"
            :ObjectData="ObjectData"
            Context="preview"
            :FloorplanTemplateData="FloorplanTemplateData"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @FloorplanClicked=" FloorplanClicked($event) "
            @FloorplanHovered=" FloorplanHovered($event) "
            @FloorplanObjectDropped="FloorplanObjectDropped($event)"
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
                @PartitionClicked=" PartitionClicked($event) "
                @PartitionHovered=" PartitionHovered($event) "
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
            @SetPartitionAddressSelected="SetPartitionAddressSelected($event)"
            @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
					/>

          <component-templates
            Context="template"
            :TemplateFaceSelected="TemplateFaceSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @PartitionClicked="PartitionClicked($event)"
            @PartitionHovered="PartitionHovered($event)"
            @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
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

const LocationData = []

const CabinetData = {
  "id": 0,
  "size": 25,
  "name": "Cabinet",
}

const ObjectData = {
  'preview': [],
  'template': [],
}

const CategoryData = [
  {
    "id": 0,
    "color": "#FFFFFFFF",
  }
]

const PortConnectorData = [
  {
    "value": 1,
    "name": "RJ45",
    "category_type_id": 1,
    "default": 1,
  },
]

const TemplateData = {
  'preview': [],
  'template': [],
}

const TemplateFaceSelected = {
  'actual': 'front',
  'template': 'front',
}

const PartitionAddressSelected = {
  'actual': {
    'object_id': null,
    'template_id': null,
    'front': [0],
    'rear': [0]
  },
  'template': {
    'object_id': null,
    'template_id': null,
    'front': [0],
    'rear': [0]
  },
  'floorplan': {
    'object_id': null
  }
}

const PartitionAddressHovered = {
  'actual': {
    'object_id': null,
    'template_id': null,
    'front': false,
    'rear': false
  },
  'template': {
    'object_id': null,
    'template_id': null,
    'front': false,
    'rear': false
  },
  'floorplan': {
    'object_id': null
  }
}

const GenericObject = {
    "id": null,
    "pseudo": true,
    "name": null,
    "template_id": null,
    "location_id": null,
    "cabinet_ru": null,
    "cabinet_front": null,
    "parent_id": null,
    "parent_face": null,
    "parent_partition_address": null,
    "parent_enclosure_address": null,
}

const GenericTemplate = {
    "id": null,
    "pseudo": true,
    "name": "PseudoTemplate",
    "category_id": null,
    "type": null,
    "ru_size": null,
    "function": null,
    "mount_config": "2-post",
    "insert_constraints": null,
    "blueprint": {
        "front": [{
                "type": "generic",
                "units": null,
                "children": [{
                        "type": "enclosure",
                        "units": null,
                        "children": [],
                        "enc_layout": {
                            "cols": null,
                            "rows": null,
                        },
                    },
                ],
            },
        ],
        "rear": []
    },
}

const PortOrientationData = [
  {
    "value": 1,
    "name": "Top-Left to Right",
    "default": 1,
  },
]

const MediaData = [
  {
    "value": 1,
    "name": "placeholder",
    "category_id": 1,
    "type_id": 1,
    "display": 1,
    "default": 1,
  }
]

const FloorplanTemplateData = []

const NodeIDSelected = null

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
  },
  directives: {
    'b-tooltip': VBTooltip,
	},
  data() {
    return {
      LocationData,
      CabinetData,
      CategoryData,
      ObjectData,
      TemplateData,
      TemplateFaceSelected,
      PartitionAddressSelected,
      PartitionAddressHovered,
      GenericObject,
      GenericTemplate,
      NodeIDSelected,
      PortOrientationData,
      MediaData,
      FloorplanTemplateData,
      PortConnectorData,
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
        vm.LocationsReady
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
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
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
    TemplatePartitionPortRange: function(){
      
      // Initialize some variables
      const vm = this
      const Context = 'preview'
      const TemplateID = vm.PartitionAddressSelected[Context].template_id
      let PortRangeString = '-'

      if(TemplateID) {

        // Get template partition address
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const TemplatePartition = vm.PartitionAddressSelected[Context][TemplateFace]

        // Get template blueprint
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
        const Template = vm.TemplateData[Context][TemplateIndex]
        const TemplateBlueprint = Template.blueprint[TemplateFace]

        // Get template partition
        const Partition = vm.GetPartition(TemplateBlueprint, TemplatePartition)
        const PartitionType = Partition.type

        if(PartitionType == 'connectable') {

          // Calculate port numbers
          const PortFormat = Partition.port_format
          const PortLayoutCols = Partition.port_layout.cols
          const PortLayoutRows = Partition.port_layout.rows
          const PortTotal = PortLayoutCols * PortLayoutRows
          const FirstPortID = 0
          const LastPortID = PortTotal - 1

          const FirstPortIDString = vm.GeneratePortID(FirstPortID, PortTotal, PortFormat)
          const LastPortIDString = vm.GeneratePortID(LastPortID, PortTotal, PortFormat)

          PortRangeString = FirstPortIDString+' - '+LastPortIDString
        }
      }

      return PortRangeString

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
    GetPartition: function(Blueprint, PartitionAddress) {
			
			// Locate template partition
			let Partition = Blueprint;
			let PartitionCollection = Blueprint
			PartitionAddress.forEach(function(PartitionIndex) {
				if(typeof PartitionCollection[PartitionIndex] !== 'undefined') {
					Partition = PartitionCollection[PartitionIndex]
					PartitionCollection = PartitionCollection[PartitionIndex]['children']
				} else {
					return false
				}
			})
			
			return Partition
      
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
    TemplateObjectEditClicked: function() {

      const vm = this
      vm.$bvModal.show('modal-templates-edit')

    },
    ObjectEdited: function(EmitData, Context='preview') {

      // Store data
      const vm = this
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
      const url = '/api/objects/'+ObjectID
      const data = EmitData

      // PATCH object
      this.$http.patch(url, data).then(function(response){
        
        const Object = response.data
        const ObjectIndex = vm.GetObjectIndex(ObjectID, 'preview')
				
        // Append new object to object array
        vm.$set(vm.ObjectData.preview, ObjectIndex, Object)

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
    FloorplanObjectDropped: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const FloorplanAddress = EmitData.floorplan_address
      let data = {}
      let url

      data.floorplan_address = FloorplanAddress

      // POST new object
      if(Context == 'template') {

        const LocationID = EmitData.location_id
        const FloorplanObjectType = EmitData.floorplan_object_type

        data.location_id = LocationID
        data.floorplan_object_type = FloorplanObjectType

        url = '/api/objects/floorplan'

        // POST to objects
        vm.$http.post(url, data).then(function(response){

          const Object = response.data
          
          // Create child node object
          vm.ObjectData.preview.push(Object)

        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response), {
            title: 'Error',
            variant: 'danger',
          })

        })
      } else if(Context == 'floorplan') {

        const FloorplanObjectID = EmitData.floorplan_object_id

        url = '/api/objects/'+FloorplanObjectID

        // PATCH to objects
        vm.$http.patch(url, data).then(function(response){

          const Object = response.data
          const ObjectIndex = vm.GetObjectIndex(FloorplanObjectID, 'preview')
          
          // Create child node object
          vm.$set(vm.ObjectData.preview, ObjectIndex, Object)

        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response), {
            title: 'Error',
            variant: 'danger',
          })

        })
      }
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

          const Object = response.data
          
          // Create child node object
          vm.ObjectData.preview.push(Object)

        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response), {
            title: 'Error',
            variant: 'danger',
          })

        })
      } else {

        const ObjectID = EmitData.object_id

        data.object_id = ObjectID

        url = '/api/objects/'+ObjectID

        // POST to objects
        vm.$http.patch(url, data).then(function(response){

          const Object = response.data
          const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
          
          // Create child node object
          vm.$set(vm.ObjectData.preview, ObjectIndex, Object)

        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response), {
            title: 'Error',
            variant: 'danger',
          })

        })
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

          const Object = response.data
          
          // Create child node object
          vm.ObjectData.preview.push(Object)

        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response), {
            title: 'Error',
            variant: 'danger',
          })

        })

      // PATCH existing object
      } else {

        const ObjectID = EmitData.object_id

        data.object_id = ObjectID

        url = '/api/objects/'+ObjectID

        // POST to objects
        vm.$http.patch(url, data).then(function(response){

          const Object = response.data
          const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
          
          // Create child node object
          vm.$set(vm.ObjectData.preview, ObjectIndex, Object)

        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response), {
            title: 'Error',
            variant: 'danger',
          })

        })
      }
    },
    TemplateObjectDeleteClicked: function() {
      
			const vm = this
      const Context = 'preview'
			const ObjectID = vm.PartitionAddressSelected[Context].object_id
			
			const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
			const ObjectName = vm.ObjectData[Context][ObjectIndex].name
			
      vm.$bvModal
        .msgBoxConfirm('Delete '+ObjectName+'?', {
          title: 'Confirm',
          size: 'sm',
          okVariant: 'primary',
          okTitle: 'Yes',
          cancelTitle: 'No',
          cancelVariant: 'outline-secondary',
          hideHeaderClose: false,
          centered: true,
        })
        .then(value => {
					
					if(value) {
						
						// Store data
						const url = '/api/objects/'+ObjectID

						// DELETE object ID
						this.$http.delete(url).then(function(response){

							// Default selected
							vm.PartitionAddressSelected[Context].object_id = null
              vm.PartitionAddressSelected[Context].template_id = null

              // Get template object data
              const DeletedObjectID = response.data.id
              const DeletedObjectIndex = vm.GetObjectIndex(DeletedObjectID, Context)

              // Delete template and object
              vm.ObjectData[Context].splice(DeletedObjectIndex,1)

						}).catch(error => {

							// Display error to user via toast
							vm.$bvToast.toast(JSON.stringify(error.response.data), {
								title: 'Error',
								variant: 'danger',
							})

						});
					}
        })
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
        vm.MediaData = response.data;
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

      const vm = this;

      this.$http.get('/api/port-orientation').then(function(response){
        vm.$store.commit('pcmProps/SET_Orientations', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop:'orientations', ReadyState:true})
      });
    },
    GETPortConnectors: function() {

      const vm = this;

      vm.$http.get('/api/port-connectors').then(function(response){
        vm.$store.commit('pcmProps/SET_Connectors', response.data)
        vm.$store.commit('pcmProps/SET_Ready', {Prop:'connectors', ReadyState:true})
      });
    },
    GETFloorplanTemplates: function() {

      const vm = this;

      vm.$http.get('/api/floorplan-templates').then(function(response){
        vm.FloorplanTemplateData = response.data;
      });
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

  },
}
</script>

<style>

</style>

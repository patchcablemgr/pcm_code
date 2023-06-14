<template>

<div
  ref="CabinetCanvasParent"
>
    <component-canvas
      v-if="Mounted"
      :CanvasHeight="ConnectionCanvasHeight"
      :CanvasWidth="ConnectionCanvasWidth"
      :ConnectionLineData="ConnectionLineData"
      :TrunkLineData="TrunkLineData"
    ></component-canvas>

    <!-- Cabinet -->
    <table>
      <tr>
        <td class="pcm_cabinet" colspan=3>
          {{ LocationData.name }}
        </td>
      </tr>
      <tr
        class="pcm_cabinet_row"
        v-for="CabinetRU in LocationData.size"
        :key="CabinetRU"
        :CabinetID="LocationID"
      >
        <td class="pcm_cabinet">{{ GetRUNumber(CabinetRU) }}</td>
        <td
          v-if=" RackObjectID(CabinetRU) !== false "
          class="pcm_cabinet_ru"
          :rowspan=" GetObjectSize( RackObjectID(CabinetRU) ) "
          :style="{height:(25*GetObjectSize( RackObjectID(CabinetRU) ))+'px'}"
        >
          <component-object
            :InitialPartitionAddress=[]
            :Context="Context"
            :ObjectID="RackObjectID(CabinetRU)"
            :CabinetFace="SelectedCabinetFace"
            :ObjectsAreDraggable="ObjectsAreDraggable"
            :TemplateView="TemplateView"
          />
        </td>
        <td
          v-else-if="!RUIsOccupied(LocationID, CabinetRU)"
          @drop="HandleDrop(CabinetRU, $event)"
          @dragover.prevent
          @dragenter.prevent
          class="pcm_cabinet_ru"
        />
        <td class="pcm_cabinet">{{ GetRUNumber(CabinetRU) }}</td>
      </tr>
      <tr>
        <td class="pcm_cabinet" colspan=3>
          {{ LocationData.name }}
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import ComponentObject from '@/views/templates/ComponentObject.vue'
import ComponentCanvas from '@/views/templates/ComponentCanvas.vue'
import CartDropdown from '../../@core/layouts/components/app-navbar/components/CartDropdown.vue'
import { PCM } from '../../mixins/PCM.js'

const ConnectionCanvasHeight = 0
const ConnectionCanvasWidth = 0
const ConnectionLineData = []
const TrunkLineData = []

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,

    ComponentObject,
    CartDropdown,
  },
  props: {
    Context: {type: String},
    SelectedCabinetFace: {type: String},
    ObjectsAreDraggable: {type: Boolean},
    TemplateView: {type: String},
  },
  data() {
    return {
      ConnectionCanvasHeight,
      ConnectionCanvasWidth,
      ConnectionLineData,
      TrunkLineData,
    }
  },
  computed: {
    Objects: function() {
      return this.$store.state.pcmObjects.Objects
    },
    Templates: function() {
      return this.$store.state.pcmTemplates.Templates
    },
    Locations: function() {
      return this.$store.state.pcmLocations.Locations
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    LocationID: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.StateSelected[Context].location_id
      
      return LocationID
    },
    LocationData: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID
      const LocationIndex = vm.Locations[Context].findIndex((location) => location.id == LocationID)

      return vm.Locations[Context][LocationIndex]
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
    GetRUNumber: function(RUIndex) {

      const vm = this
      const Cabinet = vm.LocationData
      const CabinetOrientation = Cabinet.ru_orientation
      let RUNumber = RUIndex

      if(CabinetOrientation == 'bottom-up') {
        const CabinetSize = Cabinet.size
        RUNumber = CabinetSize - (RUIndex - 1)
      }

      return RUNumber
    },
    RackObjectID: function(CabinetRU) {
      
      // Initial variables
      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID
      const TemplateFace = vm.SelectedCabinetFace

      const ObjectIndex = vm.Objects[Context].findIndex(function(Object, ObjectIndex) {
        if(Object.location_id == LocationID && Object.cabinet_ru == CabinetRU) {
          const ObjectCabinetFace = Object.cabinet_front
          const ObjectID = Object.id
          const Template = vm.GetTemplate({ObjectID, Context})
          const TemplateMountConfig = Template.mount_config

          if(ObjectCabinetFace == TemplateFace || TemplateMountConfig == "4-post") {
            return true
          }
        }
      })

      const RackObjectID = (ObjectIndex !== -1) ? vm.Objects[Context][ObjectIndex].id : false

      return RackObjectID
    },
    RUIsOccupied: function(CabinetID, CabinetRU) {
      
      // Store variables
      const vm = this
      const Context = vm.Context
      const Objects = vm.Objects
      const Templates = vm.Templates
      const SelectedCabinetFace = vm.SelectedCabinetFace
      const CabinetObjects = Objects[Context].filter((object) => object.location_id == CabinetID);
      let ObjectIsPresent = false

      CabinetObjects.forEach(function(object){
        // Store object dependent variables
        const ObjectID = object.id
        const ObjectCabinetFace = object.cabinet_front
        const ObjectCabinetRU = object.cabinet_ru

        // Get template data
        const TemplateID = vm.GetTemplateID(ObjectID, Context)
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
        const Template = Templates[Context][TemplateIndex]

        // Store template variables
        const TemplateSize = Template.ru_size
        const TemplateMountConfig = Template.mount_config

        const ObjectFirstRU = ObjectCabinetRU
        const ObjectLastRU = ObjectFirstRU + (TemplateSize - 1)

        // Determine if object is present at cabinet RU
        if(ObjectCabinetFace == SelectedCabinetFace || TemplateMountConfig == "4-post") {
          if(CabinetRU <= ObjectLastRU && CabinetRU >= ObjectFirstRU) {
            ObjectIsPresent = true
          }
        }
      })

      return ObjectIsPresent
    },
    HandleDrop: function(CabinetRU, event) {

      const vm = this
      const Context = vm.Context
      const ObjectContext = event.dataTransfer.getData('context')
      let ObjectID = event.dataTransfer.getData('object_id')
      let TemplateID = event.dataTransfer.getData('template_id')
      const TemplateFace = event.dataTransfer.getData('template_face')

      // getData() returns string which needs to be converted back to integer if it is a number
      ObjectID = (vm.is_Numeric(ObjectID)) ? parseInt(ObjectID) : ObjectID
      TemplateID = (vm.is_Numeric(TemplateID)) ? parseInt(TemplateID) : TemplateID

      const Template = vm.GetTemplate({TemplateID, Context})
      const TemplateType = Template.type

      if(TemplateType == 'standard') {
        const LocationID = vm.LocationID
        const CabinetFace = vm.SelectedCabinetFace
        let CabinetFront
        if(CabinetFace == 'front') {
          CabinetFront = TemplateFace
        } else {
          if(TemplateFace == 'front') {
            CabinetFront = 'rear'
          } else {
            CabinetFront = 'front'
          }
        }
        let data
        let url

        data = {
          "location_id": LocationID,
          "cabinet_front": CabinetFront,
          "cabinet_ru": vm.GetRUNumber(CabinetRU),
        }

        // POST new object
        if(ObjectContext == 'template') {

          data.template_id = TemplateID

          url = '/api/objects/standard'

          // POST to objects
          vm.$http.post(url, data).then(function(response){

            const Object = response.data
            
            // Create cabinet object
            vm.$store.commit('pcmObjects/ADD_Object', {pcmContext:Context, data:Object})

          }).catch(error => { vm.DisplayError(error) })

        // PATCH existing object
        } else {

          data.object_id = ObjectID

          url = '/api/objects/'+ObjectID

          // POST to objects
          vm.$http.patch(url, data).then(function(response){

            const Object = response.data
            
            // Update cabinet object
            vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:Context, data:Object})

          }).catch(error => { vm.DisplayError(error) })
        }
      }
    },
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
}
</script>
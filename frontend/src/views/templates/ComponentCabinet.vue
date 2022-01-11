<template>
  <!-- Template categories modal -->
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
      <td class="pcm_cabinet">{{ CabinetRU }}</td>
      <td
        v-if=" RackObjectID(CabinetRU) !== false "
        class="pcm_cabinet_ru"
        :rowspan=" GetObjectSize( RackObjectID(CabinetRU) ) "
      >
        <component-object
          :TemplateRUSize=" GetObjectSize( RackObjectID(CabinetRU) ) "
          :InitialPartitionAddress=[]
          :Context="Context"
          :ObjectID="RackObjectID(CabinetRU)"
          :TemplateFaceSelected="TemplateFaceSelected"
          :PartitionAddressSelected="PartitionAddressSelected"
          :PartitionAddressHovered="PartitionAddressHovered"
          @InsertObjectDropped=" $emit('InsertObjectDropped', $event) "
        />
      </td>
      <td
        v-else-if="!RUIsOccupied(LocationID, CabinetRU)"
        @drop="HandleDrop(CabinetRU, $event)"
        @dragover.prevent
        @dragenter.prevent
        class="pcm_cabinet_ru"
      />
      <td class="pcm_cabinet">{{ CabinetRU }}</td>
    </tr>
    <tr>
      <td class="pcm_cabinet" colspan=3>
        {{ LocationData.name }}
      </td>
    </tr>
  </table>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import ComponentObject from './ComponentObject.vue'
import CartDropdown from '../../@core/layouts/components/app-navbar/components/CartDropdown.vue'
import { PCM } from '../../mixins/PCM.js'

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
    LocationID: {type: Number},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  data() {
    return {
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
    LocationData: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID
      const LocationIndex = vm.Locations[Context].findIndex((location) => location.id == LocationID)

      return vm.Locations[Context][LocationIndex]
    }
  },
  methods: {
    RackObjectID: function(CabinetRU) {
      
      // Initial variables
      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID
      const TemplateFace = vm.TemplateFaceSelected[Context]

      const ObjectIndex = vm.Objects[Context].findIndex(function(Object, ObjectIndex) {
        if(Object.location_id == LocationID && Object.cabinet_ru == CabinetRU) {
          const ObjectCabinetFace = Object.cabinet_front
          const ObjectID = Object.id
          const TemplateID = vm.GetTemplateID(ObjectID, Context)
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          const Template = vm.Templates[Context][TemplateIndex]
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
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
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
        if(ObjectCabinetFace == TemplateFaceSelected || TemplateMountConfig == "4-post") {
          if(CabinetRU <= ObjectLastRU && CabinetRU >= ObjectFirstRU) {
            ObjectIsPresent = true
          }
        }
      })

      return ObjectIsPresent
    },
    HandleDrop: function(CabinetRU, event) {

      const vm = this
      const Context = event.dataTransfer.getData('context')
      const ObjectID = event.dataTransfer.getData('object_id')
      const TemplateID = event.dataTransfer.getData('template_id')
      const TemplateFace = event.dataTransfer.getData('template_face')

      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
      const Template = vm.Templates[Context][TemplateIndex]
      const TemplateType = Template.type

      if(TemplateType == 'standard') {
        const LocationID = vm.LocationID
        const CabinetFace = vm.TemplateFaceSelected[Context]
        let data
        let url

        data = {
          "location_id": LocationID,
          "cabinet_face": CabinetFace,
          "cabinet_ru": CabinetRU,
        }

        // POST new object
        if(Context == 'template') {

          data.template_id = TemplateID
          data.template_face = TemplateFace

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
  }
}
</script>
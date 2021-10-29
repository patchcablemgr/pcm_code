<template>
  <!-- Template categories modal -->
  <table>
    <tr>
      <td class="pcm_cabinet" colspan=3>
        {{ CabinetData.name }}
      </td>
    </tr>
    <tr
      class="pcm_cabinet_row"
      v-for="CabinetRU in CabinetData.size"
      :key="CabinetRU"
      :CabinetID="CabinetData.id"
    >
      <td class="pcm_cabinet">{{ CabinetRU }}</td>
      <td
        v-if=" RackObjectID(CabinetData.id, CabinetRU) !== false "
        class="pcm_cabinet_ru"
        :rowspan=" GetObjectSize( RackObjectID(CabinetData.id, CabinetRU) ) "
      >
        <component-object
          :ObjectData="ObjectData"
          :TemplateData="TemplateData"
          :CategoryData="CategoryData"
          :TemplateRUSize=" GetObjectSize( RackObjectID(CabinetData.id, CabinetRU) ) "
          :InitialPartitionAddress=[]
          :Context="Context"
          :ObjectID="RackObjectID(CabinetData.id, CabinetRU)"
          :TemplateFaceSelected="TemplateFaceSelected"
          :PartitionAddressSelected="PartitionAddressSelected"
          :PartitionAddressHovered="PartitionAddressHovered"
          @PartitionClicked=" $emit('PartitionClicked', $event) "
          @PartitionHovered=" $emit('PartitionHovered', $event) "
          @ObjectDropped=" $emit('ObjectDropped', $event) "
        />
      </td>
      <td
        v-else-if="!RUIsOccupied(CabinetData.id, CabinetRU)"
        @drop="HandleDrop(CabinetData.id, TemplateFaceSelected.preview, CabinetRU, $event)"
        @dragover.prevent
        @dragenter.prevent
        class="pcm_cabinet_ru"
      />
      <td class="pcm_cabinet">{{ CabinetRU }}</td>
    </tr>
    <tr>
      <td class="pcm_cabinet" colspan=3>
        {{ CabinetData.name }}
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
    TemplateData: {type: Object},
    CabinetData: {type: Object},
    CategoryData: {type: Array},
    ObjectData: {type: Object},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  data() {
    return {
    }
  },
  methods: {
    GetPreviewData: function(ObjectID) {

      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.GetObjectIndex(ObjectID)

      // Get template index
      const TemplateID = vm.ObjectData[Context][ObjectIndex].template_id
      const TemplateIndex = vm.GetTemplateIndex(TemplateID)

      // Get template
      const ObjectPreviewData = TemplateData[Context][TemplateIndex]

      // Return template
      return ObjectPreviewData
    },
    RackObjectID: function(CabinetID, CabinetRU) {
      
      // Initial variables
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]

      const ObjectIndex = vm.ObjectData[Context].findIndex(function(Object, ObjectIndex) {
        if(Object.cabinet_id == CabinetID && Object.cabinet_ru == CabinetRU) {
          const ObjectCabinetFace = Object.cabinet_front
          const ObjectID = Object.id
          const ObjectPreviewData = vm.GetPreviewData(ObjectID)
          const TemplateMountConfig = ObjectPreviewData.mount_config

          if(ObjectCabinetFace == TemplateFaceSelected || TemplateMountConfig == "4-post") {
            return true
          }
        }
      })

      const RackObjectID = (ObjectIndex !== -1) ? vm.ObjectData[Context][ObjectIndex].id : false

      return RackObjectID
    },
    RUIsOccupied: function(CabinetID, CabinetRU) {
      
      // Store variables
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const CabinetObjects = vm.ObjectData[Context].filter((object) => object.cabinet_id == CabinetID);
      let ObjectIsPresent = false

      CabinetObjects.forEach(function(object){
        // Store object dependent variables
        const ObjectID = object.id
        const ObjectCabinetFace = object.cabinet_front
        const ObjectCabinetRU = object.cabinet_ru

        // Get template data
        const TemplateID = vm.GetTemplateID(ObjectID, Context)
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
        const Template = vm.TemplateData[Context][TemplateIndex]

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
    HandleDrop: function(CabinetID, CabinetFace, CabinetRU, event) {

      // Store data
      const vm = this
      const data = {
        "drop_type": "cabinet",
        "context": event.dataTransfer.getData('context'),
        "cabinet_id": CabinetID,
        "cabinet_face": CabinetFace,
        "cabinet_ru": CabinetRU,
        "object_id": event.dataTransfer.getData('object_id'),
        "template_id": event.dataTransfer.getData('template_id'),
        "template_face": event.dataTransfer.getData('template_face'),
      }

      vm.$emit('ObjectDropped', data )
    },
  }
}
</script>
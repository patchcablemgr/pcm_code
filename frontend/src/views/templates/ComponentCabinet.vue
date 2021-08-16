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
        class="pcm_cabinet_ru"
        v-if=" RackObjectID(CabinetData.id, CabinetRU) !== false "
        :rowspan=" RackObjectSize( RackObjectID(CabinetData.id, CabinetRU) ) "
      >
        <component-object
          :CabinetID="CabinetData.id"
          :CabinetRU="CabinetRU"
          :ObjectData="ObjectData"
          :PreviewData="PreviewData"
          :CategoryData="CategoryData"
          :TemplateRUSize=" RackObjectSize( RackObjectID(CabinetData.id, CabinetRU) ) "
          :InitialDepthCounter=" InitialDepthCounter "
          :Context="Context"
          :ObjectID="RackObjectID(CabinetData.id, CabinetRU)"
          :TemplateID="parseInt(0)"
          :TemplateFaceSelected="TemplateFaceSelected"
          :PartitionAddressSelected="PartitionAddressSelected"
          :PartitionAddressHovered="PartitionAddressHovered"
          @PartitionClicked=" $emit('PartitionClicked', $event) "
          @PartitionHovered=" $emit('PartitionHovered', $event) "
        />
      </td>
      <td class="pcm_cabinet_ru" v-else-if=" RUIsOccupied(CabinetData.id, CabinetRU) === false "></td>
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

const InitialDepthCounter = []

export default {
  components: {
    BContainer,
    BRow,
    BCol,

    ComponentObject,
  },
  props: {
    CabinetData: {type: Object},
    CategoryData: {type: Array},
    ObjectData: {type: Object},
    PreviewData: {type: Array},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  data() {
    return {
      InitialDepthCounter
    }
  },
  methods: {
    GetObjectIndex: function(ObjectID) {

      // Initial variables
      const vm = this
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);

      return ObjectIndex
    },
    GetPreviewData: function(ObjectID) {

      // Initial variables
      const vm = this
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.GetObjectIndex(ObjectID)

      // Get template index
      const TemplateID = vm.ObjectData[Context][ObjectIndex].template_id
      const TemplateIndex = vm.GetTemplateIndex(TemplateID)

      // Get template
      const ObjectPreviewData = vm.PreviewData[TemplateIndex]

      // Return template
      return ObjectPreviewData
    },
    GetTemplateIndex: function(TemplateID) {

      // Initial variables
      const vm = this;

      // Get object index
      const TemplateIndex = vm.PreviewData.findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    RackObjectID: function(CabinetID, CabinetRU) {
      
      // Initial variables
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]

      const ObjectIndex = vm.ObjectData[Context].findIndex(function(Object, ObjectIndex) {
        if(Object.location_id == CabinetID && Object.cabinet_ru == CabinetRU) {
          const ObjectCabinetFace = Object.cabinet_face
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
    RackObjectSize: function(ObjectID) {

      // Store variables
      const vm = this
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);

      // Get template index
      const TemplateID = vm.ObjectData[Context][ObjectIndex].template_id
      const TemplateIndex = vm.PreviewData.findIndex((template) => template.id == TemplateID);

      // Get template
      const ObjectPreviewData = vm.PreviewData[TemplateIndex]

      const ObjectSize = ObjectPreviewData.ru_size

      return ObjectSize
    },
    RUIsOccupied: function(CabinetID, CabinetRU) {
      
      // Store variables
      const vm = this;
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const CabinetObjects = vm.ObjectData[Context].filter((object) => object.location_id == CabinetID);
      let ObjectIsPresent = false

      CabinetObjects.forEach(function(object){
        // Store object dependent variables
        const ObjectID = object.id
        const ObjectCabinetFace = object.cabinet_face
        const ObjectCabinetRU = object.cabinet_ru

        // Get template data
        const PreviewData = vm.GetPreviewData(ObjectID)

        // Store template variables
        const TemplateSize = PreviewData.ru_size
        const TemplateMountConfig = PreviewData.mount_config

        const ObjectFirstRU = ObjectCabinetRU
        const ObjectLastRU = ObjectFirstRU + (TemplateSize - 1)

        // Determine if object is present at cabinet RU
        if(ObjectCabinetFace == TemplateFaceSelected || TemplateMountConfig == "4-post") {
          if(CabinetRU <= ObjectLastRU && CabinetRU >= ObjectFirstRU) {
            ObjectIsPresent = true
          }
        }
      });

      return ObjectIsPresent
    },
  }
}
</script>
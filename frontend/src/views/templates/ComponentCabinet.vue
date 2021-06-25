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
      <td class="pcm_cabinet_ru" v-if=" RackObjectID(CabinetData.id, CabinetFace, CabinetRU) !== false " :rowspan=" RackObjectSize( RackObjectID(CabinetData.id, CabinetFace, CabinetRU) ) ">
        <component-object
          :TemplateBlueprint=" ObjectTemplateData( RackObjectID(CabinetData.id, CabinetFace, CabinetRU) ).blueprint[CabinetFace] "
          :TemplateBlueprintOriginal=" ObjectTemplateData( RackObjectID(CabinetData.id, CabinetFace, CabinetRU) ).blueprint[CabinetFace] "
          :TemplateRUSize=" RackObjectSize( RackObjectID(CabinetData.id, CabinetFace, CabinetRU) ) "
          :InitialDepthCounter=" InitialDepthCounter "
          :SelectedPartitionAddress=" SelectedPartitionAddress "
          :CabinetFace=" CabinetFace "
          @PartitionClicked=" $emit('PartitionClicked', $event) "
          :style="{'background-color': ObjectCategoryData( RackObjectID(CabinetData.id, CabinetFace, CabinetRU) ).color}"
        />
      </td>
      <td class="pcm_cabinet_ru" v-else-if=" RUIsOccupied(CabinetData.id, CabinetFace, CabinetRU) === false "></td>
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

const InitialDepthCounter = ""

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
    ObjectData: {type: Array},
    TemplateData: {type: Array},
    CabinetFace: {type: String},
    SelectedPartitionAddress: {type: Object},
  },
  data() {
    return {
      InitialDepthCounter
    }
  },
  methods: {
    ObjectCategoryData: function(ObjectID) {

      // Initial variables
      const vm = this;

      // Get object index
      const ObjectIndex = vm.ObjectData.findIndex((object) => object.id == ObjectID);

      // Get template index
      const TemplateID = vm.ObjectData[ObjectIndex].template_id
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);

      // Get category index
      const ObjectCategoryID = vm.TemplateData[TemplateIndex].category_id
      const ObjectCategoryIndex = vm.CategoryData.findIndex((category) => category.id == ObjectCategoryID);

      // Get category
      const ObjectCategoryData = vm.CategoryData[ObjectCategoryIndex]

      // Return category
      return ObjectCategoryData
    },
    ObjectTemplateData: function(ObjectID) {

      // Initial variables
      const vm = this;

      // Get object index
      const ObjectIndex = vm.ObjectData.findIndex((object) => object.id == ObjectID);

      // Get template index
      const TemplateID = vm.ObjectData[ObjectIndex].template_id
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);

      // Get template
      const ObjectTemplateData = vm.TemplateData[TemplateIndex]

      // Return template
      return ObjectTemplateData
    },
    RackObjectID: function(CabinetID, CabinetFace, CabinetRU) {
      
      // Initial variables
      const vm = this;

      const ObjectIndex = vm.ObjectData.findIndex(function(Object, ObjectIndex) {
        if(Object.location_id == CabinetID && Object.cabinet_ru == CabinetRU) {
          const ObjectCabinetFace = Object.cabinet_face
          const ObjectID = Object.id
          const ObjectTemplateData = vm.ObjectTemplateData(ObjectID)
          const TemplateMountConfig = ObjectTemplateData.mount_config
          if(ObjectCabinetFace == CabinetFace || TemplateMountConfig == "4-post") {
            return true
          }
        }
      })

      const RackObjectID = (ObjectIndex !== -1) ? vm.ObjectData[ObjectIndex].id : false

      return RackObjectID
    },
    RackObjectSize: function(ObjectID) {

      // Store variables
      const vm = this

      // Get object index
      const ObjectIndex = vm.ObjectData.findIndex((object) => object.id == ObjectID);

      // Get template index
      const TemplateID = vm.ObjectData[ObjectIndex].template_id
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);

      // Get template
      const ObjectTemplateData = vm.TemplateData[TemplateIndex]

      const ObjectSize = ObjectTemplateData.ru_size

      return ObjectSize
    },
    RUIsOccupied: function(CabinetID, CabinetFace, CabinetRU) {
      
      // Store variables
      const vm = this;
      const CabinetObjects = vm.ObjectData.filter((object) => object.location_id == CabinetID);
      let ObjectIsPresent = false

      CabinetObjects.forEach(function(object){
        // Store object dependent variables
        const ObjectID = object.id
        const ObjectCabinetFace = object.cabinet_face
        const ObjectCabinetRU = object.cabinet_ru

        // Get template data
        const TemplateData = vm.ObjectTemplateData(ObjectID)

        // Store template variables
        const TemplateSize = TemplateData.ru_size
        const TemplateMountConfig = TemplateData.mount_config

        const ObjectFirstRU = ObjectCabinetRU
        const ObjectLastRU = ObjectFirstRU + (TemplateSize - 1)

        // Determine if object is present at cabinet RU
        if(ObjectCabinetFace == CabinetFace || TemplateMountConfig == "4-post") {
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
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
      <td class="pcm_cabinet_ru" v-if=" ObjectIsPresent(CabinetData.id, CabinetFace, CabinetRU) !== false " :rowspan="RackObjectSize(CabinetData.id, CabinetRU)">
        <component-object
          :TemplateBlueprint=" TemplateBlueprint(CabinetData.id, CabinetRU) "
          :TemplateRUSize=" TemplateRUSize(CabinetData.id, CabinetRU) "
          :InitialDepthCounter=" InitialDepthCounter "
        />
      </td>
      <td class="pcm_cabinet_ru" v-else-if=" RUIsOccupied(CabinetData.id, CabinetFace, CabinetRU) === false "></td>
      <!--td class="pcm_cabinet_ru" v-else ></td-->
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
    ObjectData: {type: Array},
    TemplateData: {type: Array},
    CabinetFace: {type: String},
  },
  data() {
    return {
      InitialDepthCounter
    }
  },
  methods: {
    TemplateRUSize(CabinetID, CabinetRU) {

      // Store variables
      const vm = this;
      let ObjectIndex = vm.RackObjectIndex(CabinetID, CabinetRU)
      let TemplateIndex = vm.TemplateIndex(ObjectIndex)
      let TemplateRUSize = vm.TemplateData[TemplateIndex].ru_size

      return TemplateRUSize
    },
    TemplateBlueprint(CabinetID, CabinetRU) {

      // Store variables
      const vm = this;
      let ObjectIndex = vm.RackObjectIndex(CabinetID, CabinetRU)
      let TemplateIndex = vm.TemplateIndex(ObjectIndex)
      let TemplateBlueprint = vm.TemplateData[TemplateIndex].blueprint[vm.CabinetFace]

      return TemplateBlueprint
    },
    RUIsOccupied(CabinetID, CabinetFace, CabinetRU) {
      
      // Store variables
      const vm = this;
      let CabinetObjects = vm.ObjectData.filter((object) => object.location_id == CabinetID);
      let ObjectIsPresent = false

      CabinetObjects.forEach(function(object){

        // Store object dependent variables
        let ObjectID = object.id
        let ObjectIndex = vm.ObjectData.findIndex((object) => object.id == ObjectID);
        let ObjectCabinetFace = vm.ObjectData[ObjectIndex].cabinet_face
        let TemplateIndex = vm.TemplateIndex(ObjectIndex)
        let TemplateSize = vm.TemplateData[TemplateIndex].ru_size
        let TemplateMountConfig = vm.TemplateData[TemplateIndex].mount_config
        let ObjectFirstRU = vm.ObjectData[ObjectIndex].cabinet_ru
        let ObjectLastRU = ObjectFirstRU + (TemplateSize - 1)

        // Determine if object is present at cabinet RU
        if(ObjectCabinetFace == CabinetFace || TemplateMountConfig == "4-post") {
          if(CabinetRU <= ObjectLastRU && CabinetRU >= ObjectFirstRU) {
            ObjectIsPresent = true
          }
        }
      });

      return ObjectIsPresent
    },
    ObjectIsPresent(CabinetID, CabinetFace, CabinetRU) {
      
      // Store variables
      const vm = this;
      let ObjectIndex = vm.RackObjectIndex(CabinetID, CabinetRU)
      let ObjectIsPresent = false

      if(ObjectIndex !== false) {

        // Stor object dependent variables
        let TemplateIndex = vm.TemplateIndex(ObjectIndex)
        let ObjectCabinetFace = vm.ObjectData[ObjectIndex].cabinet_face
        let TemplateMountConfig = vm.TemplateData[TemplateIndex].mount_config

        // Detmerine if object is present at cabinet RU
        if(ObjectCabinetFace == CabinetFace || TemplateMountConfig == "4-post") {
          ObjectIsPresent = true
        }
      }

      return ObjectIsPresent
    },
    RackObjectIndex(CabinetID, CabinetRU) {

      const vm = this;

      // Get index of object from ObjectData array
      const ObjectIndex = vm.ObjectData.findIndex((object) => object.location_id == CabinetID && object.cabinet_ru == CabinetRU);
      
      return (ObjectIndex > -1) ? ObjectIndex : false;
    },
    TemplateIndex(ObjectIndex) {

      const vm = this;

      // Get index of template from TemplateData array
      const TemplateID = vm.ObjectData[ObjectIndex].template_id;
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);

      return (TemplateIndex > -1) ? TemplateIndex : false;
    },
    RackObjectSize(CabinetID, CabinetRU) {

      // Store variables
      const vm = this
      let ObjectIndex = vm.RackObjectIndex(CabinetID, CabinetRU)
      let TemplateIndex = vm.TemplateIndex(ObjectIndex)
      let ObjectSize = vm.TemplateData[TemplateIndex].ru_size

      return ObjectSize
    },
  }
}
</script>
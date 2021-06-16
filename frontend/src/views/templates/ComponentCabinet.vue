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
      <td class="pcm_cabinet_ru" v-if=" RackObjectID(CabinetData.id, CabinetFace, CabinetRU) !== false " :rowspan=" RackObjectSize(CabinetData.id, CabinetFace, CabinetRU) ">
        <component-object
          :TemplateBlueprint=" TemplateBlueprint(CabinetData.id, CabinetFace, CabinetRU) "
          :TemplateRUSize=" RackObjectSize(CabinetData.id, CabinetFace, CabinetRU) "
          :InitialDepthCounter=" InitialDepthCounter "
          :SelectedPartitionAddress=" SelectedPartitionAddress "
          :CabinetFace=" CabinetFace "
          @PartitionClicked=" $emit('PartitionClicked', $event) "
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
    SelectedPartitionAddress: {type: Object},
  },
  data() {
    return {
      InitialDepthCounter
    }
  },
  methods: {
    RackObjectID: function(CabinetID, CabinetFace, CabinetRU) {
      
      // Store variables
      const vm = this;
      const ObjectIndex = vm.ObjectData.findIndex((object) => object.location_id == CabinetID && object.cabinet_ru == CabinetRU);
      let RackObjectID = false

      if(ObjectIndex !== -1) {

        // Store object dependent variables
        const Object = vm.ObjectData[ObjectIndex]
        const ObjectID = Object.id
        const TemplateID = Object.template_id;
        const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);
        const Template = vm.TemplateData[TemplateIndex]
        const ObjectCabinetFace = Object.cabinet_face
        const TemplateMountConfig = Template.mount_config

        // Detmerine if object is present at cabinet RU
        if(ObjectCabinetFace == CabinetFace || TemplateMountConfig == "4-post") {
          RackObjectID = ObjectID
        }
      }

      return RackObjectID
    },
    RackObjectSize: function(CabinetID, CabinetFace, CabinetRU) {

      // Store variables
      const vm = this
      const ObjectID = vm.RackObjectID(CabinetID, CabinetFace, CabinetRU)
      const TemplateID = vm.ObjectData[ObjectID].template_id;
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);
      const ObjectSize = vm.TemplateData[TemplateIndex].ru_size

      return ObjectSize
    },
    TemplateBlueprint: function(CabinetID, CabinetFace, CabinetRU) {

      // Store variables
      const vm = this;
      const ObjectID = vm.RackObjectID(CabinetID, CabinetFace, CabinetRU)
      const TemplateID = vm.ObjectData[ObjectID].template_id
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);
      const TemplateBlueprint = vm.TemplateData[TemplateIndex].blueprint[CabinetFace]

      return TemplateBlueprint
    },
    RUIsOccupied: function(CabinetID, CabinetFace, CabinetRU) {
      
      // Store variables
      const vm = this;
      const CabinetObjects = vm.ObjectData.filter((object) => object.location_id == CabinetID);
      let ObjectIsPresent = false

      CabinetObjects.forEach(function(object){

        // Store object dependent variables
        const ObjectID = object.id
        const ObjectCabinetFace = vm.ObjectData[ObjectID].cabinet_face
        const TemplateID = vm.ObjectData[ObjectID].template_id
        const TemplateSize = vm.TemplateData[TemplateID].ru_size
        const TemplateMountConfig = vm.TemplateData[TemplateID].mount_config
        const ObjectFirstRU = vm.ObjectData[ObjectID].cabinet_ru
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
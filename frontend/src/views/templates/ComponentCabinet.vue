<template>
  <!-- Template categories modal -->
  <b-container>
    <b-row>
      <b-col class="pcm_cabinet_structure">
        {{ CabinetData.name }}
      </b-col>
    </b-row>
    <b-row
      v-for="CabinetRU in CabinetData.size"
      :key="CabinetRU"
      :CabinetID="CabinetData.id"
    >
      <b-col class="pcm_cabinet_structure" cols=1>{{ CabinetRU }}</b-col>
      <b-col>
        <b-container v-if=" RackObjectIndex(CabinetID, CabinetRU) > -1 ">
          <component-object
            :TemplateData="TemplateData"
            :ObjectData="ObjectData"
            :RackObjectIndex="RackObjectIndex(CabinetID, CabinetRU)"
            :TemplateIndex="TemplateIndex(RackObjectIndex(CabinetID, CabinetRU))"
          />
        </b-container>
      </b-col>
      <b-col class="pcm_cabinet_structure" cols=1>{{ CabinetRU }}</b-col>
    </b-row>
    <b-row>
      <b-col class="pcm_cabinet_structure">
        {{ CabinetData.name }}
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import ComponentObject from './ComponentObject.vue'

export default {
  components: {
    BContainer,
    BRow,
    BCol,

    ComponentObject,
  },
  data() {
    return {
    }
  },
  props: {
    CabinetData: {type: Object},
    ObjectData: {type: Array},
    TemplateData: {type: Array},
    CabinetFace: {type: String},
  },
  methods: {
    RackObjectIndex(CabinetID, CabinetRU) {

      const vm = this;

      // Get index of object from ObjectData array
      const ObjectIndex = vm.ObjectData.findIndex((object) => object.location_id == CabinetID && object.cabinet_ru == CabinetRU);
      console.log(ObjectIndex);
      return (ObjectIndex > -1) ? ObjectIndex : false;
    },
    TemplateIndex(ObjectIndex) {

      const vm = this;

      console.log(JSON.stringify(vm.ObjectData[ObjectIndex]));

      // Get index of template from TemplateData array
      const TemplateID = vm.ObjectData[ObjectIndex].template_id;
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);

      return (TemplateIndex > -1) ? TemplateIndex : false;
    },
  },
  mounted() {
    console.log(JSON.stringify(this.ObjectData));
  }
}
</script>
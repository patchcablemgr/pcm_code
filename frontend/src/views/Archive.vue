<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid="xs">
      <b-row
        cols="1"
        cols-md="2"
        cols-xl="3"
      >
        <b-col>

          <card-archive-archive/>

        </b-col>

        <b-col>

          <card-archive-convert/>

        </b-col>
      </b-row>
    </b-container>

  </div>
</template>

<script>
import {
  BContainer,
  BRow,
  BCol,
  BCard,
  BCardBody,
  BButton,
  VBTooltip,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import ToastGeneral from '@/views/templates/ToastGeneral.vue'
import { PCM } from '@/mixins/PCM.js'
import CardArchiveArchive from '@/views/templates/CardArchiveArchive.vue'
import CardArchiveConvert from '@/views/templates/CardArchiveConvert.vue'

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BButton,

    ToastGeneral,
    CardArchiveArchive,
    CardArchiveConvert,
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
	},
  data() {
    return {
      Context: 'actual',
    }
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.ObjectsReady.actual,
        vm.ConnectionsReady,
        vm.TemplatesReady.actual,
        vm.CategoriesReady.actual,
        vm.LocationsReady.actual,
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
    OrganizationReady: function() {
      return this.$store.state.pcmOrganization.OrganizationReady
    },
    ConnectionsReady: function() {
      return this.$store.state.pcmConnections.ConnectionsReady
    },
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    CategoriesReady: function() {
      return this.$store.state.pcmCategories.CategoriesReady
    },
    LocationsReady: function() {
      return this.$store.state.pcmLocations.LocationsReady
    },
  },
  methods: {},
  mounted() {},
}
</script>

<style>

</style>

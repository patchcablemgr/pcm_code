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

          <b-card
            title="Port Utilization"
          >
            <b-card-body>

              <!-- Download -->
              <b-button
                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                variant="primary"
                @click="Download()"
                
              >
                Download
              </b-button>

              <!-- Upload -->
              <b-button
                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                variant="primary"
                v-b-modal.modal-file-upload-archive
              >
                Upload
              </b-button>

            </b-card-body>
          </b-card>

        </b-col>
      </b-row>
    </b-container>

    <!-- Modal File Upload Archive -->
    <modal-file-upload-archive
      ModalTitle="Upload Archive"
      ModalID="modal-file-upload-archive"
    />

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
import ModalFileUploadArchive from '@/views/templates/ModalFileUploadArchive.vue'
import { PCM } from '@/mixins/PCM.js'

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
    ModalFileUploadArchive
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
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
    Organization() {
      return this.$store.state.pcmOrganization.Organization
    },
    OrganizationReady: function() {
      return this.$store.state.pcmOrganization.OrganizationReady
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    ConnectionsReady: function() {
      return this.$store.state.pcmConnections.ConnectionsReady
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    CategoriesReady: function() {
      return this.$store.state.pcmCategories.CategoriesReady
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    LocationsReady: function() {
      return this.$store.state.pcmLocations.LocationsReady
    },
  },
  methods: {
    Download() {

      const vm = this
      
      //https://stackoverflow.com/questions/53772331/vue-html-js-how-to-download-a-file-to-browser-using-the-download-tag
      //https://stackoverflow.com/questions/57437531/how-do-i-download-a-zip-with-multiple-types-of-files-with-axios
      vm.$http.get('/api/archive', {responseType: 'blob'}).then(response  => {
        const blob = new Blob([response.data], { type: 'application/zip' })
        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = 'pcmExport-'+Date.now()
        link.click()
        URL.revokeObjectURL(link.href)
      })
    },
  },
  mounted() {},
}
</script>

<style>

</style>

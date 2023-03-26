<template>
    <!-- Template port Format modal -->
    <b-modal
      :id="ModalID"
      :title="ModalTitle"
      ok-only
      ok-title="OK"
      @ok="FileSubmitted()"
    >
      <b-card
        title="Select a File"
        class="position-static"
      >
        <!-- Styled -->
        <b-form-file
          v-model="File"
          placeholder="Choose a file or drop it here..."
          drop-placeholder="Drop file here..."
        />

        <b-card-text class="my-1">
          Selected file: <strong>{{ File ? File.name : '' }}</strong>
        </b-card-text>
      </b-card>
    </b-modal>
</template>

<script>
import {
  BRow,
  BCol,
  BCard,
  BCardText,
  BFormFile,
  VBTooltip,
} from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
    BRow,
    BCol,
    BCard,
    BCardText,
    BFormFile,
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  props: {
    ModalTitle: {type: String},
    ModalID: {type: String}
  },
  data() {
    return {
      File: null,
    }
  },
  computed: {
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
  },
  methods: {
    FileSelected: function(event) {

      const vm = this
      vm.File = event.target.files[0]
    },
    FileSubmitted: function() {

      const vm = this

      const url = '/api/archive/convert'
      let data = new FormData()
      const options = {
        'headers': {
          'Content-Type': 'multipart/form-data'
        },
        'responseType': 'blob',
      }
      data.append('file', vm.File)

      // POST floorplan image
      vm.$http.post(url, data, options).then(function(response){
        const blob = new Blob([response.data], { type: 'application/zip' })
        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = 'pcmExport-'+Date.now()
        link.click()
        URL.revokeObjectURL(link.href)
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  },
  mounted() {
  }
}
</script>

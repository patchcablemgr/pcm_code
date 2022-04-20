<template>
    <!-- Template port Format modal -->
    <b-modal
      id="modal-cert-upload"
      title="Upload"
      ok-only
      ok-title="OK"
      @ok="FileSubmitted()"
    >
      <b-card
        title="SSL Certificate"
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
    SelectedCSRID: {type: Number},
  },
  data() {
    return {
      File: null,
    }
  },
  computed: {},
  methods: {
    FileSubmitted: function() {

      const vm = this
      const CSRID = vm.SelectedCSRID
      const url = '/api/config/csr/'+CSRID+'/cert'
      let data = new FormData()
      const options = {
        'headers': {
          'Content-Type': 'multipart/form-data'
        }
      }
      data.append('file', vm.File)

      // POST floorplan image
      vm.$http.post(url, data, options).then(function(response){
        vm.$store.commit('pcmSSL/ADD_Cert', {data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  },
  mounted() {
  }
}
</script>
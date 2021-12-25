<template>
    <!-- Template port Format modal -->
    <b-modal
      id="modal-file-upload"
      :title="Title"
      ok-only
      ok-title="OK"
      @ok="FileSubmitted()"
    >
      <b-card
        title="Port Format"
        class="position-static"
      >
        <!-- Styled -->
        <b-form-file
          @change="FileSelected($event)"
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
    Title: {type: String},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
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
  },
  methods: {
    FileSelected: function(event) {

      const vm = this
      vm.File = event.target.files[0]
    },
    FileSubmitted: function() {

      const vm = this
      const Context = vm.Context
      const Template = vm.GetTemplateSelected(Context)
      const Face = vm.TemplateFaceSelected[Context]
      const TemplateID = Template.id
      const url = '/api/templates/'+TemplateID+'/image'
      let data = new FormData()
      const options = {
        'headers': {
          'Content-Type': 'multipart/form-data'
        }
      }
      data.append('file', vm.File)
      data.append('face', Face)

      // POST floorplan image
      vm.$http.post(url, data, options).then(function(response){
        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:'template', data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  },
  mounted() {
  }
}
</script>
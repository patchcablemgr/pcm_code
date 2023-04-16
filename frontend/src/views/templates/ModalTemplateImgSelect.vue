<template>
    <!-- Template port Format modal -->
    <b-modal
      :id="ModalID"
      :title="ModalTitle"
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

      <template #modal-footer>
        <b-button
          variant="secondary"
          class="float-right"
          @click="Cancel"
        >
          Cancel
        </b-button>
        <b-button
          variant="danger"
          class="float-right"
          @click="Clear"
        >
          Clear
        </b-button>
        <b-button
          variant="primary"
          class="float-right"
          @click="FileSubmitted"
        >
          Submit
        </b-button>
      </template>
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
  BButton,
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
    BButton,
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  props: {
    ModalID: {type: String},
    ModalTitle: {type: String},
    UploadType: {type: String},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
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
    Cancel: function() {

      const vm = this

      // Close modal
      vm.$root.$emit('bv::hide::modal', vm.ModalID)
    },
    Clear: function() {

      const vm = this

      // Close modal
      vm.$emit('file-selected', null)

      // Close modal
      vm.$root.$emit('bv::hide::modal', vm.ModalID)
    },
    FileSelected: function(event) {

      const vm = this
      vm.File = event.target.files[0]
    },
    FileSubmitted: function() {

      const vm = this
      const Context = vm.Context

      if(vm.File) {
        const reader = new FileReader()

        let rawImg;
        reader.onloadend = () => {
          rawImg = reader.result;
          vm.$emit('file-selected', rawImg)
          
          // Close modal
          vm.$root.$emit('bv::hide::modal', vm.ModalID)
        }
        reader.readAsDataURL(vm.File);
      }
    },
  },
  mounted() {
  }
}
</script>
<!-- Template/Object Details -->

<template>
<div>
  <b-card
    title="Convert Legacy Archive"
  >
    <b-card-body>

      <!-- Upload -->
      <b-button
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
        v-b-modal.modal-archive-upload-archive-legacy
      >
        Upload
      </b-button>

    </b-card-body>
  </b-card>

  <!-- Modal File Upload Archive -->
  <modal-archive-upload-archive-legacy
    ModalTitle="Upload Legacy Archive"
    ModalID="modal-archive-upload-archive-legacy"
  />

</div>
</template>

<script>
import {
  BCard,
  BCardBody,
  BButton,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalArchiveUploadArchiveLegacy from '@/views/templates/ModalArchiveUploadArchiveLegacy.vue'

export default {
  mixins: [PCM],
  components: {
    BCard,
    BCardBody,
    BButton,

    ModalArchiveUploadArchiveLegacy,
  },
	directives: {
    Ripple,
	},
  props: {},
  data() {
    return {}
  },
  computed: {},
  methods: {}
}
</script>
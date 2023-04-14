<!-- Template/Object Details -->

<template>
<div>
  <b-card>
    <b-card-title>
      <h4>
        Convert Legacy Archive
        <feather-icon
          icon="HelpCircleIcon"
          v-b-tooltip.hover.html="ToolTipCardTitle"
        />
      </h4>
    </b-card-title>
    <b-card-body>

      <!-- Upload -->
      <b-button
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
        v-b-modal.modal-archive-upload-archive-legacy
      >
        <b-spinner
          v-if="UploadBtnLoading"
          small
        />
        Upload
      </b-button>

    </b-card-body>
  </b-card>

  <!-- Modal File Upload Archive -->
  <modal-archive-upload-archive-legacy
    ModalTitle="Upload Legacy Archive"
    ModalID="modal-archive-upload-archive-legacy"
    @upload-start="UploadStart"
    @upload-stop="UploadStop"
  />

</div>
</template>

<script>
import {
  BCard,
  BCardTitle,
  BCardBody,
  BButton,
  VBTooltip,
  BSpinner,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalArchiveUploadArchiveLegacy from '@/views/templates/ModalArchiveUploadArchiveLegacy.vue'

const ToolTipCardTitle = {
  title: `
    <div class="text-left">
      <div>
        Convert legacy archives to be imported.<br><br>Note:
        <br>
        Legacy archive must be version 0.3.19.
      </div>
    </div>
  `
}

export default {
  mixins: [PCM],
  components: {
    BCard,
    BCardTitle,
    BCardBody,
    BButton,
    VBTooltip,
    BSpinner,

    ModalArchiveUploadArchiveLegacy,
  },
	directives: {
    Ripple,
    'b-tooltip': VBTooltip,
	},
  props: {},
  data() {
    return {
      ToolTipCardTitle,
      UploadBtnLoading:false,
    }
  },
  computed: {},
  methods: {
    UploadStart: function() {
      this.UploadBtnLoading = true
    },
    UploadStop: function() {
      this.UploadBtnLoading = false
    }
  }
}
</script>
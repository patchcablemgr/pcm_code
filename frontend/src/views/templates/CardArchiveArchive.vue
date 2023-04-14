<!-- Template/Object Details -->

<template>
<div>
  <b-card>
    <b-card-title>
      <h4>
        Archive
        <feather-icon
          icon="HelpCircleIcon"
          v-b-tooltip.hover.html="ToolTipCardTitle"
        />
      </h4>
    </b-card-title>
    <b-card-body>

      <!-- Download -->
      <b-button
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
        @click="Download()"
        
      >
        Export
      </b-button>

      <!-- Upload -->
      <b-button
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
        v-b-modal.modal-archive-upload-archive
      >
        Import
      </b-button>

    </b-card-body>
  </b-card>

  <!-- Modal File Upload Archive -->
  <modal-file-upload-archive
    ModalTitle="Upload Archive"
    ModalID="modal-archive-upload-archive"
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
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalFileUploadArchive from '@/views/templates/ModalArchiveUploadArchive.vue'

const ToolTipCardTitle = {
  title: `
    <div class="text-left">
      <div>
        Export an archive to backup data.  Import an archive to restore data.
        <br><br>
        Note:
        <br>
        Import destroys data that is not present in the archive.
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

    ModalFileUploadArchive,
  },
	directives: {
    Ripple,
    'b-tooltip': VBTooltip,
	},
  props: {},
  data() {
    return {
      ToolTipCardTitle,
    }
  },
  computed: {},
  methods: {
    Download() {

      const vm = this
      
      //https://stackoverflow.com/questions/53772331/vue-html-js-how-to-download-a-file-to-browser-using-the-download-tag
      //https://stackoverflow.com/questions/57437531/how-do-i-download-a-zip-with-multiple-types-of-files-with-axios
      vm.$http.get('/api/archive', {responseType: 'blob'}).then(response  => {
        const blob = new Blob([response.data], { type: 'application/zip' })
        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = 'pcmArchive-'+Date.now()
        link.click()
        URL.revokeObjectURL(link.href)
      })
    },
  }
}
</script>
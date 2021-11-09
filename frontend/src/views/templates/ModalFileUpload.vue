<template>
    <!-- Template port Format modal -->
    <b-modal
      id="modal-file-upload"
      title="Port ID"
      ok-only
      ok-title="OK"
      @ok=" $emit('FileSubmitted') "
    >
      <b-card
        title="Port Format"
        class="position-static"
      >
        <!-- Styled -->
        <b-form-file
          @change=" $emit('FileSelected', {'FileEvent': $event}) "
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

export default {
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
    File: {},
  },
  data() {
    return {
    }
  },
  computed: {
  },
  methods: {
  },
  mounted() {
  }
}
</script>
<!-- Template/Object Details -->

<template>
  <div class="demo-inline-spacing">

    <b-button
      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
      variant="primary"
      class="mr-1"
      @click="Update"
    >
      Update
    </b-button>

  </div>
</template>

<script>
import {
  BTable,
  BRow,
  BCol,
  BForm,
  BFormInput,
  BFormGroup,
  BFormCheckbox,
  BFormSelect,
  BButton,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
    BTable,
    BRow,
    BCol,
    BForm,
    BFormInput,
    BFormGroup,
    BFormCheckbox,
    BFormSelect,
    BButton,
  },
	directives: {
    Ripple,
	},
  props: {},
  data() {
    return {}
  },
  computed: {},
  methods: {
    Update(){

      const vm = this
      /*
      const URL = '/api/config/csr'

      vm.$http.post(URL).then(response => {
        console.log(response)
      }).catch(error => {
        vm.DisplayError(error)
      })
      */
    },
  }
}
</script>
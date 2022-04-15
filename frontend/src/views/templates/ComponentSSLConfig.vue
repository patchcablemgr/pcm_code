<!-- Template/Object Details -->

<template>
  <b-form @submit="Submit">
    <b-row>
      
      <!-- submit and reset -->
      <b-col offset-md="4">
        <b-button
          v-ripple.400="'rgba(255, 255, 255, 0.15)'"
          type="submit"
          variant="primary"
          class="mr-1"
        >
          Generate CSR
        </b-button>
      </b-col>
    </b-row>

  </b-form>
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
  props: {
    CSRList: {type: Array},
  },
  data() {
    return {}
  },
  computed: {},
  methods: {
    Submit(event){

      event.preventDefault()

      const vm = this
      const URL = '/api/config/csr'

      vm.$http.post(URL).then(response => {
        console.log(response)
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  }
}
</script>
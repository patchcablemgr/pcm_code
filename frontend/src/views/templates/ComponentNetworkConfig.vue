<!-- Template/Object Details -->

<template>
  <b-form @submit="Submit">
    <b-row>

      <b-col cols="12">
        <b-form-group
          label="DHCP"
          label-for="h-dhcp"
          label-cols-md="4"
        >
          <b-form-checkbox
            id="checkbox-2"
            name="checkbox-2"
            v-model="NetworkConfig.dhcp"
          />
        </b-form-group>
      </b-col>

      <b-col cols="12"
        v-show="!NetworkConfig.dhcp"
      >
        <b-form-group
          label="Host Address"
          label-for="h-host-address"
          label-cols-md="4"
        >
          <b-form-input
            id="h-host-address"
            placeholder="10.0.0.2/24"
            v-model="NetworkConfig.host_address"
          />
        </b-form-group>
      </b-col>

      <b-col cols="12"
        v-show="!NetworkConfig.dhcp"
      >
        <b-form-group
          label="Gateway"
          label-for="h-gateway"
          label-cols-md="4"
        >
          <b-form-input
            id="h-gateway"
            placeholder="10.0.0.1"
            v-model="NetworkConfig.gateway"
          />
        </b-form-group>
      </b-col>

      <b-col cols="12"
        v-show="!NetworkConfig.dhcp"
      >
        <b-form-group
          label="DNS"
          label-for="h-ds"
          label-cols-md="4"
        >
          <b-form-input
            id="h-dns"
            placeholder="8.8.8.8"
            v-model="NetworkConfig.dns"
          />
        </b-form-group>
      </b-col>
      

      <!-- submit and reset -->
      <b-col offset-md="4">
        <b-button
          v-ripple.400="'rgba(255, 255, 255, 0.15)'"
          type="submit"
          variant="primary"
          class="mr-1"
        >
          Submit
        </b-button>
        <b-button
          v-ripple.400="'rgba(186, 191, 199, 0.15)'"
          type="reset"
          variant="outline-secondary"
        >
          Reset
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

/*
const NetworkConfig = {
  'dhcp': false,
  'host_address': '',
  'gateway': '',
  'dns': '',
}
*/

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
    NetworkConfig: {type: Object},
  },
  data() {
    return {
      //NetworkConfig,
    }
  },
  computed: {
    Users() {
      return this.$store.state.pcmUsers.Users
    },
  },
  methods: {
    Submit(event){

      event.preventDefault()

      const vm = this
      const data = vm.NetworkConfig
      const URL = '/api/config/network'

      vm.$http.post(URL, data).then(response => {
        console.log(response)
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  }
}
</script>
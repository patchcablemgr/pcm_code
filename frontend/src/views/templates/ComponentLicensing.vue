<!-- Template/Object Details -->

<template>
<div>
  <b-card>
    <b-card-title>
      <div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing">
          Licensing
        </div>
        <div class="demo-inline-spacing">
          <b-dropdown
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            right
            size="sm"
            text="Actions"
            variant="primary"
          >

            <b-dropdown-item
              @click="RefreshSubscription"
            >
              Refresh
            </b-dropdown-item>

            <b-dropdown-item
              @click="ManageSubscription"
            >
              Manage
            </b-dropdown-item>

          </b-dropdown>
        </div>
      </div>
    </b-card-title>

    <b-card-body>
      <table>

        <!-- License Key -->
        <tr>
          <td class="text-right">
            <strong>License Key:</strong>
          </td>
          <td>
            <b-button
              v-ripple.400="'rgba(40, 199, 111, 0.15)'"
              variant="flat-success"
              class="btn-icon"
              v-b-modal.modal-edit-license-key
            >
              <feather-icon icon="EditIcon" />
            </b-button>
          </td>
          <td>
            abc123
          </td>
        </tr>

        <!-- Status -->
        <tr>
          <td class="text-right">
            <strong>Status:</strong>
          </td>
          <td>
          </td>
          <td>
            Active
          </td>
        </tr>

        <!-- Expiration -->
        <tr>
          <td class="text-right">
            <strong>Expiration:</strong>
          </td>
          <td>
          </td>
          <td>
            20230101
          </td>
        </tr>

      </table>
    </b-card-body>
  </b-card>

  <!-- Modal Edit License Key -->
  <modal-edit-license-key
    ModalTitle="License Key"
  />
</div>
</template>

<script>
import {
  BCard,
  BCardTitle,
  BCardBody,
  BDropdown,
  BDropdownItem,
  BButton,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalEditLicenseKey from './ModalEditLicenseKey.vue'

export default {
  mixins: [PCM],
  components: {
    BCard,
    BCardTitle,
    BCardBody,
    BDropdown,
    BDropdownItem,
    BButton,

    ModalEditLicenseKey,
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
    RefreshSubscription(){

      console.log('refresh')
      /*
      const vm = this
      
      const URL = '/api/config/app/update'

      vm.$http.post(URL).then(response => {
        console.log(response)
      }).catch(error => {
        vm.DisplayError(error)
      })
      */
    },
    ManageSubscription(){

      console.log('manage')
      /*
      const vm = this
      
      const URL = '/api/config/app/update'

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
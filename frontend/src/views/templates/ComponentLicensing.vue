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
            {{ Organization.license_key }}
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
            {{ Organization.license_data.status }}
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
            {{ Expiration }}
          </td>
        </tr>

        <!-- Last Checked -->
        <tr>
          <td class="text-right">
            <strong>Last Checked:</strong>
          </td>
          <td>
          </td>
          <td>
            {{ LastChecked }}
          </td>
        </tr>

        <!-- Entitlement Cabinet -->
        <tr>
          <td class="text-right">
            <strong>Cabinets:</strong>
          </td>
          <td>
          </td>
          <td>
            {{ (Organization.license_data.entitlement.cabinet == 0) ? "Unlimited" : Organization.license_data.entitlement.cabinet }} (<small>{{ CabinetCount }} used</small>)
          </td>
        </tr>

        <!-- Entitlement Objects -->
        <tr>
          <td class="text-right">
            <strong>Objects:</strong>
          </td>
          <td>
          </td>
          <td>
            {{ (Organization.license_data.entitlement.object == 0) ? "Unlimited" : Organization.license_data.entitlement.object }} (<small>{{ ObjectCount }} used</small>)
          </td>
        </tr>

        <!-- Entitlement Connections -->
        <tr>
          <td class="text-right">
            <strong>Connections:</strong>
          </td>
          <td>
          </td>
          <td>
            {{ (Organization.license_data.entitlement.connection == 0) ? "Unlimited" : Organization.license_data.entitlement.connection }} (<small>{{ ConnectionCount }} used</small>)
          </td>
        </tr>

        <!-- Entitlement Users -->
        <tr>
          <td class="text-right">
            <strong>Users:</strong>
          </td>
          <td>
          </td>
          <td>
            {{ (Organization.license_data.entitlement.user == 0) ? "Unlimited" : Organization.license_data.entitlement.user }} (<small>{{ UserCount }} used</small>)
          </td>
        </tr>

      </table>
    </b-card-body>
  </b-card>

  <!-- Modal Edit License Key -->
  <modal-edit-license-key
    ModalTitle="License Key"
    :LicenseKey="LicenseKey"
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
  computed: {
    Users: function() {
      return this.$store.state.pcmUsers.Users
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    Organization() {
      return this.$store.state.pcmOrganization.Organization
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    LicenseKey: {
      get() {

        const vm = this
        const LicenseKey = vm.Organization.license_key
        return (LicenseKey) ? LicenseKey : 'None'

      },
      set(newValue) {
        return false
      }
    },
    Expiration: {
      get() {

        const vm = this
        const Expiration = vm.Organization.license_data.expiration
        if(Expiration === null) {
          return "N/A"
        } else {
          return vm.ConvertTimestampToDate(Expiration)
        }

      },
      set(newValue) {
        return false
      }
    },
    ObjectCount: {
      get() {

        const vm = this
        const ObjectCount = vm.Objects.actual.length
        return ObjectCount

      },
      set(newValue) {
        return false
      }
    },
    CabinetCount: {
      get() {

        const vm = this
        const Cabinets = vm.Locations.actual.filter(location => location.type == 'cabinet')
        const CabinetCount = Cabinets.length
        return CabinetCount

      },
      set(newValue) {
        return false
      }
    },
    UserCount: {
      get() {

        const vm = this
        const ActiveUsers = vm.Users.filter(user => user.status)
        const UserCount = ActiveUsers.length
        return UserCount

      },
      set(newValue) {
        return false
      }
    },
    ConnectionCount: {
      get() {

        const vm = this
        const ConnectionCount = vm.Connections.length
        return ConnectionCount

      },
      set(newValue) {
        return false
      }
    },
    LastChecked: {
      get() {

        const vm = this
        const Expiration = vm.Organization.license_last_checked
        if(Expiration === null) {
          return "Never"
        } else {
          return vm.ConvertTimestampToDate(Expiration)
        }

      },
      set(newValue) {
        return false
      }
    }
  },
  methods: {
    RefreshSubscription(){

      const vm = this
      
      const URL = '/api/organization/license'

      vm.$http.get(URL).then(response => {
        vm.$store.commit('pcmOrganization/SET_Organization', {data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
      
    },
    ManageSubscription(){

      const vm = this
      
      const URL = '/api/organization/license/portal'

      vm.$http.get(URL).then(response => {
        console.log(response.data.url)
        window.open(response.data.url, '_blank');
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  },
  mounted() {},
}
</script>
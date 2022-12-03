<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid="xs">
      <b-row>

        <!-- Users -->
        <b-col>
          <b-card
            title="Users"
          >
            <b-card-body>
              <component-users/>
            </b-card-body>
          </b-card>
        </b-col>

        <!-- App Control -->
        <b-col>
          <component-app-control/>
        </b-col>

        <!-- Licensing -->
        <b-col>
          <component-licensing/>
        </b-col>

      </b-row>
      <b-row>

        <!-- SSL Config -->
        <b-col>
          <component-s-s-l-config/>
        </b-col>

        <!-- Network Config -->
        <b-col>
          <b-card
            title="Network Config"
          >
            <b-card-body>
              <component-network-config
                :NetworkConfig="NetworkConfig"
              />
            </b-card-body>
          </b-card>
        </b-col>

      </b-row>
    </b-container>

    <!-- Toast -->
    <toast-general/>

  </div>
</template>

<script>
import {
  BContainer,
  BRow,
  BCol,
  BCard,
  BCardTitle,
  BCardBody,
  BCardText,
  BFormRadio,
  BDropdown,
  BDropdownItem,
  BDropdownDivider,
  BButton,
  VBTooltip,
  BForm,
  BFormInput,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import ToastGeneral from './templates/ToastGeneral.vue'
import { PCM } from '@/mixins/PCM.js'
import ComponentUsers from '@/views/templates/ComponentUsers.vue'
import ComponentNetworkConfig from '@/views/templates/ComponentNetworkConfig.vue'
import ComponentSSLConfig from '@/views/templates/ComponentSSLConfig.vue'
import ComponentAppControl from '@/views/templates/ComponentAppControl.vue'
import ComponentLicensing from '@/views/templates/ComponentLicensing.vue'

const NetworkConfig = {
  'dhcp': false,
  'host_address': '',
  'gateway': '',
  'dns': '',
}

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardTitle,
    BCardBody,
    BDropdown,
		BDropdownItem,
		BDropdownDivider,
    BCardText,
    BFormRadio,
    BButton,
    BForm,
    BFormInput,

    ToastGeneral,
    ComponentUsers,
    ComponentNetworkConfig,
    ComponentSSLConfig,
    ComponentAppControl,
    ComponentLicensing,
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
	},
  data() {
    return {
      NetworkConfig,
    }
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.ConnectionsReady,
        vm.LocationsReady.actual,
        vm.UsersReady,
        vm.OrganizationReady,
        vm.ObjectsReady.actual,
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    ConnectionsReady: function() {
      return this.$store.state.pcmConnections.ConnectionsReady
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    LocationsReady: function() {
      return this.$store.state.pcmLocations.LocationsReady
    },
    UsersReady: function() {
      return this.$store.state.pcmUsers.UsersReady
    },
    Users: function() {
      return this.$store.state.pcmUsers.Users
    },
    Organization() {
      return this.$store.state.pcmOrganization.Organization
    },
    OrganizationReady: function() {
      return this.$store.state.pcmOrganization.OrganizationReady
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
  },
  methods: {
    GETUsers() {

      const vm = this

      // GET users
      vm.$http.get('/api/users').then(response => {
        vm.$store.commit('pcmUsers/SET_Users', {data:response.data})
        vm.$store.commit('pcmUsers/SET_Ready', {ReadyState:true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETOrganization() {

      const vm = this
      
      vm.$http.get('/api/organization').then(function(response){

        vm.$store.commit('pcmOrganization/SET_Organization', {data: response.data})
        vm.$store.commit('pcmOrganization/SET_Ready', {ReadyState:true})
      })
    },
    GETNetworkConfig() {

      const vm = this

      // GET network config
      vm.$http.get('/api/config/network').then(response => {
        vm.NetworkConfig = response.data
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETCSRList() {

      const vm = this

      // GET network config
      vm.$http.get('/api/config/csr').then(response => {
        vm.$store.commit('pcmSSL/SET_CSR', {data:response.data})
        vm.$store.commit('pcmSSL/SET_CSR_Ready', {ReadyState:true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GETCertList() {

      const vm = this

      // GET network config
      vm.$http.get('/api/config/cert').then(response => {
        vm.$store.commit('pcmSSL/SET_Cert', {data:response.data})
        vm.$store.commit('pcmSSL/SET_Cert_Ready', {ReadyState:true})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  },
  watch: {},
  mounted() {

    const vm = this

    vm.GETUsers()
    vm.GETOrganization()
    vm.GETNetworkConfig()
    vm.GETCSRList()
    vm.GETCertList()
  },
}
</script>

<style>

</style>

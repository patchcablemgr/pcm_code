<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid="xs">
      <b-row>
        <b-col>

          <b-card
            title="Users"
          >
            <b-card-body>
              <component-users/>
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
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
	},
  data() {
    return {}
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.UsersReady,
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    UsersReady: function() {
      return this.$store.state.pcmUsers.UsersReady
    },
    Users: function() {
      return this.$store.state.pcmUsers.Users
    },
    OrganizationName: {
      get() {
        return 'test'
      },
      set() {
        return true
      }
    }
  },
  methods: {
    onSubmit: function(){
      console.log('Submit')
    },
    onReset: function(){
      console.log('Reset')
    },
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
  },
  watch: {},
  mounted() {

    const vm = this

    vm.GETUsers()
  },
}
</script>

<style>

</style>

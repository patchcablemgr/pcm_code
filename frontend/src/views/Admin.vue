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

        <!-- Licensing -->
        <b-col>
          <component-licensing/>
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
import ComponentLicensing from '@/views/templates/ComponentLicensing.vue'

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
    ComponentLicensing,
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
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      //return DependenciesReady
      return true
    },
  },
  methods: {},
  watch: {},
  mounted() {},
}
</script>

<style>

</style>

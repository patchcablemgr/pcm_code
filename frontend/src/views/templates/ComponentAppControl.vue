<!-- Template/Object Details -->

<template>
<div>
  <b-card>
    <b-card-title>
      <div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing">
          App Control
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
              @click="UpdateApp"
            >
              Update App
            </b-dropdown-item>

          </b-dropdown>
        </div>
      </div>
    </b-card-title>

    <b-card-body>
    </b-card-body>
  </b-card>
</div>
</template>

<script>
import {
  BCard,
  BCardTitle,
  BCardBody,
  BDropdown,
  BDropdownItem,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
    BCard,
    BCardTitle,
    BCardBody,
    BDropdown,
    BDropdownItem,
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
    UpdateApp(){

      const vm = this
      
      const URL = '/api/config/app/update'

      vm.$http.post(URL).then(response => {
        console.log(response)
      }).catch(error => {
        vm.DisplayError(error)
      })
      
    },
  }
}
</script>
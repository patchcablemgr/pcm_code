<!-- Template/Object Details -->

<template>
  <div>
    <b-table
      small
      :fields="Fields"
      :items="Users"
      responsive="sm"
    >
      <template #cell(name)="data">
        {{ data.item.name }}
      </template>

      <template #cell(email)="data">
        {{ data.item.email }}
      </template>

      <template #cell(role)="data">
        <b-form-select
          v-model="data.item.role"
          :options="RoleOptions"
          @change="RoleChange(data.item.id, $event)"
        />
      </template>

      <template #cell(status)="data">
        <b-form-checkbox
          :checked="!!data.item.status"
          @change="StatusChange(data.item.id, !!data.item.status)"
          class="custom-control-primary"
          name="check-button"
          switch
        />
      </template>

    </b-table>
  </div>
</template>

<script>
import {
  BTable,
  BFormCheckbox,
  BFormSelect,
} from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'

const RoleOptions = [
  {'value': 'user', 'text': 'User'},
  {'value': 'operator', 'text': 'Operator'},
  {'value': 'admin', 'text': 'Administrator'},
]

const Fields = ['name', 'email', 'role', 'status']

export default {
  mixins: [PCM],
  components: {
    BTable,
    BFormCheckbox,
    BFormSelect,
  },
	directives: {
	},
  props: {},
  data() {
    return {
      RoleOptions,
      Fields,
    }
  },
  computed: {
    Users() {
      return this.$store.state.pcmUsers.Users
    },
  },
  methods: {
    RoleChange: function(UserID, Role) {

      const vm = this
      const data = {'role': Role}
      const URL = '/api/users/'+UserID

      vm.$http.patch(URL, data).then(response => {
        vm.$store.commit('pcmUsers/UPDATE_User', {data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    StatusChange: function(UserID, Status) {

      const vm = this
      const data = {'status': !Status}
      const URL = '/api/users/'+UserID

      vm.$http.patch(URL, data).then(response => {
        vm.$store.commit('pcmUsers/UPDATE_User', {data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    }
  }
}
</script>
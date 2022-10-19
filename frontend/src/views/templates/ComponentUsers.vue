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

      <template #cell(action)="data">
        <b-button
          v-ripple.400="'rgba(40, 199, 111, 0.15)'"
          variant="flat-success"
          class="btn-icon"
          @click="DeleteUser(data.item.id)"
        >
          <feather-icon icon="TrashIcon" />
        </b-button>
      </template>

    </b-table>
  </div>
</template>

<script>
import {
  BTable,
  BFormCheckbox,
  BFormSelect,
  BButton,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

const RoleOptions = [
  {'value': 'user', 'text': 'User'},
  {'value': 'operator', 'text': 'Operator'},
  {'value': 'admin', 'text': 'Administrator'},
]

const Fields = [
  {key: 'name', label: 'Name'},
  {key: 'email', label: 'Email'},
  {key: 'role', label: 'Role'},
  {key: 'status', label: 'Status'},
  {key: 'action', label: 'Action'},
]

export default {
  mixins: [PCM],
  components: {
    BTable,
    BFormCheckbox,
    BFormSelect,
    BButton,
  },
	directives: {
    Ripple,
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
    },
    DeleteUser: function(UserID) {

      const vm = this

      // Confirm Deletion
      const ConfirmMsg = 'Delete user?'
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {
        if (result === true) {
          vm.$http.delete('/api/users/'+UserID).then(response => {
            vm.$store.commit('pcmUsers/REMOVE_User', {data:response.data})
          }).catch(error => {
            vm.DisplayError(error)
          })
        }
      })
    },
  }
}
</script>
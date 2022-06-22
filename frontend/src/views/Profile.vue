<template>
  <div>
    <b-container class="bv-example-row" fluid="xs">
      <b-row>
        <b-col
          lg="4"
        >

          <b-card
            v-if="DependenciesReady"
          >
            <b-card-title>
              <div class="d-flex flex-wrap justify-content-between">
                <div class="demo-inline-spacing">
                  Password change
                </div>
              </div>
            </b-card-title>
            <b-card-body>
                
                <b-form @submit.prevent="SubmitPasswordChange">
                  <b-row>

                    <b-col cols="12">
                      <b-form-group
                        label="Password"
                        label-for="password"
                        label-cols-md="4"
                      >
                        <b-form-input
                          id="password"
                          type="password"
                          placeholder="············"
                          v-model="Password"
                        />
                      </b-form-group>
                    </b-col>

                    <b-col cols="12">
                      <b-form-group
                        label="Confirm Password"
                        label-for="password_confirm"
                        label-cols-md="4"
                      >
                        <b-form-input
                          id="password_confirm"
                          type="password"
                          placeholder="············"
                          v-model="PasswordConfirm"
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
                    </b-col>
                  </b-row>

                </b-form>
            </b-card-body>
          </b-card>

          </b-col>
          <b-col
            lg="4"
          >

          <b-card
            v-if="DependenciesReady"
          >
            <b-card-title>
              <div class="d-flex flex-wrap justify-content-between">
                <div class="demo-inline-spacing">
                  Multi-Factor Authentication
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
                      @click="EnableMFA"
                      :disabled="UserData.mfa_enabled"
                    >
                      Enable
                    </b-dropdown-item>

                    <b-dropdown-item
                      variant="danger"
                      @click="DisableMFA"
                      :disabled="!UserData.mfa_enabled"
                    >
                      Disable
                    </b-dropdown-item>

                  </b-dropdown>
                </div>
              </div>
            </b-card-title>
            <b-card-body>

              <!-- Current State -->
              <dl class="row">
                <dt class="col-sm-3">
                  Current State
                </dt>
                <dd class="col-sm-9">

                  <b-badge
                    v-if="UserData.mfa_enabled"
                    pill
                    variant="success"
                  >
                    Enabled
                  </b-badge>

                  <b-badge
                    v-if="!UserData.mfa_enabled"
                    pill
                    variant="danger"
                  >
                    Disabled
                  </b-badge>
                </dd>
              </dl>
              
              <div
                v-if="MFAPending"
              >
                
                <b-form
                  @submit.prevent="ConfirmMFA"
                  @reset="ResetMFA"
                  style="width:100%"
                >

                  <!-- Instructions -->
                  <dl class="row">
                    <dt class="col-sm-3">
                      Instructions
                    </dt>
                    <dd class="col-sm-9">
                      <ol
                        type="1"
                      >
                        <li>
                          Download and install the Google Authenticator app from your mobile device's app store.
                        </li>
                        <li>
                          Scan the QR code below with the Google Authenticator app to configure your device.
                        </li>
                        <li>
                          Confirm by entering the One Time Password from the PatchCableMgr entry in your Google Authenticator app.
                        </li>
                      </ol>
                    </dd>
                  </dl>

                  <!-- QR Code -->
                  <dl class="row">
                    <dt class="col-sm-3">
                      QR Code
                    </dt>
                    <dd class="col-sm-9">
                      <img
                        style="overflow:visible;"
                        :src="QRCode"
                      />
                    </dd>
                  </dl>

                  <!-- OTP -->
                  <dl class="row">
                    <dt class="col-sm-3">
                      Confirm OTP
                    </dt>
                    <dd class="col-sm-9">
                      <b-form-input
                        name="otp"
                        v-model="ConfirmOTP"
                        placeholder="OTP"
                      />
                    </dd>
                  </dl>

                  <!-- Submit/Cancel -->
                  <div offset-md="4">

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
                      Cancel
                    </b-button>
                  </div>
                </b-form>
              </div>
            </b-card-body>
          </b-card>

        </b-col>
      </b-row>
    </b-container>

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
  BFormGroup,
  BFormInput,
  BBadge,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import ToastGeneral from './templates/ToastGeneral.vue'
import { PCM } from '@/mixins/PCM.js'

const QRCode = ''
const MFAPending = false
const ConfirmOTP = ''
const UserDataReady = false
const UserData = null
const Password = ''
const PasswordConfirm = ''

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
    BFormGroup,
    BFormInput,
    BBadge,

    ToastGeneral,
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
	},
  data() {
    return {
      QRCode,
      MFAPending,
      ConfirmOTP,
      UserDataReady,
      UserData,
      Password,
      PasswordConfirm,
    }
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.UserDataReady,
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
  },
  methods: {
    EnableMFA() {

      const vm = this
      vm.$http.get('/api/profile/mfa').then(response => {
        vm.QRCode = response.data
        vm.MFAPending = true
      }).catch(error => {vm.DisplayError(error)})
    },
    DisableMFA() {

      const vm = this

      const ConfirmMsg = "Disabling multi-factor authentication will ease the security of your account and invalidate the Google Authenticator entry.  Are you sure you want to disable multi-factor authentication?"
      const ConfirmOpts = {
        title: "Disable?"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {

        if (result === true) {
          vm.$http.patch('/api/profile/mfa').then(response => {
            vm.UserData = response.data
            vm.DisplaySuccess('MFA successfully disabled.')
          }).catch(error => {vm.DisplayError(error)})
        }
      })
    },
    ConfirmMFA() {

      const vm = this
      const data = {'otp': vm.ConfirmOTP}
      vm.$http.post('/api/profile/mfa', data).then(response => {
        
        vm.UserData = response.data
        vm.MFAPending = false
        vm.DisplaySuccess('MFA successfully enabled.')
      }).catch(error => {vm.DisplayError(error)})
    },
    ResetMFA() {

      const vm = this
      vm.MFAPending = false
      vm.QRCode = ''
    },
    GETUser() {
	    this.$http.get('/api/auth/user').then(response => {
        
        const vm = this
        vm.UserData = response.data
        vm.UserDataReady = true
      }).catch(error => {vm.DisplayError(error)})
    },
    SubmitPasswordChange() {

      const vm = this
      const data = {
        'password': vm.Password,
        'password_confirm': vm.PasswordConfirm
      }
      vm.$http.patch('/api/profile/change-password', data).then(response => {
        
        vm.DisplaySuccess('Password successfully updated.')
      }).catch(error => {vm.DisplayError(error)})
    },
  },
  mounted() {

    const vm = this

    vm.GETUser()
  },
}
</script>

<style>

</style>

<template>
  <div class="auth-wrapper auth-v2">
    <b-row class="auth-inner m-0">

      <!-- Brand logo-->
      <b-link class="brand-logo">
        <vuexy-logo />
        <h2 class="brand-text text-primary ml-1">
          PatchCableMgr
        </h2>
      </b-link>
      <!-- /Brand logo-->

      <!-- Left Text-->
      <b-col
        lg="8"
        class="d-none d-lg-flex align-items-center p-5"
      >
        <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
          <b-img
            fluid
            :src="imgUrl"
            alt="Login V2"
          />
        </div>
      </b-col>
      <!-- /Left Text-->

      <!-- Login-->
      <b-col
        lg="4"
        class="d-flex align-items-center auth-bg px-2 p-lg-5"
      >
        <b-col
          sm="8"
          md="6"
          lg="12"
          class="px-xl-2 mx-auto"
        >
          <b-card-title
            title-tag="h2"
            class="font-weight-bold mb-1"
          >
            Welcome to PatchCableMgr
          </b-card-title>
          <b-card-text class="mb-2">
            Please sign-in
          </b-card-text>

          <!-- login form -->
          <validation-observer
            v-if="visibleForm == 'login'"
            ref="loginValidation"
          >
            <b-form
              class="auth-login-form mt-2"
              @submit.prevent
            >
              <!-- email -->
              <b-form-group
                label="Email"
                label-for="login-email"
              >
                <validation-provider
                  #default="{ errors }"
                  name="Email"
                  rules="required|email"
                >
                  <b-form-input
                    id="login-email"
                    v-model="userEmail"
                    :state="errors.length > 0 ? false:null"
                    name="login-email"
                    placeholder="john@example.com"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- password -->
              <b-form-group>
                <div class="d-flex justify-content-between">
                  <label for="login-password">Password</label>
                </div>

                <validation-provider
                  #default="{ errors }"
                  name="Password"
                  rules="required"
                >
                  <b-input-group
                    class="input-group-merge"
                    :class="errors.length > 0 ? 'is-invalid':null"
                  >
                    <b-form-input
                      id="login-password"
                      v-model="password"
                      :state="errors.length > 0 ? false:null"
                      class="form-control-merge"
                      :type="passwordFieldType"
                      name="login-password"
                      placeholder="············"
                    />
                    <b-input-group-append is-text>
                      <feather-icon
                        class="cursor-pointer"
                        :icon="passwordToggleIcon"
                        @click="togglePasswordVisibility"
                      />
                    </b-input-group-append>
                  </b-input-group>

                  <b-link
                    @click="displayForgotPW">
                    <small>Forgot Password?</small>
                  </b-link>

                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              

              <!-- submit buttons -->
              <b-button
                type="submit"
                variant="primary"
                block
                @click="SubmitLogin"
              >
                Sign in
              </b-button>
              <validation-provider
                #default="{ errors }"
                name="Submit"
              >
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>

            </b-form>
          </validation-observer>

          <!-- OTP form -->
          <validation-observer
            v-if="visibleForm == 'otp'"
            ref="otpValidation"
          >
            <b-form
              class="auth-login-form mt-2"
              @submit.prevent
            >
              <!-- OTP -->
              <b-form-group
                label="OTP"
                label-for="login-otp"
              >
                <validation-provider
                  #default="{ errors }"
                  name="OTP"
                  rules="required"
                >
                  <b-form-input
                    id="login-otp"
                    v-model="userOTP"
                    :state="errors.length > 0 ? false:null"
                    name="login-otp"
                    placeholder="######"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                  
                </validation-provider>

              </b-form-group>

              <!-- submit buttons -->
              <b-button
                type="submit"
                variant="primary"
                block
                @click="SubmitOTP"
              >
                Sign in
              </b-button>
              <validation-provider
                #default="{ errors }"
                name="Submit"
              >
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
              
            </b-form>

            <b-link
              @click="displayLogin">
              <small>Go Back</small>
            </b-link>

          </validation-observer>

          <!-- Forgot PW form -->
          <validation-observer
            v-if="visibleForm == 'forgotPW'"
            ref="forgotPWValidation"
          >
            <b-form
              class="auth-login-form mt-2"
              @submit.prevent
            >
              <!-- email -->
              <b-form-group
                label="Email"
                label-for="login-forgot-pw"
              >
                <validation-provider
                  #default="{ errors }"
                  name="Email"
                  rules="required|email"
                >
                  <b-form-input
                    id="login-forgot-pw"
                    v-model="userForgotPWEmail"
                    :state="errors.length > 0 ? false:null"
                    name="login-forgot-pw"
                    placeholder="john@example.com"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- submit buttons -->
              <b-button
                type="submit"
                variant="primary"
                block
                @click="SubmitForgotPW"
              >
                Submit
              </b-button>
              <validation-provider
                #default="{ errors }"
                name="Submit"
              >
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form>

            <b-link
              @click="displayLogin">
              <small>Go Back</small>
            </b-link>

          </validation-observer>

          <b-card-text
            v-if="visibleForm == 'login'"
            class="text-center mt-2"
          >
            <span>New on our platform? </span>
            <b-link :to="{name:'register'}">
              <span>&nbsp;Create an account</span>
            </b-link>
          </b-card-text>

        </b-col>
      </b-col>
    <!-- /Login-->
    </b-row>
  </div>
</template>

<script>
/* eslint-disable global-require */
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import VuexyLogo from '@core/layouts/components/Logo.vue'
import {
  BRow, BCol, BLink, BFormGroup, BFormInput, BInputGroupAppend, BInputGroup, BFormCheckbox, BCardText, BCardTitle, BImg, BForm, BButton,
} from 'bootstrap-vue'
import { required, email } from '@validations'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import store from '@/store/index'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import useJwt from '@/auth/jwt/useJwt'

export default {
  components: {
    BRow,
    BCol,
    BLink,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BCardText,
    BCardTitle,
    BImg,
    BForm,
    BButton,
    VuexyLogo,
    ValidationProvider,
    ValidationObserver,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      status: '',
      password: '',
      userEmail: '',
      userOTP: null,
      displayOTP: false,
      MFASessionHash: null,
      userForgotPWEmail: '',
      visibleForm: 'login',
      sideImg: require('@/assets/images/pages/login-v2.svg'),
      // validation rulesimport store from '@/store/index'
      required,
      email,
    }
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    imgUrl() {
      if (store.state.appConfig.layout.skin === 'dark') {
        // eslint-disable-next-line vue/no-side-effects-in-computed-properties
        this.sideImg = require('@/assets/images/pages/login-v2-dark.svg')
        return this.sideImg
      }
      return this.sideImg
    },
  },
  methods: {
    SubmitLogin() {
      this.$refs.loginValidation.validate().then(success => {
        if (success) {
          this.$http.post('/api/auth/login', {
            email: this.userEmail,
            password: this.password
          }).then(response => {

            if("mfa_session_hash" in response.data) {

              this.visibleForm = 'otp'
              this.MFASessionHash = response.data.mfa_session_hash

            } else {
			
              // `response.data` is response from API which is above mentioned
              const { userData } = response.data

              // Setting access token in localStorage
              // NOTE: Please check the source code to better understand JWT service
              useJwt.setToken(response.data.accessToken)

              // Setting refresh token in localStorage
              //useJwt.setRefreshToken(response.data.refreshToken)

              // Setting user data in localStorage
              localStorage.setItem('userData', JSON.stringify(response.data))

              // Updating user ability in CASL plugin instance
              this.$ability.update(response.data.ability)

              // Redirection after login
              this.$router.replace({name: 'dashboard'})
            }
          })
          .catch(error => {
            console.log(error.response.data.message)
            this.$refs.loginValidation.setErrors({'Submit':[error.response.data.message]})
          })
        }
      })
    },
    SubmitOTP() {
      this.$refs.otpValidation.validate().then(success => {
        if (success) {
          this.$http.post('/api/auth/mfa', {
            otp: this.userOTP,
            mfa_session_hash: this.MFASessionHash,
          }).then(response => {
			
            // `response.data` is response from API which is above mentioned
            const { userData } = response.data

            // Setting access token in localStorage
            // NOTE: Please check the source code to better understand JWT service
            useJwt.setToken(response.data.accessToken)

            // Setting refresh token in localStorage
            //useJwt.setRefreshToken(response.data.refreshToken)

            // Setting user data in localStorage
            localStorage.setItem('userData', JSON.stringify(response.data))

            // Updating user ability in CASL plugin instance
            this.$ability.update(response.data.ability)
            
            // Redirection after login
            this.$router.replace({name: 'dashboard'})
          })
          .catch(error => {
            this.$refs.otpValidation.setErrors({'Submit':[error.response.data.message]})
          })
        }
      })
    },
    SubmitForgotPW() {
      this.$refs.forgotPWValidation.validate().then(success => {
        if (success) {

          const url = '/api/auth/forgot-password'
          const data = {}
          this.$http.post(url, data).then(response => {
            console.log(response)
          })
          .catch(error => {
            this.$refs.forgotPWValidation.setErrors({'Submit':[error.response.data.message]})
          })
        }
      })
    },
    displayForgotPW() {

      const vm = this
      this.visibleForm = "forgotPW"
    },
    displayLogin() {

      const vm = this
      this.visibleForm = "login"
    },
  },
}
</script>

<style lang="scss">
@import '@core/scss/vue/pages/page-auth.scss';
</style>

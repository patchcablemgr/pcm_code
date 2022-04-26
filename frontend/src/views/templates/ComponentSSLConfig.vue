<!-- Template/Object Details -->

<template>
<div>
  <b-card>
    <b-card-title>
      <div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing">
          SSL Config
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
              v-b-modal.modal-ssl-subject-csr
            >
              Generate CSR
            </b-dropdown-item>

          </b-dropdown>
        </div>
      </div>
    </b-card-title>

    <b-card-body>

      <h4>CSR List</h4>
      <b-table
        small
        :fields="CSRFields"
        :items="CSRList"
        responsive="sm"
      >

        <template #cell(common_name)="data">
          {{ data.item.cn }}
        </template>

        <template #cell(created)="data">
          {{ data.item.created_at }}
        </template>

        <template #cell(created_by)="data">
          {{ GetUser(data.item.user_id).name }}
        </template>

        <template #cell(actions)="data">

          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-view-csr
            @click="SelectCSRID(data.item.id)"
            v-b-tooltip.hover.top="'View CSR'"
          >
            <feather-icon icon="EyeIcon" />
          </b-button>

          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-cert-upload
            @click="SelectCSRID(data.item.id)"
            v-b-tooltip.hover.top="'Upload certificate'"
          >
            <feather-icon icon="UploadIcon" />
          </b-button>

          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            @click="GenerateSelfSigned(data.item.id)"
            v-b-tooltip.hover.top="'Generate self-signed certificate'"
          >
            <feather-icon icon="PenToolIcon" />
          </b-button>

          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            @click="DeleteCSR(data.item.id)"
            v-b-tooltip.hover.top="'Delete CSR'"
          >
            <feather-icon icon="TrashIcon" />
          </b-button>
        </template>

      </b-table>

      <h4>Certificate List</h4>
      <b-table
        small
        :fields="CertFields"
        :items="CertList"
        responsive="sm"
      >

        <template #cell(common_name)="data">
          {{ data.item.cn }}
        </template>

        <template #cell(valid_from)="data">
          {{ data.item.valid_from }}
        </template>

        <template #cell(valid_to)="data">
          {{ data.item.valid_to }}
        </template>

        <template #cell(issuer)="data">
          {{ data.item.issuer }}
        </template>

        <template #cell(active)="data">
          <b-form-radio
            v-model="CertActive"
            name="cert-active"
            :value="data.item.id"
            v-b-tooltip.hover.top="'Apply certificate'"
          />
        </template>

        <template #cell(actions)="data">

          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            @click="DeleteCert(data.item.id)"
            v-b-tooltip.hover.top="'Delete certificate'"
          >
            <feather-icon icon="TrashIcon" />
          </b-button>
        </template>

      </b-table>
    </b-card-body>
  </b-card>

  <!-- Modal:  SSL Subect CSR -->
  <modal-s-s-l-subject
    ModalID="modal-ssl-subject-csr"
    ModalTitle="CSR"
  >
  </modal-s-s-l-subject>

  <!-- File Upload Modal -->
  <modal-view-c-s-r
    :SelectedCSRID="SelectedCSRID"
  >
  </modal-view-c-s-r>

  <!-- File Upload Modal -->
  <modal-cert-upload
    :SelectedCSRID="SelectedCSRID"
  >
  </modal-cert-upload>
</div>
</template>

<script>
import {
  VBTooltip,
  BCard,
  BCardTitle,
  BCardBody,
  BTable,
  BRow,
  BCol,
  BForm,
  BFormInput,
  BFormGroup,
  BFormCheckbox,
  BFormSelect,
  BButton,
  BFormRadio,
  BDropdown,
  BDropdownItem,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalViewCSR from '@/views/templates/ModalViewCSR.vue'
import ModalCertUpload from '@/views/templates/ModalCertUpload.vue'
import ModalSSLSubject from '@/views/templates/ModalSSLSubject.vue'

const CSRFields = ['common_name', 'created', 'created_by', 'actions']
const CertFields = ['common_name', 'valid_from', 'valid_to', 'issuer', 'active', 'actions']
const SelectedCSRID = null
const SelectedCertID = null
const ActiveCertID = null

export default {
  mixins: [PCM],
  components: {
    BCard,
    BCardTitle,
    BCardBody,
    BTable,
    BRow,
    BCol,
    BForm,
    BFormInput,
    BFormGroup,
    BFormCheckbox,
    BFormSelect,
    BButton,
    BFormRadio,
    BDropdown,
    BDropdownItem,

    ModalViewCSR,
    ModalCertUpload,
    ModalSSLSubject,
  },
	directives: {
    'b-tooltip': VBTooltip,
    Ripple,
	},
  props: {},
  data() {
    return {
      CSRFields,
      CertFields,
      SelectedCSRID,
      SelectedCertID,
      ActiveCertID,
    }
  },
  computed: {
    CSRList() {
      return this.$store.state.pcmSSL.CSRList
    },
    CertList() {
      return this.$store.state.pcmSSL.CertList
    },
    Users() {
      return this.$store.state.pcmUsers.Users
    },
    CertActive:{
      get(){

        const vm = this
        const ActiveCertIndex = vm.CertList.findIndex((cert) => cert.active )

        if(ActiveCertIndex !== -1) {
          const ActiveCert = vm.CertList[ActiveCertIndex]
          const ActiveCertID = ActiveCert.id
          return ActiveCertID
        } else {
          return 0
        }
      },
      set(CertID){

        const vm = this
        const ActiveCertID = vm.ActiveCertID

        // Ensure newly activated cert does not match already activated cert
        // This prevents multiple activation events
        let HonorActivation = true
        if(CertID == ActiveCertID) {
          HonorActivation = false
        }
        vm.ActiveCertID = CertID

        if(HonorActivation) {

          // Confirm Deletion
          const ConfirmMsg = 'Activate certificate?  This action may require users to refresh their browser.'
          const ConfirmOpts = {
            title: "Confirm"
          }
          vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {
            if (result === true) {
              vm.$http.patch('/api/config/cert/'+CertID+'/activate').then(response => {

                // Clear currently active certificate
                vm.CertList.forEach(function(cert){
                  if(cert.active) {
                    const CertCopy = JSON.parse(JSON.stringify(cert), function(key, value){
                      if(key === 'active') {
                        return false
                      } else {
                        return value
                      }
                    })
                    vm.$store.commit('pcmSSL/UPDATE_Cert', {data:CertCopy})
                  }
                })

                // Update newly activated certificate
                vm.$store.commit('pcmSSL/UPDATE_Cert', {data:response.data})
              }).catch(error => {
                vm.DisplayError(error)
              })
            }
          })
        }
      },
    },
  },
  methods: {
    GetUser(UserID){

      const vm = this
      const UserIndex = vm.Users.findIndex((user) => user.id == UserID)
      if(UserIndex !== -1) {
        const User = vm.Users[UserIndex]
        return User
      } else {
        return false
      }
    },
    GenerateCSR(){

      const vm = this
      const URL = '/api/config/csr'

      vm.$http.post(URL).then(response => {
        vm.$store.commit('pcmSSL/ADD_CSR', {data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    GenerateSelfSigned(CSRID){

      const vm = this

      const URL = '/api/config/csr/'+CSRID+'/self-signed'

      vm.$http.post(URL).then(response => {
        vm.$store.commit('pcmSSL/ADD_Cert', {data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    DeleteCSR(CSRID){

      const vm = this

      // Confirm Deletion
      const ConfirmMsg = 'Delete CSR?'
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {
        if (result === true) {
          vm.$http.delete('/api/config/csr/'+CSRID).then(response => {
            vm.$store.commit('pcmSSL/REMOVE_CSR', {data:response.data})
          }).catch(error => {
            vm.DisplayError(error)
          })
        }
      })
    },
    DeleteCert(CertID){

      const vm = this

      // Confirm Deletion
      const ConfirmMsg = 'Delete certificate?'
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {
        if (result === true) {
          vm.$http.delete('/api/config/cert/'+CertID).then(response => {
            vm.$store.commit('pcmSSL/REMOVE_Cert', {data:response.data})
          }).catch(error => {
            vm.DisplayError(error)
          })
        }
      })
    },
    SelectCSRID(CSRID){

      const vm = this
      vm.SelectedCSRID = CSRID
    },
  }
}
</script>
<!-- Template/Object Details -->

<template>
<div>
  <div class="demo-inline-spacing">

    <b-button
      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
      variant="primary"
      class="mr-1"
      @click="GenerateCSR"
    >
      Generate CSR
    </b-button>

    <b-button
      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
      variant="primary"
      class="mr-1"
    >
      Generate Self-signed
    </b-button>

  </div>

  <b-table
      small
      :fields="CSRFields"
      :items="CSRList"
      responsive="sm"
    >
      <template #cell(created)="data">
        {{ data.item.created_at }}
      </template>

      <template #cell(user)="data">
        {{ data.item.user_id }}
      </template>

      <template #cell(actions)="data">

        <b-button
          v-ripple.400="'rgba(40, 199, 111, 0.15)'"
          variant="flat-success"
          class="btn-icon"
          v-b-modal.modal-view-csr
          @click="SelectCSRID(data.item.id)"
        >
          <feather-icon icon="EyeIcon" />
        </b-button>

        <b-button
          v-ripple.400="'rgba(40, 199, 111, 0.15)'"
          variant="flat-success"
          class="btn-icon"
          v-b-modal.modal-cert-upload
          @click="SelectCSRID(data.item.id)"
        >
          <feather-icon icon="UploadIcon" />
        </b-button>

        <b-button
          v-ripple.400="'rgba(40, 199, 111, 0.15)'"
          variant="flat-success"
          class="btn-icon"
          @click="DeleteCSR(data.item.id)"
        >
          <feather-icon icon="TrashIcon" />
        </b-button>
      </template>

    </b-table>

    <b-table
      small
      :fields="CertFields"
      :items="CertList"
      responsive="sm"
    >
      <template #cell(valid_from)="data">
        {{ data.item.valid_from }}
      </template>

      <template #cell(valid_to)="data">
        {{ data.item.valid_to }}
      </template>

      <template #cell(active)="data">
        {{ data.item.user_id }}
      </template>

      <template #cell(actions)="data">

        <b-button
          v-ripple.400="'rgba(40, 199, 111, 0.15)'"
          variant="flat-success"
          class="btn-icon"
          @click="DeleteCert(data.item.id)"
        >
          <feather-icon icon="TrashIcon" />
        </b-button>
      </template>

    </b-table>

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
  BTable,
  BRow,
  BCol,
  BForm,
  BFormInput,
  BFormGroup,
  BFormCheckbox,
  BFormSelect,
  BButton,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalViewCSR from '@/views/templates/ModalViewCSR.vue'
import ModalCertUpload from '@/views/templates/ModalCertUpload.vue'

const CSRFields = ['created', 'user', 'actions']
const CertFields = ['valid_from', 'valid_to', 'active', 'actions']
const SelectedCSRID = null
const SelectedCertID = null

export default {
  mixins: [PCM],
  components: {
    BTable,
    BRow,
    BCol,
    BForm,
    BFormInput,
    BFormGroup,
    BFormCheckbox,
    BFormSelect,
    BButton,

    ModalViewCSR,
    ModalCertUpload,
  },
	directives: {
    Ripple,
	},
  props: {},
  data() {
    return {
      CSRFields,
      CertFields,
      SelectedCSRID,
      SelectedCertID,
    }
  },
  computed: {
    CSRList() {
      return this.$store.state.pcmSSL.CSRList
    },
    CertList() {
      return this.$store.state.pcmSSL.CertList
    },
  },
  methods: {
    GenerateCSR(){

      const vm = this
      const URL = '/api/config/csr'

      vm.$http.post(URL).then(response => {
        vm.$store.commit('pcmSSL/ADD_CSR', {data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
    DeleteCSR(CSRID){

      const vm = this

      // Confirm Deletion
      const ConfirmMsg = 'Confirm'
      const ConfirmOpts = {
        title: "Delete CSR?"
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
      const ConfirmMsg = 'Confirm'
      const ConfirmOpts = {
        title: "Delete certificate?"
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
<template>
    <!-- Template edit modal -->
    <b-modal
      id="modal-view-csr"
      title="View"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>
          <b-card
            title="CSR"
          >
            <b-card-text v-html="CSR">
              
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>

    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'

export default {
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BForm,
    BButton,
    BFormInput,
    BFormSelect,
    BFormCheckbox,
    BCardText,
  },
  directives: {},
  props: {
    SelectedCSRID: {type: Number},
  },
  data () {
    return {
    }
  },
  computed: {
    CSRList() {
      return this.$store.state.pcmSSL.CSRList
    },
    CSR: {
      get() {

        const vm = this
        const CSRIndex = vm.CSRList.findIndex((csr) => csr.id == vm.SelectedCSRID)

        if(CSRIndex !== -1) {

          const CSR = vm.CSRList[CSRIndex]
          return CSR.csr.replaceAll('\n', '<br>')
          //return CSR.csr
        } else {

          return ''
        }
      }
    }
  },
  methods: {
  },
}
</script>
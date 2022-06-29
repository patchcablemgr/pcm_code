<template>
    <!-- Template edit modal -->
    <b-modal
      id="modal-edit-license-key"
      title="Edit"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>
          <b-card
            :title="ModalTitle"
          >
            <b-card-text>
              <b-form
                @submit.prevent="Submit"
                style="width:100%"
              >

                <!-- Name -->
                <dl class="row">
                  <dt class="col-sm-4">
                    Key
                  </dt>
                  <dd class="col-sm-8">
                    <b-form-input
                      name="license-key"
                      v-model="Key"
                    />
                  </dd>
                </dl>

                <!-- Submit -->
                <div offset-md="4">
                  <b-button
                      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                      type="submit"
                      variant="primary"
                      class="mr-1"
                  >
                      Submit
                  </b-button>
                </div>

              </b-form>
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>

    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
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
  directives: {
    Ripple,
  },
  props: {
    ModalTitle: {type: String},
    LicenseKey: {type: String},
  },
  data () {
    return {
      Key: this.LicenseKey
    }
  },
  computed: {
    Organization() {
      return this.$store.state.pcmOrganization.Organization
    },
    OrganizationReady: function() {
      return this.$store.state.pcmOrganization.OrganizationReady
    },
  },
  methods: {
    Submit: function() {

      const vm = this
      const Data = {
        'license-key': vm.Key,
      }
      const URL = '/api/organization/license'

      vm.$http.patch(URL, Data).then(response => {
        vm.$store.commit('pcmOrganization/UPDATE_Organization', {data:response.data})

      }).catch(error => { vm.DisplayError(error) })
    }
  },
}
</script>
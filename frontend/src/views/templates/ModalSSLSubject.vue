<template>
    <!-- Template edit modal -->
    <b-modal
      :id="ModalID"
      title="Create"
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

              <b-form @submit="Submit">
                <b-row>

                  <!-- Country -->
                  <b-col cols="12">
                    <b-form-group
                      label="Country"
                      label-for="h-country"
                      label-cols-md="4"
                    >
                      <b-form-input
                        id="h-country"
                        placeholder="US"
                        v-model="SubjectData.country"
                      />
                    </b-form-group>
                  </b-col>

                  <!-- State -->
                  <b-col cols="12">
                    <b-form-group
                      label="State"
                      label-for="h-state"
                      label-cols-md="4"
                    >
                      <b-form-input
                        id="h-state"
                        placeholder="Washington"
                        v-model="SubjectData.state"
                      />
                    </b-form-group>
                  </b-col>

                  <!-- City -->
                  <b-col cols="12">
                    <b-form-group
                      label="City"
                      label-for="h-city"
                      label-cols-md="4"
                    >
                      <b-form-input
                        id="h-city"
                        placeholder="Seattle"
                        v-model="SubjectData.city"
                      />
                    </b-form-group>
                  </b-col>

                  <!-- Organization -->
                  <b-col cols="12">
                    <b-form-group
                      label="Organization"
                      label-for="h-organization"
                      label-cols-md="4"
                    >
                      <b-form-input
                        id="h-organization"
                        placeholder="My Company"
                        v-model="SubjectData.organization"
                      />
                    </b-form-group>
                  </b-col>

                  <!-- CN -->
                  <b-col cols="12">
                    <b-form-group
                      label="CN"
                      label-for="h-cn"
                      label-cols-md="4"
                    >
                      <b-form-input
                        id="h-cn"
                        placeholder="pcm.mycompany.com"
                        v-model="SubjectData.cn"
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
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>

    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormGroup, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

const SubjectData = {
  'country': null,
  'state': null,
  'city': null,
  'organization': null,
  'cn': null,
}

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BForm,
    BButton,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BFormCheckbox,
    BCardText,
  },
  directives: {
    Ripple,
  },
  props: {
    ModalID: {type: String},
    ModalTitle: {type: String},
  },
  data () {
    return {
      SubjectData,
    }
  },
  computed: {
  },
  methods: {
    Submit(event){

      event.preventDefault()

      const vm = this
      const data = vm.SubjectData
      const URL = '/api/config/csr'

      vm.$http.post(URL, data).then(response => {
        vm.$store.commit('pcmSSL/ADD_CSR', {data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    },
  },
}
</script>
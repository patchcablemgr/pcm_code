<template>
    <!-- Template port Format modal -->
    <b-modal
      :id="ModalID"
      title="Create"
      ok-title="Submit"
      @ok="Submit"
    >
      <b-card
        title="Create Cable"
        class="position-static"
      >
        <b-card-text>
          <validation-observer
            ref="validation"
            v-slot="{ invalid }"
          >
            <b-form
              @submit.prevent="Submit"
            >
              <validation-provider
                name="Value"
                :rules="{regex: /^[A-Za-z0-9\/\/\_\-]+$/}"
                #default="{ errors }"
              >
              
                <!-- Cable End A ID -->
                <dl class="row">
                  <dt class="col-sm-4">
                    A - Cable End ID
                  </dt>
                  <dd class="col-sm-8">
                    <b-form-input
                      v-model="CableEndAID"
                      type="text"
                      placeholder="1A"
                      autofocus
                    />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </dd>
                </dl>

              </validation-provider>
              <validation-provider
                name="Value"
                :rules="{regex: /^[0-9]+$/}"
                #default="{ errors }"
              >

                <!-- Cable End A Connector -->
                <dl class="row">
                  <dt class="col-sm-4">
                    A - Cable End Connector
                  </dt>
                  <dd class="col-sm-8">
                    <b-form-select
                      v-model="CableEndAConnector"
                      :options="CableConnectorOptions"
                    />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </dd>
                </dl>

              </validation-provider>
              <validation-provider
                name="Value"
                :rules="{regex: /^[A-Za-z0-9\/\/\_\-]+$/}"
                #default="{ errors }"
              >

                <!-- Cable End A ID -->
                <dl class="row">
                  <dt class="col-sm-4">
                    B - Cable End ID
                  </dt>
                  <dd class="col-sm-8">
                    <b-form-input
                      v-model="CableEndBID"
                      type="text"
                      placeholder="1B"
                    />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </dd>
                </dl>

              </validation-provider>
              <validation-provider
                name="Value"
                :rules="{regex: /^[0-9]+$/}"
                #default="{ errors }"
              >

                <!-- Cable End B Connector -->
                <dl class="row">
                  <dt class="col-sm-4">
                    B - Cable End Connector
                  </dt>
                  <dd class="col-sm-8">
                    <b-form-select
                      v-model="CableEndBConnector"
                      :options="CableConnectorOptions"
                    />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </dd>
                </dl>

              </validation-provider>
              <validation-provider
                name="Value"
                :rules="{regex: /^[0-9]+$/}"
                #default="{ errors }"
              >

                <!-- Media -->
                <dl class="row">
                  <dt class="col-sm-4">
                    Media
                  </dt>
                  <dd class="col-sm-8">
                    <b-form-select
                      v-model="CableMedia"
                      :options="CableMediaOptions"
                    />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </dd>
                </dl>

              </validation-provider>

              <validation-provider
                name="Value"
                :rules="{regex: /^[0-9]+$/}"
                #default="{ errors }"
              >

                <!-- Length -->
                <dl class="row">
                  <dt class="col-sm-4">
                    Cable Length ({{CableLengthUnit}})
                  </dt>
                  <dd class="col-sm-8">
                    <b-form-input
                      v-model="CableLength"
                      type="number"
                      placeholder="1"
                    />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </dd>
                </dl>

              </validation-provider>

            </b-form>
          </validation-observer>
        </b-card-text>
      </b-card>
    </b-modal>
</template>

<script>
import {
  BRow,
  BCol,
  BCard,
  BCardText,
  BForm,
  BFormInput,
  BFormSelect,
  BButton,
  VBTooltip,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { configure, ValidationProvider, ValidationObserver } from 'vee-validate'
import { regex } from '@validations'
import { PCM } from '@/mixins/PCM.js'

const config = {
  useConstraintAttrs: false,
  defaultMessage: "invalid input"
}

configure(config)

const ToolTipPreview = {
  title: `
    <div class="text-left">
    <div>Preview Port IDs generated from Port Format settings above.</div>
    </div>
  `
}

export default {
  mixins: [PCM],
  components: {
    ValidationProvider,
    ValidationObserver,
    regex,
    BRow,
    BCol,
    BCard,
    BCardText,
    BForm,
    BFormInput,
    BFormSelect,
    BButton,
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
  },
  props: {
    ModalID: {type: String},
  },
  data() {
    return {
      ToolTipPreview,
      CableEndAID: null,
      CableEndAConnector: null,
      CableEndBID: null,
      CableEndBConnector: null,
      CableMedia: null,
      CableLength: 1,
    }
  },
  computed: {
    CableConnectors() {
      return this.$store.state.pcmProps.CableConnectors
    },
    Media() {
      return this.$store.state.pcmProps.Media
    },
    MediaType() {
      return this.$store.state.pcmProps.MediaType
    },
    CableConnectorOptions: {
      get() {

        // Store variables
        const vm = this
        let OptionArray = []

        // Populate option array with data to be used as select options
        vm.CableConnectors.forEach(element => OptionArray.push({'value': element.value, 'text': element.name}))

        return OptionArray
      },
      set() {

      }
    },
    CableMediaOptions: {
      get() {

        // Store variables
        const vm = this
        let OptionArray = []

        // Populate option array with data to be used as select options
        vm.Media.forEach(element => OptionArray.push({'value': element.value, 'text': element.name}))

        return OptionArray
      },
      set() {

      }
    },
    CableLengthUnit() {
      const vm = this
      const MediaIndex = vm.Media.findIndex((element) => element.value == vm.CableMedia )
      
      if(MediaIndex === -1) {
        return 'ft.'
      } else {
        const MediaTypeID = vm.Media[MediaIndex]['type_id']
        const MediaTypeIndex = vm.MediaType.findIndex((element) => element.value == MediaTypeID)
        return vm.MediaType[MediaTypeIndex]['unit_of_length']
      }
    },
  },
  methods: {
    Submit: function() {

      const vm = this

      vm.$refs.validation.validate().then((Valid) => {
        if(Valid) {
          const data = {
            a_id: vm.CableEndAID,
            a_connector_id: vm.CableEndAConnector,
            b_id: vm.CableEndBID,
            b_connector_id: vm.CableEndBConnector,
            media_id: vm.CableMedia,
            length: vm.CableLength,
          }
          vm.$http.post('/api/cables', data).then(response => {

            // Update node from store
            vm.$store.commit('pcmCables/ADD_Cable', {data:response.data})

          }).catch(error => {vm.DisplayError(error)})

          // Hide modal, this is necessary to close modal after submitting by click or enter
          vm.$bvModal.hide(vm.ModalID)
        }
      })
    },
    SetDefaultCableConnector: function(){
      const vm = this
      const DefaultIndex = vm.CableConnectors.findIndex((element) => element.default )
      const DefaultValue = vm.CableConnectors[DefaultIndex].value
      vm.CableEndAConnector = DefaultValue
      vm.CableEndBConnector = DefaultValue
    },
    SetDefaultMedia: function(){
      const vm = this
      const DefaultIndex = vm.Media.findIndex((element) => element.default )
      const DefaultValue = vm.Media[DefaultIndex].value
      vm.CableMedia = DefaultValue
    }
  },
  mounted() {
    const vm = this
    vm.SetDefaultCableConnector()
    vm.SetDefaultMedia()
  }
}
</script>
<template>
    <b-modal
      :id="ModalID"
      title="Edit"
      size="lg"
      ok-title="Submit"
      @ok="Submit"
    >
      <b-row>
        <b-col>
          <b-card
            :title="ModalTitle"
          >
            <b-card-text>
              <validation-observer
                ref="validation"
                v-slot="{ invalid }"
              >
              <validation-provider
                name="Value"
                :rules="{regex: /^[A-Za-z0-9\/\_\-\s]+$/}"
                #default="{ errors }"
              >
                <b-form
                  @submit.prevent="Submit"
                >
                  <b-form-input
                    v-model="PortDescription"
                    type="text"
                    autofocus
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </b-form>
              </validation-provider>
              </validation-observer>
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>
    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'
import { configure, ValidationProvider, ValidationObserver } from 'vee-validate'
import { regex } from '@validations'

const config = {
  useConstraintAttrs: false,
  defaultMessage: "invalid input"
}

configure(config)

let PortDescription = null

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
    ValidationProvider,
    ValidationObserver,
    regex,
  },
  directives: {},
  props: {
    ModalID: {type: String},
    Context: {type: String},
    ModalTitle: {type: String},
  },
  data () {
    return {
      PortDescription,
    }
  },
  computed: {
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Ports() {
      return this.$store.state.pcmPorts.Ports
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    ComputedPortDescription() {

      const vm = this

      const ObjectID = vm.StateSelected.actual.object_id
      const ObjectFace = vm.StateSelected.actual.object_face
      const ObjectPartition = vm.StateSelected.actual.partition
      const PortID = vm.StateSelected.actual.port_id

      const PortIndex = vm.Ports.findIndex((port) => port.object_id == ObjectID && port.object_face == ObjectFace && JSON.stringify(port.object_partition) == JSON.stringify(ObjectPartition) && port.port_id == PortID)

      let PortDescription
      if (PortIndex != -1) {
        PortDescription = vm.Ports[PortIndex].description
      } else {
        PortDescription = 'None'
      }
      
      return PortDescription
    },
  },
  methods: {
    Submit: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.StateSelected.actual.object_id
      const ObjectFace = vm.StateSelected.actual.object_face
      const ObjectPartition = vm.StateSelected.actual.partition[ObjectFace]
      const PortID = vm.StateSelected.actual.port_id[ObjectFace]
      const Description = vm.PortDescription

      vm.$refs.validation.validate().then((Valid) => {
        if(Valid) {

          // PATCH object
          const URL = '/api/ports/'
          const data = {
            id: ObjectID,
            face: ObjectFace,
            partition: ObjectPartition,
            port_id: PortID,
            description: Description,
          }
          vm.$http.post(URL, data).then(response => {

            const PortIndex = vm.Ports.findIndex((port) => port.object_id == ObjectID && port.object_face == ObjectFace && JSON.stringify(port.object_partition) == JSON.stringify(ObjectPartition) && port.port_id == PortID)
            
            if (PortIndex != -1) {

              // Update port in store
              vm.$store.commit('pcmPorts/UPDATE_Port', {data:response.data})

            } else {
              
              // Add port in store
              vm.$store.commit('pcmPorts/ADD_Port', {data:response.data})

            }

          }).catch(error => {vm.DisplayError(error)})

          // Hide modal, this is necessary to close modal after submitting by click or enter
          vm.$bvModal.hide(vm.ModalID)
        }
      })
    }
  },
  mounted() {

    const vm = this
    vm.$root.$on('bv::modal::shown', (bvEvent, modalId) => {

      // Only trigger on intended modal
      if(modalId == vm.ModalID) {
        vm.PortDescription = vm.ComputedPortDescription
      }
    })
  },
  beforeDestroy(){

    const vm = this

    // Clean up
    vm.$root.$off('bv::modal::shown')
  }
}
</script>
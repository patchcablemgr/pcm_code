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
                :rules="{regex: /^[A-Za-z0-9\/]+$/}"
                #default="{ errors }"
              >
                <b-form
                  @submit.prevent="Submit"
                >
                  <b-form-input
                    v-model="ObjectName"
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

let ObjectName = null

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
      ObjectName,
    }
  },
  computed: {
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    ComputedObjectName() {

        const vm = this
        const Context = vm.Context
        const Object = vm.GetObjectSelected(Context)
        let ObjectName = 'N/A'

        if(Object) {
          ObjectName = Object.name
        }
        
        return ObjectName
    },
  },
  methods: {
    Submit: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.StateSelected[Context].object_id

      vm.$refs.validation.validate().then((Valid) => {
        if(Valid) {

          // PATCH object
          const URL = '/api/objects/'+ObjectID
          const data = {
            name: vm.ObjectName
          }
          vm.$http.patch(URL, data).then(response => {

            // Update node from store
            vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:Context, data:response.data})

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
        vm.ObjectName = vm.ComputedObjectName
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
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
                rules="required|between:1,52"
                #default="{ errors }"
              >
                <b-form
                  @submit.prevent="Submit"
                >
                  <b-form-input
                    v-model="CabinetSize"
                    type="number"
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
import { between } from '@validations'

const config = {
  useConstraintAttrs: false,
  defaultMessage: "invalid input"
}

configure(config)

let CabinetSize = null

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
    between,
  },
  directives: {},
  props: {
    ModalID: {type: String},
    Context: {type: String},
    ModalTitle: {type: String},
  },
  data () {
    return {
      CabinetSize,
    }
  },
  computed: {
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    LocationID() {

      const vm = this
      const Context = vm.Context

      return vm.StateSelected[Context].location_id
    },
    Location() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID
      let Location = false

      if(LocationID) {
        const LocationIndex = vm.GetLocationIndex(LocationID, Context)
        Location = vm.Locations[Context][LocationIndex]
      }
      
      return Location
    },
    ComputedCabinetSize() {

        const vm = this
        const Location = vm.Location
        let CabinetSize = 'N/A'

        if(Location) {
          const LocationType = Location.type
          if(LocationType == 'cabinet') {
            CabinetSize = Location.size
          }
        }
        
        return CabinetSize
    },
  },
  methods: {
    Submit: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID

      vm.$refs.validation.validate().then((Valid) => {
        if(Valid) {
          // Delete Connection
          const URL = '/api/locations/'+LocationID
          const data = {
            size: vm.CabinetSize
          }
          vm.$http.patch(URL, data).then(response => {

            // Update object RU
            if(vm.Location.ru_orientation == 'bottom-up') {

              const SizeDiff = (vm.ComputedCabinetSize > vm.CabinetSize) ? ((vm.ComputedCabinetSize - vm.CabinetSize) * -1) : (vm.CabinetSize - vm.ComputedCabinetSize)
              const Objects = vm.Objects[Context].filter(object => object.location_id == vm.LocationID && object.cabinet_ru != null)

              Objects.forEach(function(object){
                object.cabinet_ru = object.cabinet_ru + SizeDiff
                vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:Context, data:object})
              })
            }

            // Update node from store
            vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:response.data})

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
        vm.CabinetSize = vm.ComputedCabinetSize
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
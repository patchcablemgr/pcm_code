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
              :rules="{regex: /^(bottom\-up|top\-down)$/}"
              #default="{ errors }"
            >
              <b-form-select
                v-model="Adjacency"
                :options="AdjacencyOptions"
                autofocus
              />
              <small class="text-danger">{{ errors[0] }}</small>
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
import { required, regex } from '@validations'

const config = {
  useConstraintAttrs: false,
  defaultMessage: "invalid input"
}

configure(config)

let Adjacency = null
let AdjacencyOptions = []

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
    required,
    regex,
  },
  directives: {},
  props: {
    ModalID: {type: String},
    Context: {type: String},
    ModalTitle: {type: String},
    AdjacencySide: {type: String},
  },
  data () {
    return {
      Adjacency,
      AdjacencyOptions,
    }
  },
  computed: {
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    LocationID() {

      const vm = this
      const Context = vm.Context

      return vm.StateSelected[Context].location_id
    },
    ComputedCabinetOrientation() {

        const vm = this
        const Context = vm.Context
        const LocationID = vm.LocationID
        let CabinetOrientation = 'N/A'

        if(LocationID) {
          const LocationIndex = vm.GetLocationIndex(LocationID, Context)
          const Location = vm.Locations[Context][LocationIndex]
          const LocationType = Location.type
          if(LocationType == 'cabinet') {
            CabinetOrientation = Location.ru_orientation
          }
        }
        
        return CabinetOrientation
    },
  },
  methods: {
    Submit: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID

      vm.$refs.validation.validate().then((Valid) => {
        if(Valid) {

          // PATCH location
          const URL = '/api/locations/'+LocationID
          const data = {
            ru_orientation: vm.CabinetOrientation
          }
          vm.$http.patch(URL, data).then(response => {

            // Update node from store
            vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:response.data})

          }).catch(error => {vm.DisplayError(error)})
        }
      })
    }
  },
  mounted() {

    const vm = this
    const Context = vm.Context

    vm.$root.$on('bv::modal::shown', (bvEvent, modalId) => {

      // Only trigger on intended modal
      if(modalId == vm.ModalID) {
        
        const LocationID = vm.LocationID
        const LocationIndex = vm.GetLocationIndex(LocationID, Context)
        const Location = vm.Locations[Context][LocationIndex]
        const ParentID = Location.parent_id
        const ParentIndex = vm.GetLocationIndex(ParentID, Context)
        const Parent = vm.Locations[Context][ParentIndex]
        const ParentType = Parent.type

        if(ParentType == 'pod') {
          const NeighborCabinets = vm.Locations[Context].filter(location => location.parent_id == ParentID)
          NeighborCabinets.forEach(cabinet => vm.AdjacencyOptions.push({value:cabinet.id, text:cabinet.name}))
        }
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
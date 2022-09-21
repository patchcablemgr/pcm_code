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
          <b-card-title>
            <feather-icon class="mr-1" icon="MapPinIcon" />
            {{SelectedDN}}
          </b-card-title>
          <b-card-text>

            <validation-observer
              ref="validation"
              v-slot="{ invalid }"
            >
            <validation-provider
              name="Value"
              :rules="{ integer: null }"
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

    <template #modal-footer>
      <b-button
        variant="danger"
        class="float-right"
        @click="Clear"
      >
        Clear
      </b-button>
      <b-button
        variant="secondary"
        class="float-right"
        @click="Cancel"
      >
        Cancel
      </b-button>
      <b-button
        variant="primary"
        class="float-right"
        @click="Submit"
      >
        Submit
      </b-button>
    </template>

  </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BCardTitle, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'
import { configure, ValidationProvider, ValidationObserver } from 'vee-validate'
import { required } from '@validations'

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
    BCardTitle,
    BForm,
    BButton,
    BFormInput,
    BFormSelect,
    BFormCheckbox,
    BCardText,
    ValidationProvider,
    ValidationObserver,
    required,
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
    SelectedDN: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID
      
      const LocationDN = vm.GenerateLocationDN(Context, LocationID)

      return LocationDN
    },
  },
  methods: {
    Clear: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID
      const Side = vm.AdjacencySide

      const ConfirmMsg = "Clear adjacency?"
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {

        if (result === true) {

          // PATCH location
          const URL = '/api/locations/'+LocationID
          const data = (Side == 'left') ? {'left_adj_cabinet_id': null} : {'right_adj_cabinet_id': null}

          vm.$http.patch(URL, data).then(response => {

            // Update adjacent cabinet
            const OppositeAdjacencyAttribute = (Side == 'left') ? 'right_adj_cabinet_id' : 'left_adj_cabinet_id'
            const AdjacentCabinetIndex = vm.Locations[Context].findIndex((location) => location[OppositeAdjacencyAttribute] == LocationID)
            if(AdjacentCabinetIndex !== -1) {

              // Update adjacent cabinet
              const AdjacentCabinet = vm.Locations[Context][AdjacentCabinetIndex]
              const UpdatedAdjacentCabinet = JSON.parse(JSON.stringify(AdjacentCabinet), function (Key, Value) {
                if(Key == OppositeAdjacencyAttribute) {
                  return null
                } else {
                  return Value
                }
              })

              // Update store
              vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:UpdatedAdjacentCabinet})
            }

            // Update store
            vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:response.data})

            // Close modal
            vm.$root.$emit('bv::hide::modal', vm.ModalID)
          }).catch(error => {vm.DisplayError(error)})
        }
      })

    },
    Cancel: function() {

      const vm = this

      // Close modal
      vm.$root.$emit('bv::hide::modal', vm.ModalID)
    },
    Submit: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.LocationID
      const AdjacentID = vm.Adjacency
      const Side = vm.AdjacencySide

      vm.$refs.validation.validate().then((Valid) => {
        if(Valid) {

          // PATCH location
          const URL = '/api/locations/'+LocationID
          const data = (Side == 'left') ? {'left_adj_cabinet_id': AdjacentID} : {'right_adj_cabinet_id': AdjacentID}

          vm.$http.patch(URL, data).then(response => {

            // Set attribute names
            const AdjacencyAttribute = (Side == 'left') ? 'left_adj_cabinet_id' : 'right_adj_cabinet_id'
            const OppositeAdjacencyAttribute = (Side == 'right') ? 'left_adj_cabinet_id' : 'right_adj_cabinet_id'

            // Set adjacency data to clear
            const AdjacenciesToClear = [
              {
                'adj_attr': AdjacencyAttribute,
                'cabinet_id': AdjacentID
              },
              {
                'adj_attr': OppositeAdjacencyAttribute,
                'cabinet_id': LocationID
              }
            ]

            // Clear adjacencies
            AdjacenciesToClear.forEach(function(Adjacency) {

              // Get location index
              const ClearCabinetIndex = vm.Locations[Context].findIndex((location) => location[Adjacency.adj_attr] == Adjacency.cabinet_id)
              
              if(ClearCabinetIndex !== -1) {

                // Clear adjacency
                const ClearLocation = vm.Locations[Context][ClearCabinetIndex]
                const UpdatedLocation = JSON.parse(JSON.stringify(ClearLocation), function (Key, Value) {
                  if(Key == Adjacency.adj_attr) {
                    return null
                  } else {
                    return Value
                  }
                })

                // Update store
                vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:UpdatedLocation})
              }
            })

            // Update adjacent cabinet
            const AdjacentCabinetIndex = vm.Locations[Context].findIndex((location) => location.id == AdjacentID)
            if(AdjacentCabinetIndex !== -1) {

              // Update adjacent cabinet
              const AdjacentCabinet = vm.Locations[Context][AdjacentCabinetIndex]
              const UpdatedAdjacentCabinet = JSON.parse(JSON.stringify(AdjacentCabinet), function (Key, Value) {
                if(Key == OppositeAdjacencyAttribute) {
                  return LocationID
                } else {
                  return Value
                }
              })

              // Update store
              vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:UpdatedAdjacentCabinet})
            }

            // Update store
            vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:response.data})

            // Close modal
            vm.$root.$emit('bv::hide::modal', vm.ModalID)

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
        
        vm.AdjacencyOptions = []
        const LocationID = vm.LocationID
        const LocationIndex = vm.GetLocationIndex(LocationID, Context)
        const Location = vm.Locations[Context][LocationIndex]
        const ParentID = Location.parent_id
        const ParentIndex = vm.GetLocationIndex(ParentID, Context)
        const Parent = vm.Locations[Context][ParentIndex]
        const ParentType = Parent.type

        // Populate adjacency options
        if(ParentType == 'pod') {
          const NeighborCabinets = vm.Locations[Context].filter(location => location.parent_id == ParentID && location.id != LocationID)
          NeighborCabinets.forEach(cabinet => vm.AdjacencyOptions.push({value:cabinet.id, text:cabinet.name}))
        }

        // Set dropdown
        vm.Adjacency = (vm.AdjacencySide == 'left') ? Location.left_adj_cabinet_id : Location.right_adj_cabinet_id
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
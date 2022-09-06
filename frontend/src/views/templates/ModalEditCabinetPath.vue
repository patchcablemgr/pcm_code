<template>
    <b-modal
      :id="ModalID"
      title="Add"
      size="lg"
      ok-title="Submit"
      @ok="Submit"
    >
      <b-row>
        <b-col>
          <b-card
            title="Cabinet"
          >
            <b-card-body>

              <liquor-tree
                :ref="TreeRef"
                :data="[]"
                :options="LocationTreeOptions"
                class="pcm_scroll"
              >
                <span class="tree-text" slot-scope="{ node }" style="width:100%;">
                  <template>
                    <div>
                      <feather-icon :icon="node.data.icon" />
                      {{ node.text }}
                    </div>
                  </template>
                </span>
              </liquor-tree>

            </b-card-body>
          </b-card>
        </b-col>
        <b-col>
          <b-card
            title="Path Details"
          >
            <b-card-body>
              <validation-observer
                ref="validation"
                v-slot="{ invalid }"
              >
              
                <b-form
                  @submit.prevent="Submit"
                >
                  <validation-provider
                    rules="required|between:1,1000"
                    #default="{ errors }"
                  >
                    <!-- Name -->
                    <dl class="row">
                      <dt class="col-sm-4">
                        Distance (m)
                      </dt>
                      <dd class="col-sm-8">
                        <b-form-input
                          v-model="PathDistance"
                          type="number"
                          autofocus
                        />
                        <small class="text-danger">{{ errors[0] }}</small>
                      </dd>
                    </dl>
                    
                  </validation-provider>

                  <validation-provider
                    rules=""
                    #default="{ errors }"
                  >
                    <!-- Name -->
                    <dl class="row">
                      <dt class="col-sm-4">
                        Note
                      </dt>
                      <dd class="col-sm-8">
                        <b-form-input
                          v-model="PathNote"
                          type="text"
                          placeholder="Path note"
                        />
                        <small class="text-danger">{{ errors[0] }}</small>
                      </dd>
                    </dl>
                  </validation-provider>
                </b-form>
              </validation-observer>
            </b-card-body> 
          </b-card>
        </b-col>
      </b-row>
    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardBody, } from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'
import LiquorTree from 'liquor-tree'
import { configure, ValidationProvider, ValidationObserver } from 'vee-validate'
import { between } from '@validations'

const config = {
  useConstraintAttrs: false,
  defaultMessage: "invalid input"
}
configure(config)

const LocationTreeOptions = {
  "multiple": true,
}
const TreeRef = "CabinetPath"
let PathDistance = 1
let PathNote = ""

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
    BCardBody,
    ValidationProvider,
    ValidationObserver,
    between,
    LiquorTree,
  },
  directives: {},
  props: {
    ModalID: {type: String},
    Context: {type: String},
    ModalTitle: {type: String},
  },
  data () {
    return {
      LocationTreeOptions,
      TreeRef,
      PathDistance,
      PathNote,
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
    const Context = vm.Context
    const PortSelectFunction = vm.PortSelectFunction
    const TreeRef = vm.TreeRef

    vm.$root.$on('bv::modal::shown', (bvEvent, modalId) => {

      // Only trigger on intended modal
      if(modalId == vm.ModalID) {

        // Build tree
        // setTimeout is required to wait until liquor tree is rendered before manipulating it (https://www.hesselinkwebdesign.nl/2019/nexttick-vs-settimeout-in-vue/)
        setTimeout(function(){
          
          // Determine if tree will display partitions or ports
          let Scope = 'location'
          vm.BuildLocationTree({Scope})
          
        }, 0)
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
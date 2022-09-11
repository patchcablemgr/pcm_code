<template>
    <b-modal
      :id="ModalID"
      :title="ModalTitle"
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
                    <!-- Distance -->
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
                    :rules="{regex: /^[a-zA-Z0-9\s\.]{1,255}$/}"
                    #default="{ errors }"
                  >
                    <!-- Notes -->
                    <dl class="row">
                      <dt class="col-sm-4">
                        Notes
                      </dt>
                      <dd class="col-sm-8">
                        <b-form-input
                          v-model="PathNotes"
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
import { between, regex } from '@validations'

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
let PathNotes = ""

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
    regex,
    LiquorTree,
  },
  directives: {},
  props: {
    ModalID: {type: String},
    Context: {type: String},
    ModalTitle: {type: String},
    CablePathID: {type: Number},
  },
  data () {
    return {
      LocationTreeOptions,
      TreeRef,
      PathDistance,
      PathNotes,
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
    CablePaths() {
      return this.$store.state.pcmCablePaths.CablePaths
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
  },
  methods: {
    Submit: function() {

      const vm = this
      const Context = vm.Context
      const TreeRef = vm.TreeRef
      const CablePathID = vm.CablePathID
      const LocationID = vm.LocationID

      vm.$refs.validation.validate().then((Valid) => {
        if(Valid) {

          // Collect data
          const TreeSelection = vm.$refs[TreeRef].selected()
          const data = {
            cabinet_a_id: LocationID,
            cabinet_b_id: (TreeSelection[0]) ? TreeSelection[0].data.id : null,
            distance: vm.PathDistance,
            notes: vm.PathNotes,
          }

          if(CablePathID) {

            // PATCH cable path
            const URL = '/api/cable-paths/'+CablePathID
            vm.$http.patch(URL, data).then(response => {

              // Update entry in store
              vm.$store.commit('pcmCablePaths/UPDATE_CablePath', {pcmContext:Context, data:response.data})

            }).catch(error => {vm.DisplayError(error)})
          } else {

            // POST cable path
            const URL = '/api/cable-paths'
            vm.$http.post(URL, data).then(response => {

              // Add new entry to store
              vm.$store.commit('pcmCablePaths/ADD_CablePath', {pcmContext:Context, data:response.data})

            }).catch(error => {vm.DisplayError(error)})
          }

          // Hide modal, this is necessary to close modal after submitting by click or enter
          vm.$bvModal.hide(vm.ModalID)
        }
      })
    }
  },
  mounted() {

    const vm = this
    const Context = vm.Context
    const TreeRef = vm.TreeRef
    
    vm.$root.$on('bv::modal::shown', (bvEvent, modalId) => {
      const CablePathID = vm.CablePathID
      // Only trigger on intended modal
      if(modalId == vm.ModalID) {

        // Build tree
        // setTimeout is required to wait until liquor tree is rendered before manipulating it (https://www.hesselinkwebdesign.nl/2019/nexttick-vs-settimeout-in-vue/)
        setTimeout(function(){
          
          // Determine if tree will display partitions or ports
          let Scope = 'location'
          vm.BuildLocationTree({Scope})

          // Set input values if editing
          if(CablePathID) {

            const CablePathIndex = vm.GetCablePathIndex(CablePathID, Context)
            const CablePath = vm.CablePaths[Context][CablePathIndex]
            const LocationID = vm.StateSelected[Context].location_id
            const PeerID = (CablePath.cabinet_a_id == LocationID) ? CablePath.cabinet_b_id : CablePath.cabinet_a_id
            const PeerNodeID = vm.GetLocationNodeID(PeerID)
            const PeerNode = vm.GetLocationNode(PeerNodeID)

            vm.PathDistance = CablePath.distance
            vm.PathNotes = CablePath.notes
            
            if(PeerNode) {

              // Select node
              PeerNode.select(true)

              // Expand parent nodes
              if(PeerNode.parent) {
                let NodeParentID = PeerNode.parent.id
                while(NodeParentID.toString() !== '0') {
                  let NodeParent = vm.GetLocationNode(NodeParentID, TreeRef)
                  NodeParent.expand()
                  NodeParentID = NodeParent.data.parent_id
                }
              }
            }
          } else {
            vm.PathDistance = 1
            vm.PathNotes = ""
          }
          
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
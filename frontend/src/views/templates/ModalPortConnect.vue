<template>
    <!-- Template tree select modal -->
    <b-modal
      :id="ModalID"
      :title="ModalTitle"
      size="lg"
    >
      <b-row>
        <b-col>
          <b-card>
            <b-card-title>
              <feather-icon class="mr-1" icon="MapPinIcon" />
              {{SelectedDN}}
            </b-card-title>
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
      </b-row>

      <template #modal-footer>
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
import {
  BRow,
  BCol,
  BCard,
  BCardTitle,
  BCardBody,
  BButton,
} from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'
import LiquorTree from 'liquor-tree'

const LocationTreeOptions = {
  "multiple": true,
}
const TreeID = "modal-port-select"

export default {
  mixins: [PCM],
  components: {
    BRow,
    BCol,
    BCard,
    BCardTitle,
    BCardBody,
    BButton,
    LiquorTree,
  },
  directives: {},
  props: {
    ModalID: {type: String},
    ModalTitle: {type: String},
    TreeRef: {type: String},
    Context: {type: String},
    PortSelectFunction: {type: String},
  },
  data () {
    return {
      LocationTreeOptions,
      TreeID
    }
  },
  computed: {
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    Trunks() {
      return this.$store.state.pcmTrunks.Trunks
    },
    Media() {
      return this.$store.state.pcmProps.Media
    },
    Connectors() {
      return this.$store.state.pcmProps.Connectors
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    TemplateType: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.StateSelected[Context].object_id
      let TemplateType = null

      if(ObjectID) {
        const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
        const Object = vm.Objects[Context][ObjectIndex]
        if(Object.floorplan_object_type != null) {
          TemplateType = Object.floorplan_object_type
        } else {
          const TemplateID = Object.template_id
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          const Template = vm.Templates[Context][TemplateIndex]
          TemplateType = Template.type
        }
      }

      return TemplateType
    },
    SelectedDN: function() {

      const vm = this
      const SelectedDN = vm.GenerateSelectedPortDN(vm.PortSelectFunction)

      return SelectedDN
    },
  },
  methods: {
    Cancel: function() {

      const vm = this

      // Close modal
      vm.$root.$emit('bv::hide::modal', vm.ModalID)
    },
    Submit: function() {

      const vm = this
      const Context = vm.Context
      const TreeRef = vm.TreeRef
      const Criteria = function(node){
        return node.states.selected == true
      }
      const TreeSelection = vm.$refs[TreeRef].findAll(Criteria)

      const SelectedObjectID = vm.StateSelected[Context].object_id

      let FloorplanObjectType
      if(SelectedObjectID) {
        const ObjectIndex = vm.GetObjectIndex(SelectedObjectID, Context)
        const Object = vm.Objects[Context][ObjectIndex]
        FloorplanObjectType = Object.floorplan_object_type
      }
      const SelectedObjectIsFloorplan = (FloorplanObjectType == 'camera' || FloorplanObjectType == 'wap' || FloorplanObjectType == 'walljack') ? true : false
      const SelectedObjectFace = (SelectedObjectIsFloorplan) ? 'front' : vm.StateSelected[Context].object_face
      const SelectedObjectPartition = (SelectedObjectIsFloorplan) ? [0] : vm.StateSelected[Context].partition[SelectedObjectFace]
      const SelectedObjectPortID = (SelectedObjectIsFloorplan) ? null : vm.StateSelected[Context].port_id[SelectedObjectFace]

      // Compile POST data
      const data = {
        'a_id':SelectedObjectID,
        'a_face':SelectedObjectFace,
        'a_partition':SelectedObjectPartition,
        'a_port':SelectedObjectPortID
      }

      if(TreeSelection.length > 1) {
        const GroupId = Math.floor(Date.now() / 1000)
      }

      TreeSelection.forEach(function(node){
        data['b_id'] = node.data.object_id
        data['b_face'] = node.data.face
        data['b_partition'] = node.data.partition_address
        data['b_port'] = node.data.port_id

        // POST Connection
        const URL = '/api/connections'
        vm.$http.post(URL, data).then(response => {

          // Add connection to store
          vm.$store.commit('pcmConnections/ADD_Connection', {data:response.data.add})
          response.data.remove.forEach(remove => vm.$store.commit('pcmConnections/REMOVE_Connection', {data:remove}))

          // Close modal
          vm.$root.$emit('bv::hide::modal', vm.ModalID)

        }).catch(error => {vm.DisplayError(error)})
      })
    },
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
          let Scope
          if(PortSelectFunction == 'trunk') {
            Scope = (vm.TemplateType == 'standard' || vm.TemplateType == 'insert') ? 'partition' : 'port'
          } else {
            Scope = 'port'
          }

          vm.BuildLocationTree({Scope})

          const SelectedObjectID = vm.StateSelected[Context].object_id
          const SelectedObjectFace = vm.StateSelected[Context].object_face
          const SelectedObjectPartition = vm.StateSelected[Context].partition[SelectedObjectFace]

          const Object = vm.GetObjectSelected(Context)
          const FloorplanObjectType = Object.floorplan_object_type

          // Prevent users from selecting multiple partitions
          // Allow users to select multiple ports
          let MultipleSelect
          if (Scope == 'partition') {
            MultipleSelect = false
          } else {
            if (FloorplanObjectType == 'walljack') {
              MultipleSelect = true
            } else {
              MultipleSelect = false
            }
          }
          vm.$refs[TreeRef].setMultiple(MultipleSelect)
          
          if(PortSelectFunction == 'trunk') {

            const Trunks = vm.GetTrunks(SelectedObjectID, SelectedObjectFace, SelectedObjectPartition)
            const TrunksWithPortSet = Trunks.findIndex((trunk) => trunk.b_port !== null)

            if(TrunksWithPortSet == -1 || FloorplanObjectType != null) {
              vm.SelectTrunkNodes(SelectedObjectID, Trunks)
            }
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
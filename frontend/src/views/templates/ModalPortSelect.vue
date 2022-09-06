<template>
    <!-- Template port select modal -->
    <b-modal
      :id="ModalID"
      :title="ModalTitle"
      size="lg"
      ok-title="Submit"
      @ok="Submit"
      cancel-title="Clear"
      @cancel="Clear"
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
    </b-modal>
</template>

<script>
import {
  BRow,
  BCol,
  BCard,
  BCardTitle,
  BCardBody,
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
    Medium() {
      return this.$store.state.pcmProps.Medium
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
    Submit: function() {

      const vm = this
      const Context = vm.Context
      const TreeRef = vm.TreeRef
      const PortSelectFunction = vm.PortSelectFunction
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

      let PeerData = []
      TreeSelection.forEach(function(node){
        const PeerObjectID = node.data.object_id
        const PeerObjectFace = node.data.face
        const PeerObjectPartition = node.data.partition_address
        const PeerObjectPortID = node.data.port_id
        PeerData.push({'id':PeerObjectID, 'face':PeerObjectFace, 'partition':PeerObjectPartition, 'port_id':PeerObjectPortID})
      })

      // Compile POST data
      const data = {'id':SelectedObjectID, 'face':SelectedObjectFace, 'partition':SelectedObjectPartition, 'port_id':SelectedObjectPortID, 'peer_data':PeerData}

      if(PortSelectFunction == 'trunk') {

        // POST Trunk
        const URL = '/api/trunks'
        vm.$http.post(URL, data).then(response => {

          // Add trunk to store
          response.data.add.forEach(add => vm.$store.commit('pcmTrunks/ADD_Trunk', {data:add}))
          response.data.remove.forEach(remove => vm.$store.commit('pcmTrunks/REMOVE_Trunk', {data:remove}))

        }).catch(error => {vm.DisplayError(error)})

      } else if(PortSelectFunction == 'port') {

        // POST Connection
        const URL = '/api/connections'
        vm.$http.post(URL, data).then(response => {

          // Add connection to store
          response.data.add.forEach(add => vm.$store.commit('pcmConnections/ADD_Connection', {data:add}))
          response.data.remove.forEach(remove => vm.$store.commit('pcmConnections/REMOVE_Connection', {data:remove}))

        }).catch(error => {vm.DisplayError(error)})

      }
    },
    Clear: function() {

      const vm = this
      const Context = vm.Context
      const TreeRef = vm.TreeRef
      const PortSelectFunction = vm.PortSelectFunction

      const SelectedObjectID = vm.StateSelected[Context].object_id
      const SelectedObjectFace = vm.StateSelected[Context].object_face
      const SelectedObjectPartition = vm.StateSelected[Context].partition[SelectedObjectFace]

      if(PortSelectFunction == 'trunk') {

        const Trunks = vm.GetTrunks(SelectedObjectID, SelectedObjectFace, SelectedObjectPartition)

        Trunks.forEach(function(trunk){

          const TrunkID = trunk.id
          // Delete Trunk
          const URL = '/api/trunks/'+TrunkID
          vm.$http.delete(URL).then(response => {

            // Remove trunk from store
            vm.$store.commit('pcmTrunks/REMOVE_Trunk', {data:response.data})

          }).catch(error => {vm.DisplayError(error)})
        })
      } else if(PortSelectFunction == 'port') {

        const SelectedPortID = vm.StateSelected[Context].port_id[SelectedObjectFace]
        const Connection = vm.GetConnection(SelectedObjectID, SelectedObjectFace, SelectedObjectPartition, SelectedPortID)
        const ConnectionID = Connection.data.id
        
        // Delete Connection
        const URL = '/api/connections/'+ConnectionID
        vm.$http.delete(URL).then(response => {

          // Remove trunk from store
          vm.$store.commit('pcmConnections/REMOVE_Connection', {data:response.data})

        }).catch(error => {vm.DisplayError(error)})
      }
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
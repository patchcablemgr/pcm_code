<template>
    <!-- Template port select modal -->
    <b-modal
      :id="TreeID"
      title="Edit"
      size="lg"
      ok-title="OK"
      @ok="Submit"
      cancel-title="Clear"
      @cancel="Clear"
    >
      <b-row>
        <b-col>
          <b-card
            :title="ModalTitle"
          >
            <b-card-body>
              <liquor-tree
                :ref="TreeRef"
                :data="[]"
                :options="LocationTreeOptions"
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
    BCardBody,
    LiquorTree,
  },
  directives: {},
  props: {
    ModalTitle: {type: String},
    TreeRef: {type: String},
    Context: {type: String},
    PartitionAddressSelected: {type: Object},
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
    TemplateType: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
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

      const SelectedObjectID = vm.PartitionAddressSelected[Context].object_id

      let FloorplanObjectType
      if(SelectedObjectID) {
        const ObjectIndex = vm.GetObjectIndex(SelectedObjectID, Context)
        const Object = vm.Objects[Context][ObjectIndex]
        FloorplanObjectType = Object.floorplan_object_type
      }
      const SelectedObjectIsFloorplan = (FloorplanObjectType == 'camera' || FloorplanObjectType == 'wap' || FloorplanObjectType == 'walljack') ? true : false
      const SelectedObjectFace = (SelectedObjectIsFloorplan) ? 'front' : vm.PartitionAddressSelected[Context].object_face
      const SelectedObjectPartition = (SelectedObjectIsFloorplan) ? [0] : vm.PartitionAddressSelected[Context][SelectedObjectFace]

      let PeerData = []
      TreeSelection.forEach(function(node){
        const PeerObjectID = node.data.object_id
        const PeerObjectFace = node.data.face
        const PeerObjectPartition = node.data.partition_address
        const PeerObjectPortID = node.data.port_id
        PeerData.push({'id':PeerObjectID, 'face':PeerObjectFace, 'partition':PeerObjectPartition, 'port_id':PeerObjectPortID})
      })

      // Compile POST data
      const data = {'id':SelectedObjectID, 'face':SelectedObjectFace, 'partition':SelectedObjectPartition, 'port_id':null, PeerData}

      if(PortSelectFunction == 'trunk') {

        // POST Trunk
        const URL = '/api/trunks/'
        vm.$http.post(URL, data).then(response => {

          // Add trunk to store
          response.data.add.forEach(add => vm.$store.commit('pcmTrunks/ADD_Trunk', {data:add}))
          response.data.remove.forEach(remove => vm.$store.commit('pcmTrunks/REMOVE_Trunk', {data:remove}))

        }).catch(error => {vm.DisplayError(error)})
      }
    },
    Clear: function() {

      const vm = this
      const Context = vm.Context
      const TreeRef = vm.TreeRef
      const PortSelectFunction = vm.PortSelectFunction

      const SelectedObjectID = vm.PartitionAddressSelected[Context].object_id
      const SelectedObjectFace = vm.PartitionAddressSelected[Context].object_face
      const SelectedObjectPartition = vm.PartitionAddressSelected[Context][SelectedObjectFace]

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
      }
    }
  },
  mounted() {

    const vm = this
    const Context = vm.Context
    const PortSelectFunction = vm.PortSelectFunction
    const TreeRef = vm.TreeRef

    this.$root.$on('bv::modal::shown', (bvEvent, modalId) => {

      if(modalId == vm.TreeID) {

        // Build tree
        // setTimeout is required to wait until liquor tree is rendered before manipulating it (https://www.hesselinkwebdesign.nl/2019/nexttick-vs-settimeout-in-vue/)
        setTimeout(function(){
          const Scope = (vm.TemplateType == 'standard' || vm.TemplateType == 'insert') ? 'partition' : 'port'

          vm.BuildLocationTree({Scope})

          const SelectedObjectID = vm.PartitionAddressSelected[Context].object_id
          const SelectedObjectFace = vm.PartitionAddressSelected[Context].object_face
          const SelectedObjectPartition = vm.PartitionAddressSelected[Context][SelectedObjectFace]
          if(PortSelectFunction == 'trunk') {

            const Trunks = vm.GetTrunks(SelectedObjectID, SelectedObjectFace, SelectedObjectPartition)
            Trunks.forEach(function(trunk){

              const peerID = (trunk.a_id == SelectedObjectID) ? trunk.b_id : trunk.a_id
              const peerFace = (trunk.a_id == SelectedObjectID) ? trunk.b_face : trunk.a_face
              const peerPartition = (trunk.a_id == SelectedObjectID) ? trunk.b_partition : trunk.a_partition
              const peerPortID = (trunk.a_id == SelectedObjectID) ? trunk.b_port : trunk.a_port

              const Criteria = function(node){
                let match = false

                if(node.data.object_id == peerID) {
                  if(node.data.face == peerFace) {
                    let i = node.data.partition_address.length
                    let PartitionMatch = true
                    while (i--) {
                      if (node.data.partition_address[i] !== peerPartition[i]) {
                        PartitionMatch = false
                      }
                    }
                    if(PartitionMatch) {
                      if(node.data.port_id == peerPortID) {
                        match = true
                      }
                    }
                  }
                }

                return match
              }
              let Nodes = vm.$refs[TreeRef].findAll(Criteria)
              //Nodes.select(true)
              Nodes.forEach(function(Node){
                Node.select(true)
                // Expand parent nodes
                if(Node.parent) {
                  let NodeParentID = Node.parent.id
                  while(NodeParentID.toString() !== '0') {
                    let NodeParent = vm.GetLocationNode(NodeParentID)
                    NodeParent.expand()
                    if(NodeParent.parent) {
                      NodeParentID = NodeParent.parent.id
                    } else {
                      NodeParentID = 0
                    }
                  }
                }
              })
            })
          }

          // Prevent nodes from being selected
          vm.$refs[TreeRef].$on('node:selected', function(node){
            const AllowedNodeTypes = [
              'partition',
              'port'
            ]
            const NodeType = node.data.type
            if(!AllowedNodeTypes.includes(NodeType)) {
              node.unselect()
            }
          })
        }, 0)
      }
    })
  },
}
</script>
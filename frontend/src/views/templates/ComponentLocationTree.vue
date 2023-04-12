<template>
  <div>
    <liquor-tree
      :ref="TreeRef"
      :data="[]"
      :options="LocationTreeOptions"
      class="pcm_scroll"
    >
      <span class="tree-text" slot-scope="{ node }" style="width:100%;">
        <template>
          <div @contextmenu.prevent.stop="handleClick($event, {'node_id': node.data.id})">
            <feather-icon :icon="node.data.icon" />
            {{ node.text }}
          </div>
        </template>
      </span>
    </liquor-tree>

    <!-- Context Menu -->
    <vue-simple-context-menu
      v-if="TreeIsContextual"
      :elementId="'myUniqueId'"
      :options="MenuOptions"
      :ref="'vueSimpleContextMenu'"
      @option-clicked="optionClicked"
      class="context_menu_option"
    />
  </div>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import LiquorTree from 'liquor-tree'
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css'
import VueSimpleContextMenu from 'vue-simple-context-menu'
import { PCM } from '@/mixins/PCM.js'

const LocationTreeOptions = {
  "dnd": true,
  "multiples": false,
}

const MenuOptions = [
  {
    "name": "Rename",
    "action": "rename",
  },
  {
    "type": "divider",
  },
  {
    "name": "New Location",
    "action": "location",
  },
  {
    "name": "New Pod",
    "action": "pod",
  },
  {
    "type": "divider",
  },
  {
    "name": "New Cabinet",
    "action": "cabinet",
  },
  {
    "name": "New Floorplan",
    "action": "floorplan",
  },
  {
    "type": "divider",
  },
  {
    "name": "Delete",
    "action": "delete",
  }
]

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,

    LiquorTree,
    VueSimpleContextMenu,
  },
  props: {
    Context: {type: String},
    TreeRef: {type: String},
    TreeIsContextual: {type: Boolean},
  },
  data() {
    return {
      LocationTreeOptions,
      MenuOptions
    }
  },
  computed: {
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    CablePaths() {
      return this.$store.state.pcmCablePaths.CablePaths
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
  },
  watch: {
    Locations: {
      handler(newValue, oldValue) {
        const Scope = 'location'
        this.RebuildLocationTree({Scope})
      },
      deep: true
    }
  },
  methods: {
    GetCookie(cname) {
      let name = cname + "="
      let decodedCookie = decodeURIComponent(document.cookie)
      let ca = decodedCookie.split(';')
      for(let i = 0; i <ca.length; i++) {
        let c = ca[i]
        while (c.charAt(0) == ' ') {
          c = c.substring(1)
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length)
        }
      }
      return ""
    },
    optionClicked (event) {

      const vm = this
      const Context = vm.Context
      const TreeRef = vm.TreeRef
      const Action = event.option.action
      const LocationID = event.item.node_id

      if(Action == 'rename') {

        // Rename location
        const Criteria = function(node){
          return node.data.id == LocationID
        }
        Node = vm.$refs[TreeRef].find(Criteria)[0]
        Node.startEditing()

      } else if(Action == 'delete') {

        // Delete location
        const url = '/api/locations/'+LocationID
        vm.$http.delete(url).then(function(response){

          // Clear node selection
          vm.$store.commit('pcmState/DEFAULT_Selected_All', {pcmContext:Context})

          response.data.cable_path.forEach(function(DeletedCablePathID){
            // Clear stale cable paths
            const StaleCablePaths = vm.CablePaths[Context].filter((CablePath) => CablePath.cabinet_a_id == DeletedCablePathID || CablePath.cabinet_b_id == DeletedCablePathID)
            StaleCablePaths.forEach(function(StaleCablePath) {
              vm.$store.commit('pcmCablePaths/REMOVE_CablePath', {pcmContext:Context, data:StaleCablePath})
            })
          })

          response.data.location.forEach(function(DeletedLocationID){

            // Clear stale adjacencies
            const AdjacencyAttributes = ['right_adj_cabinet_id', 'left_adj_cabinet_id']
            AdjacencyAttributes.forEach(function(AdjacencyAttribute) {
              const AdjacentCabinetIndex = vm.Locations[Context].findIndex((location) => location[AdjacencyAttribute] == DeletedLocationID)
              if(AdjacentCabinetIndex !== -1) {

                // Update adjacent cabinet
                const AdjacentCabinet = vm.Locations[Context][AdjacentCabinetIndex]
                const UpdatedAdjacentCabinet = JSON.parse(JSON.stringify(AdjacentCabinet), function (Key, Value) {
                  if(Key == AdjacencyAttribute) {
                    return null
                  } else {
                    return Value
                  }
                })

                // Update store
                vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:UpdatedAdjacentCabinet})
              }
            })

            // Delete node from tree
            const Criteria = function(node){
              return node.data.id == DeletedLocationID
            }
            let Node = vm.$refs[TreeRef].find(Criteria)[0]
            Node.remove()

            // Remove node from store
            vm.$store.commit('pcmLocations/REMOVE_Location', {pcmContext:Context, data:{id:DeletedLocationID}})

          })
        }).catch(error => {vm.DisplayError(error)})

      } else {

        // Add location
        const url = '/api/locations'
        const data = {
          "parent_id": LocationID,
          "type": Action
        }

        // POST to locations
        vm.$http.post(url, data).then(function(response){

          const Node = response.data

          // Add node to store
          vm.$store.commit('pcmLocations/ADD_Location', {pcmContext:Context, data:Node})
          
          // Create child node object
          const Child = {
            "id": Node.id,
            "text": Node.name,
            "data": {
              "id": Node.id,
              "type": Node.type,
              "icon": vm.GetNodeIcon(Node.type),
              "parent_id": Node.parent_id,
              "img": Node.img,
            },
          }

          // Append child node object to parent
          const Criteria = function(node){
            return node.data.id == LocationID
          }
          let ParentNode = vm.$refs[TreeRef].find(Criteria)[0]
          ParentNode.append(Child)

        }).catch(error => {vm.DisplayError(error)})
      }
    },
    handleClick (event, node) {
      const vm = this

      if (vm.TreeIsContextual) {
        vm.$refs.vueSimpleContextMenu.showMenu(event, node)
      }
    },
  },
  mounted() {

    const vm = this
    const Context = vm.Context
    const TreeRef = vm.TreeRef

    // $nextTick is required to allow time for the $refs[TreeRef] to be rendered
    setTimeout(function(){

      // Build the location tree data
      const Scope = 'location'
      vm.BuildLocationTree({Scope})

      // Update selected node
      vm.$refs[TreeRef].$on('node:selected', (node) => {

        const LocationID = node.data.id
        const NodeID = node.id
        let UpdateData = {
          location_id: LocationID,
          node_id: NodeID
        }

        // Preserve selected object/face/partition/portID if a child of selected location
        const SelectedObjectIndex = vm.GetSelectedObjectIndex(Context)
        if(SelectedObjectIndex !== -1) {
          const Object = vm.Objects[Context][SelectedObjectIndex]
          const ObjectLocationID = Object.location_id
          if(ObjectLocationID == LocationID) {
            UpdateData.object_id = vm.StateSelected[Context].object_id
            UpdateData.object_face = vm.StateSelected[Context].object_face
            UpdateData.partition = vm.StateSelected[Context].partition
            UpdateData.port_id = vm.StateSelected[Context].port_id
          }
        }

        vm.$store.commit('pcmState/DEFAULT_Selected_All', {pcmContext:Context})
        vm.$store.commit('pcmState/UPDATE_Selected', {pcmContext:Context, data:UpdateData})
      })
      
      // Update node text
      vm.$refs[TreeRef].$on('node:editing:stop', (node) => {

        // Store data
        const NodeID = node.data.id
        const NodeText = node.text
        const url = '/api/locations/'+NodeID
        const data = {"name": NodeText}

        // PATCH category form data
        vm.$http.patch(url, data).then(function(response){
          
          // Update node from store
          vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:response.data})

        }).catch(error => { vm.DisplayError(error) })
      })

      // Update node parent
      vm.$refs[TreeRef].$on('node:dragging:finish', (node) => {

        const LocationID = node.data.id
        const ParentID = (node.parent) ? node.parent.data.id : 0

        // Determine node order
        let NodeOrder
        if(node.parent) {
          NodeOrder = node.parent.children.findIndex(child => child.id == LocationID)
        } else {
          NodeOrder = vm.$refs[TreeRef].tree.model.findIndex(child => child.id == LocationID)
        }

        // Store data
        const url = '/api/locations/'+LocationID
        const data = {"parent_id": ParentID}
        const Scope = 'location'

        // PATCH category form data
        vm.$http.patch(url, data).then(function(response){

          // Clear adjacent cabinets
          const LocationIndex = vm.GetLocationIndex(LocationID, Context)
          const Location = vm.Locations[Context][LocationIndex]
          const LocationParentID = Location.parent_id

          if(ParentID != LocationParentID) {

            // Update adjacent cabinet
            const AdjacencyAttributes = ['right_adj_cabinet_id', 'left_adj_cabinet_id']
            AdjacencyAttributes.forEach(function(AdjacencyAttribute) {
              const AdjacentCabinetIndex = vm.Locations[Context].findIndex((location) => location[AdjacencyAttribute] == LocationID)
              if(AdjacentCabinetIndex !== -1) {

                // Update adjacent cabinet
                const AdjacentCabinet = vm.Locations[Context][AdjacentCabinetIndex]
                const UpdatedAdjacentCabinet = JSON.parse(JSON.stringify(AdjacentCabinet), function (Key, Value) {
                  if(Key == AdjacencyAttribute) {
                    return null
                  } else {
                    return Value
                  }
                })

                // Update store
                vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:UpdatedAdjacentCabinet})
              }
            })
          }
          
          // Update node from store
          vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:response.data})

          vm.RebuildLocationTree({Scope})
          
        }).catch(error => {
          vm.DisplayError(error)
          vm.RebuildLocationTree({Scope})
        })
      })

      const SelectedNodeID = vm.StateSelected[Context].node_id ? vm.StateSelected[Context].node_id : ""
      if(SelectedNodeID != "") {

        // Select previously viewed node
        let Node = vm.GetLocationNode(SelectedNodeID, TreeRef)
        if(Node) {
          Node.select(true)

          // Expand parent nodes
          while(Node.parent) {
            let NodeParent = vm.GetLocationNode(Node.parent.id, TreeRef)
            NodeParent.expand()
            Node = NodeParent
          }
        }
        
      }
    }, 0)
  }
}
</script>
<template>
  <div>
    <liquor-tree
      :ref="TreeRef"
      :data="[]"
      :options="LocationTreeOptions"
    >
      <span class="tree-text" slot-scope="{ node }" style="width:100%;">
        <template>
          <div @contextmenu.prevent.stop="handleClick($event, {'node_id': node.id})">
            <feather-icon :icon="node.data.icon" />
            {{ node.text }}
          </div>
        </template>
      </span>
    </liquor-tree>

    <!-- Context Menu -->
    <vue-simple-context-menu
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
      const NodeID = event.item.node_id

      if(Action == 'rename') {

        // Rename location
        const Criteria = {
          "id": NodeID.toString()
        }
        Node = vm.$refs[TreeRef].find(Criteria)[0]
        Node.startEditing()

      } else if(Action == 'delete') {

        // Delete location
        const url = '/api/locations/'+NodeID

        // DELETE category form data
        vm.$http.delete(url).then(function(response){

          // Remove node from store
          vm.$store.commit('pcmLocations/REMOVE_Location', response.data)

          // Clear node selection
          vm.$emit('LocationNodeSelected', {id:null})

          const ReturnedLocationID = response.data.id

          // Delete node from tree
          const Criteria = {
            "id": ReturnedLocationID.toString()
          }
          let Node = vm.$refs[TreeRef].find(Criteria)[0]
          Node.remove()
          
        }).catch(error => {vm.DisplayError(error)})

      } else {

        // Add location
        const url = '/api/locations'
        const data = {
          "parent_id": NodeID,
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
              "type": Node.type,
              "icon": vm.GetNodeIcon(Node.type),
              "parent_id": Node.parent_id,
              "img": Node.img,
            },
          }

          // Append child node object to parent
          const Criteria = {
            "id": NodeID.toString()
          }
          let ParentNode = vm.$refs[TreeRef].find(Criteria)[0]
          ParentNode.append(Child)

        }).catch(error => {vm.DisplayError(error)})
      }
    },
    handleClick (event, node) {
      this.$refs.vueSimpleContextMenu.showMenu(event, node)
    },
  },
  mounted() {

    const vm = this
    const Context = vm.Context
    const TreeRef = vm.TreeRef

    vm.$nextTick(function () {

      // Build the location tree data
      vm.BuildLocationTree()

      // Update selected node
      vm.$refs[TreeRef].$on('node:selected', (node) => {

        const NodeID = node.id
        vm.$emit('LocationNodeSelected', {"id":NodeID})
        document.cookie = "environment_location_nodeID="+NodeID
      })
      
      // Update node text
      vm.$refs[TreeRef].$on('node:editing:stop', (node) => {

        // Store data
        const NodeID = node.id
        const NodeText = node.text
        const url = '/api/locations/'+NodeID
        const data = {"text": NodeText}

        // PATCH category form data
        vm.$http.patch(url, data).then(function(response){
          
          // Update node from store
          vm.$store.commit('pcmLocations/UPDATE_Location', {pcmContext:Context, data:response.data})

        }).catch(error => { vm.DisplayError(error) })
      })

      // Update node parent
      vm.$refs[TreeRef].$on('node:dragging:finish', (node) => {

        const NodeID = node.id
        const Parent = node.parent
        let ParentID = 0

        if(Parent !== null) {
          ParentID = Parent.id
        }

        // Store data
        const url = '/api/locations/'+NodeID
        const data = {"parent": ParentID}

        // PATCH category form data
        vm.$http.patch(url, data).then(function(response){
          //
        }).catch(error => { vm.DisplayError(error) })
      })

      const SelectedNodeID = vm.GetCookie('environment_location_nodeID')
      if(SelectedNodeID != "") {

        // Select previously viewed node
        let Node = vm.GetLocationNode(SelectedNodeID, TreeRef)
        Node.select(true)

        // Expand parent nodes
        let NodeParentID = Node.data.parent_id
        while(NodeParentID.toString() !== '0') {
          let NodeParent = vm.GetLocationNode(NodeParentID, TreeRef)
          NodeParent.expand()
          NodeParentID = NodeParent.data.parent_id
        }
        
      }
    })
  }
}
</script>
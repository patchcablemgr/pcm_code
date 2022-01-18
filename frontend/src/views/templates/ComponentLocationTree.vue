<template>
  <liquor-tree
    ref="LocationTree"
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
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import LiquorTree from 'liquor-tree'
import { PCM } from '@/mixins/PCM.js'

const LocationTreeOptions = {
  "dnd": true,
  "multiples": false,
}

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,

    LiquorTree,
  },
  props: {
    Context: {type: String},
  },
  data() {
    return {
      LocationTreeOptions,
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
    GetLocationNode(NodeID) {

      const vm = this

      const Criteria = {
        "id": NodeID.toString()
      }
      let Node = vm.$refs.LocationTree.find(Criteria)[0]

      return Node
    },
    BuildLocationTree: function(Parent=false){

      const vm = this
      const Context = vm.Context
      Parent = (Parent) ? Parent : { id: 0 }
      const ParentID = Parent.id
      const ChildrenFiltered = vm.Locations[Context].filter(location => location.parent_id == ParentID)
      const ChildrenData = []

      ChildrenFiltered.forEach(function(child) {
        const ChildData = {
          "id": child.id,
          "text": child.name,
          "data": {
            "type": child.type,
            "icon": vm.GetNodeIcon(child.type),
            "parent_id": child.parent_id,
            "img": child.img,
          },
        }
        ChildrenData.push(ChildData)
      })

      if(ChildrenData.length) {
        
        ChildrenData.forEach(function(child) {

          if(ParentID == 0) {

            vm.$refs.LocationTree.append(child)
          } else {

            let ParentNode = vm.GetLocationNode(ParentID)
            ParentNode.append(child)
          }
        })

        ChildrenData.forEach(child => vm.BuildLocationTree(child))                
      }
      
      return
    },
  },
  mounted() {

    const vm = this
    const Context = vm.Context

    vm.$nextTick(function () {

      // Build the location tree data
      vm.BuildLocationTree()

      // Update selected node
      vm.$refs.LocationTree.$on('node:selected', (node) => {

        const NodeID = node.id
        vm.$emit('LocationNodeSelected', {"id":NodeID})
        document.cookie = "environment_location_nodeID="+NodeID

        // Store data
        const url = '/api/locations/'+NodeID

        // PATCH category form data
        vm.$http.get(url).then(function(response){
          vm.CabinetData = response.data
        }).catch(error => { vm.DisplayError(error) })
      })
      
      // Update node text
      vm.$refs.LocationTree.$on('node:editing:stop', (node) => {

        // Store data
        const NodeID = node.id
        const NodeText = node.text
        const url = '/api/locations/'+NodeID
        const data = {"text": NodeText}

        // PATCH category form data
        vm.$http.patch(url, data).then(function(response){
          //
        }).catch(error => { vm.DisplayError(error) })
      })

      // Update node parent
      vm.$refs.LocationTree.$on('node:dragging:finish', (node) => {

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
        let Node = vm.GetLocationNode(SelectedNodeID)
        Node.select(true)
        // Expand parent nodes
        let NodeParentID = Node.data.parent_id
        while(NodeParentID.toString() !== '0') {
          let NodeParent = vm.GetLocationNode(NodeParentID)
          NodeParent.expand()
          NodeParentID = NodeParent.data.parent_id
        }
        
      }
    })
  }
}
</script>
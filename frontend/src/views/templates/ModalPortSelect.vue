<template>
    <!-- Template port select modal -->
    <b-modal
      id="modal-port-select"
      title="Edit"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>
          <b-card
            :title="ModalTitle"
          >
            <b-card-body>

              <liquor-tree
                ref="PortSelectTree"
                :data="TreeData"
                :options="TreeOptions"
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
import { PCM } from '../../mixins/PCM.js'
import LiquorTree from 'liquor-tree'

const TreeData = []

const TreeOptions = {
  "dnd": true,
  "multiples": false,
}

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
    ObjectData: {type: Object},
    TemplateData: {type: Object},
    LocationData: {type: Array},
  },
  data () {
    return {
      TreeData,
      TreeOptions
    }
  },
  computed: {
  },
  methods: {
    BuildLocationTree: function(array, Parent){

      const vm = this
      Parent = typeof Parent !== 'undefined' ? Parent : { id: 0 }
      const ParentID = Parent.id
      const ChildrenFiltered = array.filter(location => location.parent_id == ParentID)
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

            vm.TreeData.push(child)
          } else {

            let ParentNode = vm.GetLocationNode(ParentID)
            if(ParentNode.hasOwnProperty('children')) {
              ParentNode.children.push(child)
            } else {
              ParentNode.children = [child]
            }
          }
        })

        ChildrenData.forEach(child => vm.BuildLocationTree(array, child))                
      }
      
      return
    },
    GetLocationNode(NodeID, Tree=false) {

      const vm = this
      Tree = (Tree) ? Tree : vm.TreeData
      const Node = Tree.find(element => element.id == NodeID)

      if(Node === 'undefined') {
        Tree.forEach(function(element){
          if(element.hasOwnProperty('children')) {
            vm.GetLocationNode(NodeID, element.children)
          }
        })
      } else {
        return Node
      }
    },
    GETLocations: function () {

      const vm = this
      
      vm.$http.get('/api/locations').then(function(response){

        vm.BuildLocationTree(response.data)

      })
    },
  },
  mounted() {

    const vm = this
    vm.GETLocations()
  },
}
</script>
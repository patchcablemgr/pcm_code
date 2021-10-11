<template>
  <div>
    <b-container class="bv-example-row" fluid="xs">
      <b-row>
        <b-col>
          <b-card
            title="Locations and Cabinets"
          >
            <b-card-body>
              <liquor-tree
                ref="LiquorTree"
                :data="TreeData"
                :options="TreeOptions"
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
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>
          <b-card
            title="Cabinet"
          >
            <b-card-body
              v-if=" PreviewDisplay == 'cabinet' "
            >
              <b-form-radio
                v-model="TemplateFaceSelected.preview"
                plain
                value="front"
                :disabled="TemplateFaceToggleIsDisabled"
              >Front
              </b-form-radio>
              <b-form-radio
                v-model="TemplateFaceSelected.preview"
                plain
                value="rear"
                :disabled="TemplateFaceToggleIsDisabled"
              >
                Rear
              </b-form-radio>
              <component-cabinet
                :TemplateData="TemplateData"
                :CabinetData="CabinetData"
                :CategoryData="CategoryData"
                :ObjectData="ObjectData"
                Context="preview"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressSelected="PartitionAddressSelected"
                :PartitionAddressHovered="PartitionAddressHovered"
                @PartitionClicked=" PartitionClicked($event) "
                @PartitionHovered=" PartitionHovered($event) "
              />
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>
          <b-card
            title="Card 3"
          >
            <b-card-body>
              Card body
            </b-card-body>
          </b-card>

        </b-col>
      </b-row>
    </b-container>

    <!-- Toast -->
    <toast-general/>

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
import { BContainer, BRow, BCol, BCard, BCardBody, BCardText, BFormRadio, } from 'bootstrap-vue'
import ToastGeneral from './templates/ToastGeneral.vue'
import ComponentCabinet from './templates/ComponentCabinet.vue'
import LiquorTree from 'liquor-tree'
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css'
import VueSimpleContextMenu from 'vue-simple-context-menu'

const TreeData = []

const TreeOptions = {
  "dnd": true,
  "multipls": false,
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

const CabinetData = {
  "id": StandardTemplateID,
  "size": 25,
  "name": "Cabinet",
}

const ObjectData = {
  'preview': [],
  'template': [],
}

const CategoryData = [
  {
    "id": 0,
    "color": "#FFFFFFFF",
  }
]

const TemplateData = {
  'preview': [],
  'template': [],
}

const TemplateFaceSelected = {
  'preview': 'front',
  'template': 'front',
}

const PartitionAddressSelected = {
  'preview': {
    'template_id': null,
    'front': [0],
    'rear': [0]
  },
  'template': {
    'template_id': null,
    'front': [0],
    'rear': [0]
  }
}
const PartitionAddressHovered = {
  'preview': {
    'template_id': null,
    'front': false,
    'rear': false
  },
  'template': {
    'template_id': null,
    'front': false,
    'rear': false
  }
}

export default {
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardText,
    BFormRadio,

    ToastGeneral,
    ComponentCabinet,
    LiquorTree,
    VueSimpleContextMenu,
  },
  data() {
    return {
      TreeData,
      TreeOptions,
      MenuOptions,
      CabinetData,
      CategoryData,
      ObjectData,
      TemplateData,
      TemplateFaceSelected,
      PartitionAddressSelected,
      PartitionAddressHovered,
    }
  },
  computed: {
    PreviewDisplay: function() {

      const vm = this

      const SelectedNode = vm.$refs.LiquorTree.selected()[0]
      const NodeType = SelectedNode.data.type
      let PreviewDisplay = "none"

      if(NodeType == 'location') {
        PreviewDisplay = "none"
      } else if(NodeType == 'pod') {
        PreviewDisplay = "none"
      } else if(NodeType == 'cabinet') {
        PreviewDisplay = "cabinet"
      } else if(NodeType == 'floorplan') {
        PreviewDisplay = "floorplan"
      }

      return PreviewDisplay
    }
  },
  methods: {
    GetNodeIcon: function(NodeType) {

      let Icon = "HomeIcon"
      if(NodeType == 'location') {
        Icon = "HomeIcon"
      } else if(NodeType == 'pod') {
        Icon = "CircleIcon"
      } else if(NodeType == 'cabinet') {
        Icon = "ServerIcon"
      } else if(NodeType == 'floorplan') {
        Icon = "MapIcon"
      }

      console.log("Debug (Icon):"+Icon)

      return Icon
    },
    handleClick (event, node) {
      this.$refs.vueSimpleContextMenu.showMenu(event, node)
    },
    optionClicked (event) {

      const vm = this
      const Action = event.option.action
      const NodeID = event.item.node_id

      if(Action == 'rename') {

        // Rename location
        const Criteria = {
          "id": NodeID.toString()
        }
        Node = vm.$refs.LiquorTree.find(Criteria)[0]
        Node.startEditing()
      } else if(Action == 'delete') {

        // Delete location
        const url = '/api/locations/'+NodeID

        // DELETE category form data
        vm.$http.delete(url).then(function(response){

          const ReturnedLocationID = response.data.id

          // Append child node object to parent
          const Criteria = {
            "id": ReturnedLocationID.toString()
          }
          let Node = vm.$refs.LiquorTree.find(Criteria)[0]
          Node.remove()
          
        }).catch(error => {

          // Display error to user via toast
          vm.$bvToast.toast(JSON.stringify(error.response.data), {
            title: 'Error',
            variant: 'danger',
          })

        });
      } else {

        // Add location
        vm.AddLocation(NodeID, Action)
      }
    },
    AddLocation: function(ParentID, Type) {
      // Store data
      const vm = this
      const url = '/api/locations'
      const data = {
        "parent_id": ParentID,
        "type": Type
      }

      // POST to locations
      vm.$http.post(url, data).then(function(response){

        const Node = response.data
        
        // Create child node object
        const Child = {
          "id": Node.id,
          "text": Node.name,
          "data": {
            "type": Node.type,
            "icon": vm.GetNodeIcon(Node.type),
            "parent_id": Node.parent_id,
          },
        }

        // Append child node object to parent
        const Criteria = {
          "id": ParentID.toString()
        }
        let ParentNode = vm.$refs.LiquorTree.find(Criteria)[0]
        ParentNode.append(Child)

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      })
    },
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
          },
        }
        ChildrenData.push(ChildData)
      })

      if(ChildrenData.length) {
        
        ChildrenData.forEach(function(child) {

          if(ParentID == 0) {

            vm.$refs.LiquorTree.append(child)
          } else {

            const Criteria = {
              "id": ParentID.toString()
            }
            let ParentNode = vm.$refs.LiquorTree.find(Criteria)[0]
            ParentNode.append(child)
          }
        })

        ChildrenData.forEach(child => vm.BuildLocationTree(array, child))                
      }
      
      return
    },
    LocationsGET: function () {

      const vm = this
      
      this.$http.get('/api/locations').then(function(response){

        vm.BuildLocationTree(response.data)

      })
    },
  },
  mounted() {

    const vm = this
    
    vm.$refs.LiquorTree.$on('node:editing:stop', (node, isTextChanged) => {

      // Store data
      const vm = this
      const NodeID = node.id
      const NodeText = node.text
      const url = '/api/locations/'+NodeID
      const data = {"text": NodeText}

      // PATCH category form data
      vm.$http.patch(url, data).then(function(response){
        
        //

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    })
  },
}
</script>

<style>

</style>

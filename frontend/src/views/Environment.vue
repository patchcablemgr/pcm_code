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
                <span class="tree-text" slot-scope="{ node }">
                  <template>
                    <div @contextmenu.prevent.stop="handleClick($event, {'node_id': node.id})">
                      <i class="ion-android-star"></i>
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
            title="Card 2"
          >
            <b-card-body>
              Card body
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
import LiquorTree from 'liquor-tree'
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css'
import VueSimpleContextMenu from 'vue-simple-context-menu'

const TreeData = []

const TreeOptions = {
  "dnd": true,
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
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardText,
    BFormRadio,

    ToastGeneral,
    LiquorTree,
    VueSimpleContextMenu,
  },
  data() {
    return {
      TreeData,
      TreeOptions,
      MenuOptions,
    }
  },
  computed: {
  },
  methods: {
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
        
        // Create child node object
        const Child = {
          "id": response.id,
          "text": response.name,
          "parent_id": response.parent_id
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
          "parent_id": child.parent_id
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

    vm.LocationsGET()
    this.$refs.LiquorTree.$on('node:editing:start', (node) => {
      console.log('Start editing: ' + node.text)
    })
    
    this.$refs.LiquorTree.$on('node:editing:stop', (node, isTextChanged) => {
      console.log('Stop editing: ' + node.text + ', ' + isTextChanged)
    })
  },
}
</script>

<style>

</style>

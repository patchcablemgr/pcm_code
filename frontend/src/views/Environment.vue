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
              >Front
              </b-form-radio>
              <b-form-radio
                v-model="TemplateFaceSelected.preview"
                plain
                value="rear"
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

          <component-template-Object-details
            CardTitle="Object Details"
						:TemplateData="TemplateData"
						:CategoryData="CategoryData"
						Context="template"
						:TemplateFaceSelected="TemplateFaceSelected"
						:PartitionAddressSelected="PartitionAddressSelected"
						:TemplatePartitionPortRange="TemplatePartitionPortRange"
            @TemplateObjectEditClicked="TemplateObjectEditClicked()"
            @TemplateObjectCloneClicked="TemplateObjectCloneClicked()"
						@TemplateObjectDeleteClicked="TemplateObjectDeleteClicked()"
					/>

          <component-templates
            :TemplateData="TemplateData"
            :CategoryData="CategoryData"
            :ObjectData="ObjectData"
            Context="template"
            :TemplateFaceSelected="TemplateFaceSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @PartitionClicked="PartitionClicked($event)"
            @PartitionHovered="PartitionHovered($event)"
            @TemplateFaceChanged="TemplateFaceChanged($event)"
          />

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
import ComponentTemplateObjectDetails from './templates/ComponentTemplateObjectDetails.vue'
import ComponentTemplates from './templates/ComponentTemplates.vue'
import LiquorTree from 'liquor-tree'
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css'
import VueSimpleContextMenu from 'vue-simple-context-menu'

const TreeData = []

const TreeOptions = {
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

const CabinetData = {
  "id": 0,
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
  },
  'object': {
    'object_id': null,
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
const GenericObject = {
    "id": null,
    "pseudo": true,
    "name": null,
    "template_id": null,
    "location_id": null,
    "cabinet_ru": null,
    "cabinet_face": null,
    "parent_id": null,
    "parent_face": null,
    "parent_part_addr": null,
    "parent_enc_addr": null,
}
const GenericTemplate = {
    "id": null,
    "pseudo": true,
    "name": "PseudoTemplate",
    "category_id": null,
    "type": null,
    "ru_size": null,
    "function": null,
    "mount_config": "2-post",
    "insert_constraints": null,
    "blueprint": {
        "front": [{
                "type": "generic",
                "units": null,
                "children": [{
                        "type": "enclosure",
                        "units": null,
                        "children": [],
                        "enc_layout": {
                            "cols": null,
                            "rows": null,
                        },
                    },
                ],
            },
        ],
        "rear": []
    },
}

const NodeIDSelected = null

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
    ComponentTemplateObjectDetails,
    ComponentTemplates,
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
      GenericObject,
      GenericTemplate,
      NodeIDSelected,
    }
  },
  computed: {
    PreviewDisplay: function() {

      const vm = this
      const NodeID = vm.NodeIDSelected
      let PreviewDisplay = "none"

      if(NodeID) {

        const Criteria = {
          "id": NodeID.toString()
        }
        const SelectedNode = vm.$refs.LiquorTree.find(Criteria)[0]
        const NodeType = SelectedNode.data.type

        if(NodeType == 'location') {
          PreviewDisplay = "none"
        } else if(NodeType == 'pod') {
          PreviewDisplay = "none"
        } else if(NodeType == 'cabinet') {
          PreviewDisplay = "cabinet"
        } else if(NodeType == 'floorplan') {
          PreviewDisplay = "floorplan"
        }
      }

      return PreviewDisplay
    },
    TemplatePartitionPortRange: function(){
      
      // Initialize some variables
      const vm = this
      const Context = 'template'
      const TemplateID = vm.PartitionAddressSelected[Context].template_id
      let PortRangeString = '-'

      if(TemplateID) {

        // Get template partition address
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const TemplatePartition = vm.PartitionAddressSelected[Context][TemplateFace]

        // Get template blueprint
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
        const Template = vm.TemplateData[Context][TemplateIndex]
        const TemplateBlueprint = Template.blueprint[TemplateFace]

        // Get template partition
        const Partition = vm.GetPartition(TemplateBlueprint, TemplatePartition)
        const PartitionType = Partition.type

        if(PartitionType == 'connectable') {

          // Calculate port numbers
          const PortFormat = Partition.port_format
          const PortLayoutCols = Partition.port_layout.cols
          const PortLayoutRows = Partition.port_layout.rows
          const PortTotal = PortLayoutCols * PortLayoutRows
          const FirstPortID = 0
          const LastPortID = PortTotal - 1

          const FirstPortIDString = vm.GeneratePortID(FirstPortID, PortTotal, PortFormat)
          const LastPortIDString = vm.GeneratePortID(LastPortID, PortTotal, PortFormat)

          PortRangeString = FirstPortIDString+' - '+LastPortIDString
        }
      }

      return PortRangeString

    },
  },
  methods: {
    GetTemplateIndex: function(TemplateID, Context) {

      const vm = this
      const TemplateIndex = vm.TemplateData[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    PartitionHovered: function(EmitData) {

      // Store variables
      const vm = this
      const Context = EmitData.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddress = EmitData.PartitionAddress
      const HoverState = EmitData.HoverState
      const TemplateID = EmitData.TemplateID
			
			// Hovered partition should not be highlighted if it is a preview insert parent
			const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
      const HonorHover = (vm.TemplateData[Context][TemplateIndex].hasOwnProperty('pseudo')) ? false : true

			if(HonorHover) {
				vm.PartitionAddressHovered[Context][TemplateFaceSelected] = (HoverState) ? PartitionAddress : false
				vm.PartitionAddressHovered[Context].template_id = TemplateID
			}

    },
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

            let ParentNode = vm.GetLocationNode(ParentID)
            ParentNode.append(child)
          }
        })

        ChildrenData.forEach(child => vm.BuildLocationTree(array, child))                
      }
      
      return
    },
    GeneratePseudoData: function (Template, Context) {

      const vm = this
      let WorkingObjectData = []
      let WorkingTemplateData = []
      let PseudoObjectParentID = null
      let PseudoObjectParentFace = null
      let PseudoObjectParentPartitionAddress = null
      let PseudoObjectParentEnclosureAddress = null
      const TemplateID = Template.id
      const TemplateType = Template.type

      if (TemplateType == 'insert') {
        
        PseudoObjectParentFace = 'front'
        PseudoObjectParentPartitionAddress = [0, 0]
        PseudoObjectParentEnclosureAddress = [0, 0]
        const InsertConstraints = Template.insert_constraints

        // Generate pseudo IDs
        const PseudoTemplateID = "pseudo-" + (vm.TemplateData[Context].length + WorkingTemplateData.length)
        const PseudoObjectID = "pseudo-" + (vm.ObjectData[Context].length + WorkingObjectData.length)

        // Create pseudo object
        WorkingObjectData.push(JSON.parse(JSON.stringify(vm.GenericObject), function (GenericObjectKey, GenericObjectValue) {
          if (GenericObjectKey == 'id') {
            return PseudoObjectID
          } else if (GenericObjectKey == 'cabinet_face') {
            return 'front'
          } else if (GenericObjectKey == 'location_id') {
            return (Context == 'preview') ? vm.InsertTemplateID : null
          } else if (GenericObjectKey == 'cabinet_ru') {
            return 1
          } else if (GenericObjectKey == 'template_id') {
            return PseudoTemplateID
          } else if (GenericObjectKey == 'parent_id') {
            return PseudoObjectParentID
          } else {
            return GenericObjectValue
          }
        }))

        // Create pseudo template
        WorkingTemplateData.push(JSON.parse(JSON.stringify(vm.GenericTemplate), function (GenericTemplateKey, GenericTemplateValue) {
          if (GenericTemplateKey == 'id') {
            return PseudoTemplateID
          } else if (GenericTemplateKey == 'name') {
            return Template.name
          } else if (GenericTemplateKey == 'category_id') {
            return Template.category_id
          } else if (GenericTemplateKey == 'type') {
            // Set pseudo template type, but avoid setting partition type
            if (GenericTemplateValue === null) {
                return 'standard'
            } else {
                return GenericTemplateValue
            }
          } else if (GenericTemplateKey == 'ru_size') {
            // Set pseudo template RU size if this is the insert constraint origin ('standard' template type)
            return Math.ceil(InsertConstraints.part_layout.height / 2)
          } else if (GenericTemplateKey == 'blueprint') {
            // Set pseudo template partition attributes
            GenericTemplateValue.front[0].units = InsertConstraints.part_layout.width
            GenericTemplateValue.front[0].children[0].units = InsertConstraints.part_layout.height
            GenericTemplateValue.front[0].children[0].enc_layout.cols = InsertConstraints.enc_layout.cols
            GenericTemplateValue.front[0].children[0].enc_layout.rows = InsertConstraints.enc_layout.rows
            return GenericTemplateValue

          } else {

              return GenericTemplateValue
          }
        }))

        PseudoObjectParentID = PseudoObjectID
      }

      // Create pseudo object for template
      const PseudoObjectID = "pseudo-" + (vm.ObjectData[Context].length + WorkingObjectData.length)
      WorkingObjectData.push(JSON.parse(JSON.stringify(vm.GenericObject), function (GenericObjectKey, GenericObjectValue) {
        if (GenericObjectKey == 'id') {
            return PseudoObjectID
        } else if (GenericObjectKey == 'location_id') {
            return (Context == 'preview' && TemplateType == 'standard') ? vm.InsertTemplateID : GenericObjectValue
        } else if (GenericObjectKey == 'cabinet_face') {
            return (Context == 'preview' && TemplateType == 'standard') ? 'front' : GenericObjectValue
        } else if (GenericObjectKey == 'cabinet_ru') {
            return (Context == 'preview' && TemplateType == 'standard') ? 1 : GenericObjectValue
        } else if (GenericObjectKey == 'parent_id') {
            return PseudoObjectParentID
        } else if (GenericObjectKey == 'parent_face') {
            return PseudoObjectParentFace
        } else if (GenericObjectKey == 'parent_part_addr') {
            return PseudoObjectParentPartitionAddress
        } else if (GenericObjectKey == 'parent_enc_addr') {
            return PseudoObjectParentEnclosureAddress
        } else if (GenericObjectKey == 'template_id') {
            return TemplateID
        } else {
            return GenericObjectValue
        }
      }))

      vm.TemplateData[Context] = vm.TemplateData[Context].concat(WorkingTemplateData)
      vm.ObjectData[Context] = vm.ObjectData[Context].concat(WorkingObjectData)

      return PseudoObjectID
    },
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
      let Node = vm.$refs.LiquorTree.find(Criteria)[0]

      return Node
    },
    LocationsGET: function () {

      const vm = this
      
      this.$http.get('/api/locations').then(function(response){

        vm.BuildLocationTree(response.data)
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
    },
    categoryGET: function() {

      const vm = this

      vm.$http.get('/api/categories').then(function(response){

        vm.CategoryData = response.data

      });
    },
    templatesGET: function () {

      const vm = this
			const Context = 'template'
      
      vm.$http.get('/api/templates').then(function(response){

        vm.TemplateData.template = response.data

        response.data.forEach(function(Template){
					
					vm.GeneratePseudoData(Template, Context)
        })
      });
    },
  },
  mounted() {

    const vm = this

    vm.LocationsGET()
    vm.categoryGET()
    vm.templatesGET()

    // Update selected node
    vm.$refs.LiquorTree.$on('node:selected', (node) => {

      const NodeID = node.id
      vm.NodeIDSelected = NodeID
      document.cookie = "environment_location_nodeID="+NodeID

      // Store data
      const url = '/api/locations/'+NodeID

      // PATCH category form data
      vm.$http.get(url).then(function(response){
        vm.CabinetData = response.data
      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    })
    
    // Update node text
    vm.$refs.LiquorTree.$on('node:editing:stop', (node) => {

      // Store data
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

    // Update node parent
    vm.$refs.LiquorTree.$on('node:dragging:finish', (node) => {

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

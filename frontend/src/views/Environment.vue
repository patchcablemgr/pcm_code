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
                @ObjectDropped="ObjectDropped($event)"
              />
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>

          <component-template-Object-details
            CardTitle="Object Details"
						:TemplateData="TemplateData"
						:CategoryData="CategoryData"
            :ObjectData="ObjectData"
            :PortConnectorData="PortConnectorData"
            :MediaData="MediaData"
						Context="preview"
						:TemplateFaceSelected="TemplateFaceSelected"
						:PartitionAddressSelected="PartitionAddressSelected"
						:TemplatePartitionPortRange="TemplatePartitionPortRange"
            :PortOrientationData="PortOrientationData"
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

    <!-- Template Edit Modal -->
    <modal-templates-edit
      :TemplateData="TemplateData"
      :CategoryData="CategoryData"
      :ObjectData="ObjectData"
      Context="preview"
      :TemplateFaceSelected="TemplateFaceSelected"
      :PartitionAddressSelected="PartitionAddressSelected"
      :TemplatePartitionPortRange="TemplatePartitionPortRange"
      PreviewPortID="test"
      @ObjectEdited="ObjectEdited($event)"
      @TemplateEdited="TemplateEdited($event)"
      @TemplatePartitionPortFormatValueUpdated="TemplatePartitionPortFormatValueUpdated($event)"
      @TemplatePartitionPortFormatTypeUpdated="TemplatePartitionPortFormatTypeUpdated($event)"
      @TemplatePartitionPortFormatCountUpdated="TemplatePartitionPortFormatCountUpdated($event)"
      @TemplatePartitionPortFormatOrderUpdated="TemplatePartitionPortFormatOrderUpdated($event)"
      @TemplatePartitionPortFormatFieldMove="TemplatePartitionPortFormatFieldMove($event)"
      @TemplatePartitionPortFormatFieldCreate="TemplatePartitionPortFormatFieldCreate($event)"
      @TemplatePartitionPortFormatFieldDelete="TemplatePartitionPortFormatFieldDelete($event)"
    />

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
import ModalTemplatesEdit from './templates/ModalTemplatesEdit.vue'
import LiquorTree from 'liquor-tree'
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css'
import VueSimpleContextMenu from 'vue-simple-context-menu'
import { PCM } from '../mixins/PCM.js'

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

const PortConnectorData = [
  {
    "value": 1,
    "name": "RJ45",
    "category_type_id": 1,
    "default": 1,
  },
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
    'object_id': null,
    'template_id': null,
    'front': [0],
    'rear': [0]
  },
  'template': {
    'object_id': null,
    'template_id': null,
    'front': [0],
    'rear': [0]
  }
}

const PartitionAddressHovered = {
  'preview': {
    'object_id': null,
    'template_id': null,
    'front': false,
    'rear': false
  },
  'template': {
    'object_id': null,
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
    "cabinet_id": null,
    "cabinet_ru": null,
    "cabinet_front": null,
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

const PortOrientationData = [
  {
    "value": 1,
    "name": "Top-Left to Right",
    "default": 1,
  },
]

const MediaData = [
  {
    "value": 1,
    "name": "placeholder",
    "category_id": 1,
    "type_id": 1,
    "display": 1,
    "default": 1,
  }
]

const NodeIDSelected = null

export default {
  mixins: [PCM],
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
    ModalTemplatesEdit,
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
      PortOrientationData,
      MediaData,
      PortConnectorData,
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
      const Context = 'preview'
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
    GetPartition: function(Blueprint, PartitionAddress) {
			
			// Locate template partition
			let Partition = Blueprint;
			let PartitionCollection = Blueprint
			PartitionAddress.forEach(function(PartitionIndex) {
				if(typeof PartitionCollection[PartitionIndex] !== 'undefined') {
					Partition = PartitionCollection[PartitionIndex]
					PartitionCollection = PartitionCollection[PartitionIndex]['children']
				} else {
					return false
				}
			})
			
			return Partition
      
    },
    TemplateObjectEditClicked: function() {

      const vm = this
      vm.$bvModal.show('modal-templates-edit')

    },
    TemplateFaceChanged: function(EmitData) {

      // Store variables
      const vm = this
      const TemplateFace = EmitData.TemplateFace
      const Context = EmitData.Context
      vm.TemplateFaceSelected[Context] = TemplateFace

    },
    ObjectEdited: function(EmitData) {

      // Store data
      const vm = this
      const Context = 'preview'
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
      const url = '/api/objects/'+ObjectID
      const data = EmitData

      // PATCH object
      this.$http.patch(url, data).then(function(response){
        
        const Object = response.data
        const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
				
        // Append new object to object array
        vm.$set(vm.ObjectData[Context], ObjectIndex, Object)

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
    ObjectDropped: function(EmitData) {

      const vm = this
      const Context = EmitData.context
      const DropType = EmitData.drop_type
      const TemplateID = EmitData.template_id
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
      const Template = vm.TemplateData[Context][TemplateIndex]
      const TemplateType = Template.type

      if((TemplateType == 'standard' && DropType == 'cabinet') || (TemplateType == 'insert' && DropType == 'enclosure')) {
      
        let data
        if(DropType == 'cabinet') {

          data = {
            "cabinet_id": EmitData.cabinet_id,
            "cabinet_face": EmitData.cabinet_face,
            "cabinet_ru": EmitData.cabinet_ru,
          }
        } else if(DropType == 'enclosure') {

          data = {
            "parent_id": EmitData.parent_id,
            "parent_face": EmitData.parent_face,
            "parent_partition_address": EmitData.parent_partition_address,
            "parent_enclosure_address": EmitData.parent_enclosure_address,
          }
        }

        if(Context == 'template') {

          data.template_id = EmitData.template_id
          data.template_face = EmitData.template_face

          const url = '/api/objects'

          // POST to objects
          vm.$http.post(url, data).then(function(response){

            const Object = response.data
            
            // Create child node object
            vm.ObjectData.preview.push(Object)

          }).catch(error => {

            // Display error to user via toast
            vm.$bvToast.toast(JSON.stringify(error.response), {
              title: 'Error',
              variant: 'danger',
            })

          })
        } else if(Context == 'preview') {

          data.object_id = EmitData.object_id

          const ObjectID = EmitData.object_id
          const url = '/api/objects/'+ObjectID

          // POST to objects
          vm.$http.patch(url, data).then(function(response){

            const Object = response.data
            const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
            
            // Create child node object
            vm.$set(vm.ObjectData.preview, ObjectIndex, Object)

          }).catch(error => {

            // Display error to user via toast
            vm.$bvToast.toast(JSON.stringify(error.response), {
              title: 'Error',
              variant: 'danger',
            })

          })
        }
      }
    },
    TemplateObjectDeleteClicked: function() {
      
			const vm = this
      const Context = 'preview'
			const ObjectID = vm.PartitionAddressSelected[Context].object_id
			
			const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
			const ObjectName = vm.ObjectData[Context][ObjectIndex].name
			
      vm.$bvModal
        .msgBoxConfirm('Delete '+ObjectName+'?', {
          title: 'Confirm',
          size: 'sm',
          okVariant: 'primary',
          okTitle: 'Yes',
          cancelTitle: 'No',
          cancelVariant: 'outline-secondary',
          hideHeaderClose: false,
          centered: true,
        })
        .then(value => {
					
					if(value) {
						
						// Store data
						const url = '/api/objects/'+ObjectID

						// DELETE object ID
						this.$http.delete(url).then(function(response){

							// Default selected
							vm.PartitionAddressSelected[Context].object_id = null
              vm.PartitionAddressSelected[Context].template_id = null

              // Get template object data
              const DeletedObjectID = response.data.id
              const DeletedObjectIndex = vm.GetObjectIndex(DeletedObjectID, Context)

              // Delete template and object
              vm.ObjectData[Context].splice(DeletedObjectIndex,1)

						}).catch(error => {

							// Display error to user via toast
							vm.$bvToast.toast(JSON.stringify(error.response.data), {
								title: 'Error',
								variant: 'danger',
							})

						});
					}
        })
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
          } else if (GenericObjectKey == 'cabinet_front') {
            return 'front'
          } else if (GenericObjectKey == 'cabinet_id') {
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
        } else if (GenericObjectKey == 'cabinet_id') {
            return (Context == 'preview' && TemplateType == 'standard') ? vm.InsertTemplateID : GenericObjectValue
        } else if (GenericObjectKey == 'cabinet_front') {
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
    GeneratePortID: function(Index, PortTotal, PortFormat){

      // Store variables
      const vm = this
      let PreviewPortID = ''
      let IncrementalCount = 0

      // Create character arrays
      let LowercaseArray = []
      let UppercaseArray = []
      for(let x=97; x<=122; x++) {
        LowercaseArray.push(String.fromCharCode(x))
      }
      for(let x=65; x<=90; x++) {
        UppercaseArray.push(String.fromCharCode(x))
      }

      // Account for infinite count incrementables
      PortFormat.forEach(function(Field, FieldIndex){
        const FieldType = Field.type
        let FieldCount = Field.count

        // Increment IncrementalCount
        if(FieldType == 'incremental' || FieldType == 'series') {
          IncrementalCount++

          // Adjust field count
          if(FieldType == 'incremental' && FieldCount == 0) {
            PortFormat[FieldIndex].count = PortTotal
          } else if(FieldType == 'series') {
            let CurrentFieldValue = Field.value
            PortFormat[FieldIndex].count = CurrentFieldValue.split(',').length
          }
        }
      })

      PortFormat.forEach(function(Field){
        const FieldType = Field.type
        const FieldValue = Field.value
        const FieldOrder = Field.order
        const FieldCount = Field.count
        let HowMuchToIncrement
        let RollOver
        let Numerator = 1

        if(FieldType == 'static') {
          PreviewPortID = PreviewPortID + FieldValue
        } else {

          PortFormat.forEach(function(LoopField){
            const LoopFieldType = LoopField.type
            const LoopFieldOrder = LoopField.order
            const LoopFieldCount = LoopField.count

            if(LoopFieldType == 'incremental' || LoopFieldType == 'series') {
              if(LoopFieldOrder < FieldOrder) {
                Numerator *= LoopFieldCount
              }
            }
          })

          HowMuchToIncrement = Math.floor(Index / Numerator)

          if(HowMuchToIncrement >= FieldCount) {
            RollOver = Math.floor(HowMuchToIncrement / FieldCount)
            HowMuchToIncrement = HowMuchToIncrement - (RollOver * FieldCount)
            
          }

          if(FieldType == 'incremental') {

            if(!isNaN(FieldValue)) {
              PreviewPortID = PreviewPortID + (parseInt(FieldValue) + HowMuchToIncrement)
            } else {
              PreviewPortID = PreviewPortID + '-'
            }

          } else if(FieldType == 'series') {

            const FieldValueArray = FieldValue.split(',')
            PreviewPortID = PreviewPortID + FieldValueArray[HowMuchToIncrement]

          }
        }
      })
      
      return PreviewPortID
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
    GETLocations: function () {

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
    GETCategories: function() {

      const vm = this

      vm.$http.get('/api/categories').then(function(response){

        vm.CategoryData = response.data

      });
    },
    GETMedia: function() {

      const vm = this;

      vm.$http.get('/api/medium').then(function(response){
        vm.MediaData = response.data;
      });
    },
    GETTemplates: function () {

      const vm = this
			const Context = 'template'
      
      vm.$http.get('/api/templates').then(function(response){

        vm.TemplateData[Context] = response.data
        vm.TemplateData.preview = response.data

        response.data.forEach(function(Template){
					
					vm.GeneratePseudoData(Template, Context)
        })
      })
    },
    GETObjects: function () {

      const vm = this
			const Context = 'preview'
      
      vm.$http.get('/api/objects').then(function(response){

        vm.ObjectData[Context] = response.data
      })
    },
    GETPortOrientations: function() {

      const vm = this;

      this.$http.get('/api/port-orientation').then(function(response){
        vm.PortOrientationData = response.data
      });
    },
    GETPortConnectors: function() {

      const vm = this;

      vm.$http.get('/api/port-connectors').then(function(response){
        vm.PortConnectorData = response.data;
      });
    },
  },
  mounted() {

    const vm = this

    vm.GETLocations()
    vm.GETCategories()
    vm.GETTemplates()
    vm.GETMedia()
    vm.GETObjects()
    vm.GETPortOrientations()
    vm.GETPortConnectors()

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

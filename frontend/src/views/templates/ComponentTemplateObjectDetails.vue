<!-- Template/Object Details -->

<template>
  <div>
	
  <b-card>
    <b-card-title class="mb-0">
      <div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing">
          {{CardTitle}}
        </div>
        <div class="demo-inline-spacing"
          v-if="DetailsAreEditable"
        >
          <b-dropdown
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            right
            size="sm"
            text="Actions"
            variant="primary"
          >

            <b-dropdown-item
              v-if="Context == 'template'"
              @click="CloneTemplate()"
              :disabled="!ComputedObjectSelected"
            >Clone
            </b-dropdown-item>

            <b-dropdown-item
              v-if="Context == 'template'"
              :disabled="!ComputedObjectSelected"
            >Where Used</b-dropdown-item>

            <b-dropdown-item
              v-if="Context == 'catalog'"
              @click=" ImportTemplate() "
              :disabled="!ComputedObjectSelected"
            >Import</b-dropdown-item>

            <b-dropdown-divider />

            <b-dropdown-item
              v-if="Context == 'template' || Context == 'actual'"
              variant="danger"
              @click=" DeleteTemplate() "
              :disabled="!ComputedObjectSelected"
            >Delete</b-dropdown-item>

            <b-dropdown-item
              v-if="Context == 'template' || Context == 'actual'"
              variant="danger"
              @click=" Clear() "
              :disabled="!ComputedObjectSelected"
            >Clear Trunk</b-dropdown-item>

          </b-dropdown>
        </div>
      </div>
    </b-card-title>
		<b-card-body>
		
    <div
      class="h5 font-weight-bolder m-0"
    >
			General:
    </div>
    <hr
      class="separator mt-0"
    >

    <table>
      <tr>
        <td class="text-right">
          <strong>Object Name:</strong>
        </td>
        <td>
          <b-button
            v-if="Context == 'actual' && DetailsAreEditable"
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-object-name
            :disabled="!ComputedObjectSelected"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{ComputedObjectName}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Template Name:</strong>
        </td>
        <td>
          <b-button
            v-if="Context == 'template'  && DetailsAreEditable"
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-template-name
            :disabled="!ComputedObjectSelected"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{TemplateName}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Category:</strong>
        </td>
        <td>
          <b-button
            v-if="Context == 'template'  && DetailsAreEditable"
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-template-category
            :disabled="!ComputedObjectSelected"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{ComputedCategoryName}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Type:</strong>
        </td>
        <td>
        </td>
        <td>
          {{TemplateType}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Function:</strong>
        </td>
        <td>
        </td>
        <td>
          {{TemplateFunction}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>RU Size:</strong>
        </td>
        <td>
        </td>
        <td>
          {{ComputedRUSize}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Mount Config:</strong>
        </td>
        <td>
        </td>
        <td>
          {{ComputedMountConfig}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Image:</strong>
        </td>
        <td>
          <b-button
            v-if="Context == 'template'  && DetailsAreEditable"
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-file-upload
            :disabled="!ComputedObjectSelected"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td
          v-if="ComputedObjectSelected"
        >
          <img
            v-if="TemplateImage"
            :src="TemplateImage"
            :style="{ width: '100%' }"
          />
          <div
            v-else
          >
            N/A
          </div>
        </td>
        <td
          v-else
        >
          -
        </td>
      </tr>
    </table>

    <div
      class="h5 font-weight-bolder m-0"
    >
      Partition:
    </div>
    <hr
      class="separator mt-0"
    >

    <table>
      <tr>
        <td class="text-right">
          <strong>Partition Type:</strong>
        </td>
        <td>
        </td>
        <td>
          {{ComputedPartitionType}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Trunked To:</strong>
        </td>
        <td>
          <b-button
            v-if="TrunkedToEditable"
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-trunk-select
            :disabled="!ComputedObjectSelected"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{ComputedTrunkedTo}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Port Range:</strong>
        </td>
        <td>
          <b-button
            v-if="PortRangeEditable"
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-template-port-id-details
            :disabled="!ComputedObjectSelected"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{PortRange}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Port Orientation:</strong>
        </td>
        <td>
        </td>
        <td>
          {{ComputedPortOrientation}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Port Type:</strong>
        </td>
        <td>
        </td>
        <td>
          {{ComputedPortType}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Media Type:</strong>
        </td>
        <td>
        </td>
        <td>
          {{ComputedPortMediaType}}
        </td>
      </tr>

    </table>

  </b-card-body>
  
  </b-card>

    <!-- Modal Edit Object Name -->
    <modal-edit-object-name
      ModalID="modal-edit-object-name"
      ModalTitle="Object Name"
      :Context="Context"
    />

    <!-- Modal Edit Template Name -->
    <modal-edit-template-name
      :Context="Context"
    />

    <!-- Modal Edit Template Category -->
    <modal-edit-template-category
      :Context="Context"
      ModalTitle="Category"
    />

    <!-- Modal Edit Template Port ID -->
    <modal-edit-template-port-id
      ModalID="modal-edit-template-port-id-details"
      :Context="Context"
      :TemplateFaceSelected="TemplateFaceSelected"
      PreviewPortID="temp"
    />

    <!-- File Upload Modal -->
    <modal-file-upload
      Title="Template Image"
      UploadType="templateImg"
      :Context="Context"
      :TemplateFaceSelected="TemplateFaceSelected"
    />

    <!-- Modal Trunk Select -->
    <modal-trunk-select
      v-if="Context != 'template' && Context != 'catalog'"
      ModalID="modal-trunk-select"
      ModalTitle="Select Trunk Peer"
      TreeRef="TreeSelectTrunk"
      :Context="Context"
      PortSelectFunction="trunk"
    />

  </div>
</template>

<script>
import {
  BCard,
  BCardTitle,
  BCardBody,
  BDropdown,
  BDropdownItem,
  BDropdownDivider,
  BButton,
} from 'bootstrap-vue'
import ModalEditObjectName from './ModalEditObjectName.vue'
import ModalEditTemplateName from './ModalEditTemplateName.vue'
import ModalEditTemplateCategory from './ModalEditTemplateCategory.vue'
import ModalEditTemplatePortId from './ModalEditTemplatePortId.vue'
import ModalTrunkSelect from '@/views/templates/ModalTrunkSelect.vue'
import ModalFileUpload from './ModalFileUpload.vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
		BCard,
		BCardTitle,
		BCardBody,
		BDropdown,
		BDropdownItem,
		BDropdownDivider,
    BButton,

    ModalEditObjectName,
    ModalEditTemplateName,
    ModalEditTemplateCategory,
    ModalEditTemplatePortId,
    ModalTrunkSelect,
    ModalFileUpload,
  },
	directives: {
		Ripple,
	},
  props: {
    CardTitle: {type: String},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    DetailsAreEditable: {type: Boolean},
  },
  data() {
    return {
    }
  },
  computed: {
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    Media() {
      return this.$store.state.pcmProps.Media
    },
    Connectors() {
      return this.$store.state.pcmProps.Connectors
    },
    Orientations() {
      return this.$store.state.pcmProps.Orientations
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Trunks() {
      return this.$store.state.pcmTrunks.Trunks
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    ComputedObjectSelected: function() {

      const vm = this
      const Context = vm.Context
      return (vm.StateSelected[Context].object_id) ? true : false
    },
    PortRangeEditable: function(){

      const vm = this
      const Context = vm.Context
      const DetailsAreEditable = vm.DetailsAreEditable
      const Partition = vm.GetPartitionSelected(Context)
      const PartitionType = (Partition) ? Partition.type : null

      return Context == 'template' && DetailsAreEditable && PartitionType == 'connectable'

    },
    TrunkedToEditable: function(){

      const vm = this
      const Context = vm.Context
      const DetailsAreEditable = vm.DetailsAreEditable

      return Context == 'actual' && DetailsAreEditable

    },
    ComputedObjectName: {
      get() {

        const vm = this
        const Context = vm.Context
        const Object = vm.GetObjectSelected(Context)
        let ReturnString = '-'

        if(Object) {
          return (Context == 'actual') ? Object.name : 'N/A'
        }

        return ReturnString
      },
    },
    TemplateName: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.StateSelected[Context].object_id
        const Template = vm.GetTemplate({ObjectID, Context})

        return (Template) ? Template.name : '-'
      },
    },
    ComputedCategoryName: {
      get() {

        const vm = this
        const Context = vm.Context
        let ReturnData = '-'
        const ObjectID = vm.StateSelected[Context].object_id
        const Category = vm.GetCategory({ObjectID, Context})
        
        ReturnData = (Category) ? Category.name : ReturnData

        return ReturnData
      },
    },
    ComputedTrunkedTo: {
      get() {

        const vm = this
        const Context = vm.Context
        const Partition = vm.GetPartitionSelected(Context)
        let TrunkedTo = '-'

        if(Partition) {

          if(Partition.type == 'connectable' && Context == 'actual') {

            const ObjectID = vm.StateSelected[Context].object_id
            const ObjectFace = vm.StateSelected[Context].object_face
            const ObjectPartition = vm.StateSelected[Context].partition[ObjectFace]

            const Trunks = vm.GetTrunks(ObjectID, ObjectFace, ObjectPartition)
            const TrunksWithPortSet = Trunks.findIndex((trunk) => trunk.b_port !== null)

            if(TrunksWithPortSet != -1) {
              TrunkedTo = "[Floorplan Object]"
            } else if(Trunks.length == 1) {
              Trunks.forEach(function(Trunk){

                const LocalTrunkSide = (Trunk.a_id == ObjectID) ? 'a' : 'b'
                let RemoteObjectID
                let RemoteObjectFace
                let RemoteObjectPartition
                if(LocalTrunkSide == 'a') {
                  RemoteObjectID = Trunk.b_id
                  RemoteObjectFace = Trunk.b_face
                  RemoteObjectPartition = Trunk.b_partition
                } else {
                  RemoteObjectID = Trunk.a_id
                  RemoteObjectFace = Trunk.a_face
                  RemoteObjectPartition = Trunk.a_partition
                }

                const Scope = 'trunk'
                TrunkedTo = vm.GenerateDN(Scope, RemoteObjectID, RemoteObjectFace, RemoteObjectPartition)

              })
            } else {
              TrunkedTo = "[None]"
            }

          } else {
            TrunkedTo = "N/A"
          }
        }

        return TrunkedTo
      },
    },
    TemplateType: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.StateSelected[Context].object_id
        const Template = vm.GetTemplate({ObjectID, Context})

        const TemplateType = (Template) ? Template.type : '-'
        return TemplateType.charAt(0).toUpperCase() + TemplateType.slice(1)
      },
    },
    TemplateFunction: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.StateSelected[Context].object_id
        const Template = vm.GetTemplate({ObjectID, Context})

        const TemplateFunction = (Template) ? Template.function : '-'
        return TemplateFunction.charAt(0).toUpperCase() + TemplateFunction.slice(1)
      },
    },
    ComputedRUSize: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.StateSelected[Context].object_id
        const Template = vm.GetTemplate({ObjectID, Context})

        if(Template) {
          const TemplateType = Template.type
          if(TemplateType == 'insert') {
            return 'N/A'
          } else {
            return Template.ru_size
          }
        } else {
          return '-'
        }
      },
    },
    ComputedMountConfig: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.StateSelected[Context].object_id
        const Template = vm.GetTemplate({ObjectID, Context})
        
        if(Template) {
          const TemplateType = Template.type
          if(TemplateType == 'insert') {
            return 'N/A'
          } else {
            return Template.mount_config
          }
        } else {
          return '-'
        }
      },
    },
    TemplateImage: {
      get() {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const ObjectID = vm.StateSelected[Context].object_id
        const Template = vm.GetTemplate({ObjectID, Context})
        const imgAttr = (Face == 'front') ? 'img_front' : 'img_rear'

        return (Template) ? Template[imgAttr] : false
      },
      set() {
        return true
      }
    },
    ComputedPartitionType: {
      get() {

        const vm = this
        const Context = vm.Context
        const Partition = vm.GetPartitionSelected(Context)
        let PartitionType = '-'

        if(Partition) {
          PartitionType = Partition.type
          PartitionType = PartitionType.charAt(0).toUpperCase() + PartitionType.slice(1)
        }

        return PartitionType
      },
    },
    PortRange: {
      get() {
      
        // Initialize some variables
        const vm = this
        const Partition = vm.GetPartitionSelected()
        let PortRange = '-'

        if(Partition) {

          if(Partition.type == 'connectable') {

            // Calculate port numbers
            const PortFormat = Partition.port_format
            const PortLayoutCols = Partition.port_layout.cols
            const PortLayoutRows = Partition.port_layout.rows
            const PortTotal = PortLayoutCols * PortLayoutRows
            const FirstPortID = 0
            const LastPortID = PortTotal - 1

            const FirstPortIDString = vm.GeneratePortID(FirstPortID, PortTotal, PortFormat)
            const LastPortIDString = vm.GeneratePortID(LastPortID, PortTotal, PortFormat)

            PortRange = FirstPortIDString+' - '+LastPortIDString
          } else {

            PortRange = 'N/A'
          }
        }

        return PortRange
      }
    },
    ComputedPortOrientation: {
      get() {

        const vm = this
        const Context = vm.Context
        const Partition = vm.GetPartitionSelected(Context)
        let PortOrientation = '-'

        if(Partition) {

          if(Partition.type == 'connectable') {
            const PortOrientationID = Partition.port_orientation
            const PortOrientationIndex = vm.GetPortOrientationIndex(PortOrientationID)
            const PortOrientationName = vm.Orientations[PortOrientationIndex].name
            PortOrientation = PortOrientationName
          } else {

            PortOrientation = 'N/A'
          }
        }

        return PortOrientation
      },
    },
    ComputedPortType: {
      get() {

        const vm = this
        const Context = vm.Context
        const Partition = vm.GetPartitionSelected(Context)
        let PortType = '-'

        if(Partition) {

          if(Partition.type == 'connectable') {
            const PortConnectorID = Partition.port_connector
            const PortConnectorIndex = vm.GetPortConnectorIndex(PortConnectorID)
            PortType = vm.Connectors[PortConnectorIndex].name
          } else {

            PortType = 'N/A'
          }
        }

        return PortType
      },
    },
    ComputedPortMediaType: {
      get() {

        const vm = this
        const Context = vm.Context
        const Partition = vm.GetPartitionSelected(Context)
        let MediaType = '-'

        if(Partition) {

          // Get template
          const ObjectID = vm.StateSelected[Context].object_id
          const Template = vm.GetTemplate({ObjectID, Context})
          const TemplateType = Template.type

          if(TemplateType == 'passive') {

            if(Partition.type == 'connectable') {
              const MediaID = Partition.media
              const MediaIndex = vm.GetMediaIndex(MediaID)
              MediaType = vm.Media[MediaIndex].name
            }
          } else {

            MediaType = 'N/A'
          }
        }

        return MediaType
      },
    },
  },
  methods: {
    GetPortOrientationIndex: function(PortOrientationID) {

      const vm = this
      const PortOrientationIndex = vm.Orientations.findIndex((PortOrientation) => PortOrientation.value == PortOrientationID);
      
      return PortOrientationIndex
    },
    GetPortConnectorIndex: function(PortConnectorID) {

      const vm = this
      const PortConnectorIndex = vm.Connectors.findIndex((PortConnector) => PortConnector.value == PortConnectorID);
      
      return PortConnectorIndex
    },
    GetMediaIndex: function(MediaID) {

      const vm = this
      const MediaIndex = vm.Media.findIndex((Media) => Media.value == MediaID);
      
      return MediaIndex
    },
    GetCategoryIndex: function(CategoryID) {

      const vm = this
      const Context = vm.Context
      const CategoryIndex = vm.Categories[Context].findIndex((category) => category.id == CategoryID)

      return parseInt(CategoryIndex)
    },
    ImportTemplate: function() {

      const vm = this
      const Context = vm.Context

      const ObjectID = vm.StateSelected[Context].object_id
      const Template = vm.GetTemplate({ObjectID, Context})
      const TemplateID = Template.id

      vm.$http.get('/api/catalog/template/'+TemplateID).then(response => {

        const Category = response.data.category
        if(Category) {
          vm.$store.commit('pcmCategories/ADD_Category', {pcmContext:'template', data:Category})
        }

        const Template = response.data.template
        vm.GeneratePseudoData('template', Template)
				
        // Append new template to template array
        vm.$store.commit('pcmTemplates/ADD_Template', {pcmContext:'template', data:Template})
      })
    },
    DeleteTemplate: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.StateSelected[Context].object_id

      const Object = vm.GetObject({ObjectID, Context})
      const ObjectName = (Context == 'actual') ? Object.name : 'N/A'

      const Template = vm.GetTemplate({ObjectID, Context})
      const TemplateName = Template.name

      const Category = vm.GetCategory({ObjectID, Context})
      const CategoryName = Category.name

      // Confirm Deletion
      const DeleteObjectMsg = "Delete "+ObjectName+" ("+TemplateName+")?"
      const DeleteTemplateMsg = "Delete "+TemplateName+" ("+CategoryName+")?"
      const ConfirmMsg = (Context == 'actual') ? DeleteObjectMsg : DeleteTemplateMsg
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {

        if (result === true) {
          // Delete Template
          if(Context == 'template') {

            const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
            const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
            const TemplateID = Template.id
            const URL = '/api/templates/'+TemplateID

            vm.$http.delete(URL).then(response => {
              
              // Collect parentID before deleting object... we will need it if object is an insert
              const ParentID = Object.parent_id

              // Delete pseudo object and template
              vm.$store.commit('pcmObjects/REMOVE_Object', {pcmContext:Context, data:Object})
              vm.$store.commit('pcmTemplates/REMOVE_Template', {pcmContext:Context, data:response.data})

              if(Template.type == 'insert') {
                const ParentIndex = vm.GetObjectIndex(ParentID, Context)
                const Parent = vm.Objects[Context][ParentIndex]
                const ParentTemplateID = Parent.template_id
                const ParentTemplateIndex = vm.GetTemplateIndex(ParentTemplateID, Context)
                const ParentTemplate = vm.Templates[Context][ParentTemplateIndex]

                // Delete pseudo object and template
                vm.$store.commit('pcmObjects/REMOVE_Object', {pcmContext:Context, data:Parent})
                vm.$store.commit('pcmTemplates/REMOVE_Template', {pcmContext:Context, data:ParentTemplate})
              }
            }).catch(error => {
              vm.DisplayError(error)
            })

          // Delete Object
          } else {

            const URL = '/api/objects/'+ObjectID

            vm.$http.delete(URL).then(response => {

              response.data.trunk.forEach(entryID => vm.$store.commit('pcmTrunks/REMOVE_Trunk', {data:{id:entryID}}))
              response.data.connection.forEach(entryID => vm.$store.commit('pcmConnections/REMOVE_Connection', {data:{id:entryID}}))
              response.data.object.forEach(entryID => vm.$store.commit('pcmObjects/REMOVE_Object', {pcmContext:Context, data:{id:entryID}}))

            }).catch(error => {
              vm.DisplayError(error)
            })
          }

          // Clear user selection
          vm.$store.commit('pcmState/DEFAULT_Selected_Object', {pcmContext:Context})
        }
      })
    },
    CloneTemplate: function() {

      const vm = this
      const TemplateContext = 'template'
      const PreviewContext = 'workspace'
      const StandardTemplateID = this.$store.state.pcmProps.WorkspaceStandardID
      const InsertTemplateID = this.$store.state.pcmProps.WorkspaceInsertID
      let PartitionSelectedData = {
        Context: PreviewContext,
        object_id: StandardTemplateID,
        front: [0],
        rear: [0]
      }
      let FaceSelectedData = {
        Context: PreviewContext,
        Face: 'front'
      }

      // Get cloned template
      const ObjectID = vm.StateSelected[TemplateContext].object_id
      const Template = vm.GetTemplate({ObjectID, Context:TemplateContext})

      // Set active preview template
      const PreviewTemplateID = (Template.type == 'standard') ? StandardTemplateID : InsertTemplateID

      // Create a copy of cloned template
      const TemplateClone = JSON.parse(JSON.stringify(Template), function(TemplateKey, TemplateValue){
        if (TemplateKey == 'id') {
          return PreviewTemplateID
        } else {
          return TemplateValue
        }
      })
      TemplateClone.clone = true

      // Copy cloned template to active preview template
      vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:PreviewContext, data:TemplateClone})

      if(Template.type == 'insert') {

        // Remove preview pseudo objects/templates
        vm.RemovePreviewPseudoData()

        // Create pseudo template clone parent
        const PseudoTemplateID = "pseudo-" + (vm.Templates[PreviewContext].length)
        const InsertConstraints = Template.insert_constraints
        const GenericTemplate = vm.$store.state.pcmProps.GenericTemplate
        const TemplateCloneParent = JSON.parse(JSON.stringify(GenericTemplate), function (Key, Value) {
          if (Key == 'id') {
            return PseudoTemplateID
          } else if (Key == 'name') {
            return Template.name
          } else if (Key == 'category_id') {
            return Template.category_id
          } else if (Key == 'type') {
            // Set pseudo template type, but avoid setting partition type
            if (Value === null) {
              return 'standard'
            } else {
              return Value
            }
          } else if (Key == 'ru_size') {
            // Set pseudo template RU size if this is the insert constraint origin ('standard' template type)
            return Math.ceil(InsertConstraints[Template.insert_constraints.length-1].part_layout.height / 2)
          } else if (Key == 'blueprint') {

            Value.front[0].units = InsertConstraints[Template.insert_constraints.length-1].part_layout.width

            // Generate enclosure partition
            let EnclosurePartition = {
              'type': 'enclosure',
              'units': InsertConstraints[Template.insert_constraints.length-1].part_layout.height,
              'enc_layout': {
                'cols': InsertConstraints[Template.insert_constraints.length-1].enc_layout.cols,
                'rows': InsertConstraints[Template.insert_constraints.length-1].enc_layout.rows
              },
              'children': []
            }

            // Set pseudo template partition attributes
            Value.front[0].units = InsertConstraints[Template.insert_constraints.length-1].part_layout.width
            Value.front[0].children.push(EnclosurePartition)
            
            return Value

          } else {
              return Value
          }
        })
        vm.$store.commit('pcmTemplates/ADD_Template', {pcmContext:PreviewContext, data:TemplateCloneParent})

        // Generate pseudo object and constraining templates/objects if necessary
        const PseudoObjectID = vm.GeneratePseudoData(PreviewContext, TemplateCloneParent)

        // Update insert parent data
        const InsertObjectIndex = vm.GetObjectIndex(InsertTemplateID, PreviewContext)
        const InsertObject = vm.Objects[PreviewContext][InsertObjectIndex]
        const WorkspaceObject = JSON.parse(JSON.stringify(InsertObject), function (Key, Value) {
          if (Key == 'parent_id') {
            return PseudoObjectID
          } if (Key == 'parent_face') {
            return 'front'
          } if (Key == 'parent_partition_address') {
            return [0]
          } if (Key == 'parent_enclosure_address') {
            return [0,0]
          } else {
            return Value
          }
        })
        vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:PreviewContext, data:WorkspaceObject})

        // Update 
        PartitionSelectedData.object_id = InsertTemplateID
      }

      vm.$emit('SetTemplateFaceSelected', FaceSelectedData)
    },
    Clear: function() {

      const vm = this
      const Context = vm.Context
      const SelectedObjectID = vm.StateSelected[Context].object_id
      const SelectedObjectFace = vm.StateSelected[Context].object_face
      const SelectedObjectPartition = vm.StateSelected[Context].partition[SelectedObjectFace]
      const ConfirmMsg = "Clear trunk?"
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {
        if (result === true) {
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
      })
    }
  }
}
</script>
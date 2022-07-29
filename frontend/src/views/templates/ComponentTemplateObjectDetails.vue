<!-- Template/Object Details -->

<template>
  <div>
	
  <b-card>
    <b-card-title>
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
              :disabled="!TemplateSelected"
            >Clone
            </b-dropdown-item>

            <b-dropdown-item
              v-if="Context == 'template'"
              :disabled="!TemplateSelected"
            >Where Used</b-dropdown-item>

            <b-dropdown-divider />

            <b-dropdown-item
              variant="danger"
              @click=" DeleteTemplate() "
              :disabled="!TemplateSelected"
            >Delete</b-dropdown-item>

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
          Object Name:
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
          Template Name:
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
          Category:
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
          Type:
        </td>
        <td>
        </td>
        <td>
          {{TemplateType}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Function:
        </td>
        <td>
        </td>
        <td>
          {{TemplateFunction}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          RU Size:
        </td>
        <td>
        </td>
        <td>
          {{ComputedRUSize}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Mount Config:
        </td>
        <td>
        </td>
        <td>
          {{ComputedMountConfig}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Image:
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
          Partition Type:
        </td>
        <td>
        </td>
        <td>
          {{ComputedPartitionType}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Trunked To:
        </td>
        <td>
          <b-button
            v-if="DetailsAreEditable"
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.port-select-trunk
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
          Port Range:
        </td>
        <td>
          <b-button
            v-if="Context == 'template' && DetailsAreEditable"
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
          Port Orientation:
        </td>
        <td>
        </td>
        <td>
          {{ComputedPortOrientation}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Port Type:
        </td>
        <td>
        </td>
        <td>
          {{ComputedPortType}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Media Type:
        </td>
        <td>
        </td>
        <td>
          {{ComputedPortMediaType}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Enclosure Tolerance:
        </td>
        <td>
        </td>
        <td>
          {{ComputedCategoryName}}
        </td>
      </tr>
    </table>

  </b-card-body>
  
  </b-card>

    <!-- Modal Edit Object Name -->
    <modal-edit-object-name
      :Context="Context"
      :PartitionAddressSelected="PartitionAddressSelected"
      ModalTitle="Object Name"
    />

    <!-- Modal Edit Template Name -->
    <modal-edit-template-name
      :Context="Context"
      :PartitionAddressSelected="PartitionAddressSelected"
    />

    <!-- Modal Edit Template Category -->
    <modal-edit-template-category
      :Context="Context"
      ModalTitle="Category"
      :PartitionAddressSelected="PartitionAddressSelected"
    />

    <!-- Modal Edit Template Port ID -->
    <modal-edit-template-port-id
      ModalID="modal-edit-template-port-id-details"
      :Context="Context"
      :TemplateFaceSelected="TemplateFaceSelected"
      :PartitionAddressSelected="PartitionAddressSelected"
      PreviewPortID="temp"
    />

    <!-- File Upload Modal -->
    <modal-file-upload
      Title="Template Image"
      :Context="Context"
      :TemplateFaceSelected="TemplateFaceSelected"
      :PartitionAddressSelected="PartitionAddressSelected"
    />

    <!-- Modal Port Select -->
    <modal-port-select
      ModalID="port-select-trunk"
      ModalTitle="Select Trunk Peer"
      TreeRef="PortSelectTrunk"
      :Context="Context"
      :PartitionAddressSelected="PartitionAddressSelected"
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
import ModalPortSelect from '@/views/templates/ModalPortSelect.vue'
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
    ModalPortSelect,
    ModalFileUpload,
  },
	directives: {
		Ripple,
	},
  props: {
    CardTitle: {type: String},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
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
    Medium() {
      return this.$store.state.pcmProps.Medium
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
    ComputedObjectSelected: function() {

      const vm = this
      const Context = vm.Context
      return (vm.PartitionAddressSelected[Context].object_id) ? true : false
    },
    TemplateSelected: function(){

      const vm = this
      const Context = vm.Context
      return (vm.GetTemplateSelected(Context)) ? true : false
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
        const Template = vm.GetTemplateSelected(Context)

        return (Template) ? Template.name : '-'
      },
    },
    ComputedCategoryName: {
      get() {

        const vm = this
        const Context = vm.Context
        let ReturnData = '-'
        const Template = vm.GetTemplateSelected(Context)
        if(Template) {
          const CategoryID = Template.category_id
          const CategoryIndex = vm.GetCategoryIndex(CategoryID)
          ReturnData = vm.Categories[CategoryIndex].name
        }

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

          const ObjectID = vm.PartitionAddressSelected[Context].object_id
          const ObjectFace = vm.PartitionAddressSelected[Context].object_face
          const ObjectPartition = vm.PartitionAddressSelected[Context][ObjectFace]

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
          }
        }

        return TrunkedTo
      },
    },
    TemplateType: {
      get() {

        const vm = this
        const Context = vm.Context
        const Template = vm.GetTemplateSelected(Context)

        const TemplateType = (Template) ? Template.type : '-'
        return TemplateType.charAt(0).toUpperCase() + TemplateType.slice(1)
      },
    },
    TemplateFunction: {
      get() {

        const vm = this
        const Context = vm.Context
        const Template = vm.GetTemplateSelected(Context)

        const TemplateFunction = (Template) ? Template.function : '-'
        return TemplateFunction.charAt(0).toUpperCase() + TemplateFunction.slice(1)
      },
    },
    ComputedRUSize: {
      get() {

        const vm = this
        const Context = vm.Context
        const Template = vm.GetTemplateSelected(Context)
        const TemplateType = Template.type

        if(TemplateType == 'insert') {
          return 'N/A'
        } else {
          return (Template) ? Template.ru_size : '-'
        }
      },
    },
    ComputedMountConfig: {
      get() {

        const vm = this
        const Context = vm.Context
        const Template = vm.GetTemplateSelected(Context)
        const TemplateType = Template.type

        if(TemplateType == 'insert') {
          return 'N/A'
        } else {
          return (Template) ? Template.mount_config : '-'
        }
      },
    },
    TemplateImage: {
      get() {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const Template = vm.GetTemplateSelected(Context)

        return (Face == 'front') ? Template.image_front : Template.image_rear
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
          const Template = vm.GetTemplateSelected(Context)
          const TemplateType = Template.type

          if(TemplateType == 'passive') {

            if(Partition.type == 'connectable') {
              const MediaID = Partition.media
              const MediaIndex = vm.GetMediaIndex(MediaID)
              MediaType = vm.Medium[MediaIndex].name
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
      const MediaIndex = vm.Medium.findIndex((Media) => Media.value == MediaID);
      
      return MediaIndex
    },
    GetCategoryIndex: function(CategoryID) {

      const vm = this
      const CategoryIndex = vm.Categories.findIndex((category) => category.id == CategoryID)

      return parseInt(CategoryIndex)
    },
    DeleteTemplate: function() {

      const vm = this
      const Context = vm.Context

      const Object = vm.GetObjectSelected(Context)
      const ObjectID = Object.id
      const ObjectName = (Context == 'actual') ? Object.name : 'N/A'

      const Template = vm.GetTemplateSelected(Context)
      const TemplateName = Template.name

      const CategoryID = Template.category_id
      const CategoryIndex = vm.Categories.findIndex((category) => category.id == CategoryID)
      const Category = vm.Categories[CategoryIndex]
      const CategoryName = Category.name

      // Confirm Deletion
      const DeleteObjectMsg = ObjectName+" ("+TemplateName+")"
      const DeleteTemplateMsg = TemplateName+" ("+CategoryName+")"
      const ConfirmMsg = (Context == 'actual') ? DeleteObjectMsg : DeleteTemplateMsg
      const ConfirmOpts = {
        title: "Delete?"
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
              
              vm.$store.commit('pcmTemplates/REMOVE_Template', {pcmContext:Context, data:response.data})

              if(Template.type == 'insert') {
                const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
                const Object = vm.Objects[Context][ObjectIndex]
                const ParentID = Object.parent_id
                const ParentIndex = vm.GetObjectIndex(ParentID, Context)
                const Parent = vm.Objects[Context][ParentIndex]
                const ParentTemplateID = Parent.template_id
                const ParentTemplateIndex = vm.GetTemplateIndex(ParentTemplateID, Context)
                const ParentTemplate = vm.Templates[Context][ParentTemplateIndex]
                vm.$store.commit('pcmTemplates/REMOVE_Template', {pcmContext:Context, data:ParentTemplate})
              }
            }).catch(error => {
              vm.DisplayError(error)
            })

          // Delete Object
          } else {

            const URL = '/api/objects/'+ObjectID

            vm.$http.delete(URL).then(response => {

              // Remove deleted object
              vm.$store.commit('pcmObjects/REMOVE_Object', {pcmContext:Context, data:response.data})

              // Remove trunks associated with deleted object
              const DeleteTrunks = vm.Trunks.filter(trunk => trunk.a_id == response.data.id || trunk.b_id == response.data.id)
              DeleteTrunks.forEach(trunk => vm.$store.commit('pcmTrunks/REMOVE_Trunk', {data:trunk}))

              // Remove connections associated with deleted object
              const DeleteConnections = vm.Connections.filter(connection => connection.a_id == response.data.id || connection.b_id == response.data.id)
              DeleteConnections.forEach(connection => vm.$store.commit('pcmConnections/REMOVE_Connection', {data:connection}))

            }).catch(error => {
              vm.DisplayError(error)
            })
          }

          // Clear user selection
          const PartitionAddressSelected = {
            Context,
            object_id: null,
            template_id: null,
            front: [0],
            rear: [0]
          }
          vm.$emit('SetPartitionAddressSelected', PartitionAddressSelected)
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
      const Template = vm.GetTemplateSelected(TemplateContext)

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
            return Math.ceil(InsertConstraints.part_layout.height / 2)
          } else if (Key == 'blueprint') {

            Value.front[0].units = InsertConstraints.part_layout.width

            // Generate enclosure partition
            let EnclosurePartition = {
              'type': 'enclosure',
              'units': InsertConstraints.part_layout.height,
              'enc_layout': {
                'cols': InsertConstraints.enc_layout.cols,
                'rows': InsertConstraints.enc_layout.rows
              },
              'children': []
            }

            // Set pseudo template partition attributes
            Value.front[0].units = InsertConstraints.part_layout.width
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
      vm.$emit('SetPartitionAddressSelected', PartitionSelectedData)
    },
  }
}
</script>
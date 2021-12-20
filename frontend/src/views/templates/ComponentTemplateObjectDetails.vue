<!-- Template/Object Details -->

<template>
  <div>
	
  <b-card>
    <b-card-title>
      <div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing">
          {{CardTitle}}
        </div>
        <div class="demo-inline-spacing">
          <b-dropdown
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            right
            size="sm"
            text="Actions"
            variant="primary"
          >

            <b-dropdown-item
              v-if="Context == 'template'"
              @click=" $emit('TemplateObjectCloneClicked') "
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
            v-if="Context == 'preview'"
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
            v-if="Context == 'template'"
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
            v-if="Context == 'template'"
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
        </td>
        <td>
          {{ComputedCategoryName}}
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
            v-if="Context == 'template'"
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
      ModalTitle="Object Name"
      :NameValue="ComputedObjectName"
      @ObjectNameEdited=" $emit('ObjectEdited', $event) "
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
      @TemplateCategoryEdited=" $emit('TemplateEdited', $event) "
    />

    <!-- Modal Edit Template Port ID -->
    <modal-edit-template-port-id
      ModalID="modal-edit-template-port-id-details"
      :Context="Context"
      :TemplateFaceSelected="TemplateFaceSelected"
      :PartitionAddressSelected="PartitionAddressSelected"
      PreviewPortID="temp"
      v-on:TemplatePartitionPortFormatFieldSelected="$emit('TemplatePartitionPortFormatFieldSelected', $event)"
      v-on:TemplatePartitionPortFormatValueUpdated="$emit('TemplatePartitionPortFormatValueUpdated', $event)"
      v-on:TemplatePartitionPortFormatTypeUpdated="$emit('TemplatePartitionPortFormatTypeUpdated', $event)"
      v-on:TemplatePartitionPortFormatCountUpdated="$emit('TemplatePartitionPortFormatCountUpdated', $event)"
      v-on:TemplatePartitionPortFormatOrderUpdated="$emit('TemplatePartitionPortFormatOrderUpdated', $event)"
      @TemplatePartitionPortFormatFieldMove="$emit('TemplatePartitionPortFormatFieldMove', $event)"
      v-on:TemplatePartitionPortFormatFieldCreate="$emit('TemplatePartitionPortFormatFieldCreate', $event)"
      v-on:TemplatePartitionPortFormatFieldDelete="$emit('TemplatePartitionPortFormatFieldDelete', $event)"
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
  },
	directives: {
		Ripple,
	},
  props: {
    CardTitle: {type: String},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
  },
  data() {
    return {
    }
  },
  computed: {
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
        const ObjectID = vm.PartitionAddressSelected[Context].object_id
        let ReturnString = '-'

        if(Context == 'workspace') {
          if(ObjectID) {
            const ObjectIndex = vm.GetObjectIndex(ObjectID)
            ReturnString = vm.Objects[Context][ObjectIndex].name
          }
        } else {
          ReturnString = 'N/A'
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
        const ObjectID = vm.PartitionAddressSelected[Context].object_id
        let ReturnString = '-'

        if(ObjectID) {
          ReturnString = 'Trunked To'
        }

        return ReturnString
      },
    },
    TemplateType: {
      get() {

        const vm = this
        const Context = vm.Context
        const Template = vm.GetTemplateSelected(Context)

        return (Template) ? Template.type : '-'
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

        return (Template) ? Template.ru_size : '-'
      },
    },
    ComputedMountConfig: {
      get() {

        const vm = this
        const Context = vm.Context
        const Template = vm.GetTemplateSelected(Context)

        return (Template) ? Template.mount_config : '-'
      },
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
    GetObjectIndex: function(ObjectID) {

      const vm = this
      const Context = vm.Context
      const ObjectIndex = vm.Objects[Context].findIndex((object) => object.id == ObjectID);
      
      return ObjectIndex
    },
    GetCategoryIndex: function(CategoryID) {

      const vm = this
      const CategoryIndex = vm.Categories.findIndex((category) => category.id == CategoryID)

      return parseInt(CategoryIndex)
    },
    GetPartition: function(Blueprint, PartitionAddress) {

      // Store variables
      let WorkingPartitionChildren = Blueprint
      let SelectedPartition = WorkingPartitionChildren

      // Traverse blueprint until selected partition is reached
      PartitionAddress.forEach(function(AddressIndex, Index) {
        SelectedPartition = WorkingPartitionChildren[AddressIndex]
        WorkingPartitionChildren = SelectedPartition.children
      })

      // Return selected partition
      return SelectedPartition
    },
    DeleteTemplate: function() {

      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
      const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
      const TemplateID = Template.id
      const URL = '/api/templates/'+TemplateID

      vm.$http.delete(URL).then(response => {
        const PartitionAddressSelected = {
          Context,
          object_id: null,
          template_id: null,
          front: [0],
          rear: [0]
        }
        vm.$emit('SetPartitionAddressSelected', PartitionAddressSelected)
        vm.$store.commit('pcmTemplates/REMOVE_Template', {pcmContext:'template', data:response.data})
      }).catch(error => {
        vm.DisplayError(error)
      })
    }
  }
}
</script>
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
              @click=" $emit('TemplateObjectEditClicked') "
              :disabled="!TemplateSelected"
            >Edit
            </b-dropdown-item>

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
              @click=" $emit('TemplateObjectDeleteClicked') "
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
          {{ComputedTemplateName}}
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
          {{ComputedType}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Function:
        </td>
        <td>
        </td>
        <td>
          {{ComputedFunction}}
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
          {{TemplatePartitionPortRange}}
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
      ModalTitle="Template Name"
      :NameValue="ComputedTemplateName"
      @TemplateNameEdited=" $emit('TemplateEdited', $event) "
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
    PortConnectorData: {type: Array},
    MediaData: {type: Array},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    TemplatePartitionPortRange: {type: String},
    PortOrientationData: {type: Array},
  },
  data() {
    return {
    }
  },
  computed: {
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
      const TemplateID = vm.PartitionAddressSelected[Context].template_id

      return (TemplateID === null) ? false : true
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
    ComputedTemplateName: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'
        
        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          ReturnString = vm.Templates[Context][TemplateIndex].name
        }

        return ReturnString
      },
    },
    ComputedCategoryName: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          const CategoryID = vm.Templates[Context][TemplateIndex].category_id
          const CategoryIndex = vm.GetCategoryIndex(CategoryID)
          ReturnString = vm.Categories[CategoryIndex].name
        }

        return ReturnString
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
    ComputedType: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          ReturnString = vm.Templates[Context][TemplateIndex].type
          ReturnString = ReturnString.charAt(0).toUpperCase() + ReturnString.slice(1)
        }

        return ReturnString
      },
    },
    ComputedFunction: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          ReturnString = vm.Templates[Context][TemplateIndex].function
          ReturnString = ReturnString.charAt(0).toUpperCase() + ReturnString.slice(1)
        }

        return ReturnString
      },
    },
    ComputedRUSize: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          ReturnString = vm.Templates[Context][TemplateIndex].ru_size
        }

        return ReturnString
      },
    },
    ComputedMountConfig: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          ReturnString = vm.Templates[Context][TemplateIndex].mount_config
        }

        return ReturnString
      },
    },
    ComputedPartitionType: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        let ReturnString = '-'

        if(TemplateID) {

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          const Template = vm.Templates[Context][TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          ReturnString = (Partition.type) ? Partition.type : 'generic'
          ReturnString = ReturnString.charAt(0).toUpperCase() + ReturnString.slice(1)
        }

        return ReturnString
      },
    },
    ComputedPortOrientation: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        let ReturnString = '-'

        if(TemplateID) {

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          const Template = vm.Templates[Context][TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          const PartitionType = Partition.type

          if(PartitionType == 'connectable') {
            const PortOrientationID = Partition.port_orientation
            const PortOrientationIndex = vm.GetPortOrientationIndex(PortOrientationID)
            const PortOrientationName = vm.PortOrientationData[PortOrientationIndex].name
            ReturnString = PortOrientationName
          } else {

            ReturnString = 'N/A'
          }
        }

        return ReturnString
      },
    },
    ComputedPortType: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        let ReturnString = '-'

        if(TemplateID) {

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          const Template = vm.Templates[Context][TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          const PartitionType = Partition.type

          if(PartitionType == 'connectable') {
            const PortConnectorID = Partition.port_connector
            const PortConnectorIndex = vm.GetPortConnectorIndex(PortConnectorID)
            ReturnString = vm.PortConnectorData[PortConnectorIndex].name
          } else {

            ReturnString = 'N/A'
          }
        }

        return ReturnString
      },
    },
    ComputedPortMediaType: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        let ReturnString = '-'

        if(TemplateID) {

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
          const Template = vm.Templates[Context][TemplateIndex]
          const TemplateType = Template.type

          if(TemplateType == 'passive') {
            // Get partition
            const Blueprint = Template.blueprint[TemplateFace]
            const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
            const Partition = vm.GetPartition(Blueprint, PartitionAddress)

            // Get partition type
            const PartitionType = Partition.type

            if(PartitionType == 'connectable') {
              const MediaID = Partition.media
              const MediaIndex = vm.GetMediaIndex(MediaID)
              ReturnString = vm.MediaData[MediaIndex].name
            }
          } else {

            ReturnString = 'N/A'
          }
        }

        return ReturnString
      },
    },
  },
  methods: {
    GetPortOrientationIndex: function(PortOrientationID) {

      const vm = this
      const PortOrientationIndex = vm.PortOrientationData.findIndex((PortOrientation) => PortOrientation.value == PortOrientationID);
      
      return PortOrientationIndex
    },
    GetPortConnectorIndex: function(PortConnectorID) {

      const vm = this
      const PortConnectorIndex = vm.PortConnectorData.findIndex((PortConnector) => PortConnector.value == PortConnectorID);
      
      return PortConnectorIndex
    },
    GetMediaIndex: function(MediaID) {

      const vm = this
      const MediaIndex = vm.MediaData.findIndex((Media) => Media.value == MediaID);
      
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
  }
}
</script>
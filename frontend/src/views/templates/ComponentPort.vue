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
              v-b-modal.port-select-port
              :disabled="!PortIsSelected"
            >Connect
            </b-dropdown-item>

          </b-dropdown>
        </div>
      </div>
    </b-card-title>
		<b-card-body>

    <table>
      <tr>
        <td class="text-right">
          Selection:
        </td>
        <td>
        </td>
        <td>
          <b-form-select
            name="Port"
            v-model="SelectedPortIndex"
            :options="PortOptions"
          />
        </td>
      </tr>
      <tr>
        <td class="text-right">
          Description:
        </td>
        <td>
          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-object-name
            :disabled="!PortIsSelected"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{Description}}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          Populated:
        </td>
        <td>
        </td>
        <td>
          <b-form-checkbox
            v-model="Populated"
            value="yes"
            plain
            :disabled="!PortIsSelected"
          >
          </b-form-checkbox>
        </td>
      </tr>
    </table>

  </b-card-body>
  
  </b-card>

    <!-- Modal Edit Object Name -->
    <modal-edit-object-name
      :Context="Context"
      :PartitionAddressSelected="PartitionAddressSelected"
      ModalTitle="Port Description"
    />

    <!-- Modal Port Select -->
    <modal-port-select
      ModalID="port-select-port"
      ModalTitle="Port Connect"
      TreeRef="PortSelectPort"
      :Context="Context"
      :PartitionAddressSelected="PartitionAddressSelected"
      PortSelectFunction="port"
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
  BFormCheckbox,
  BFormSelect,
} from 'bootstrap-vue'
import ModalEditObjectName from './ModalEditObjectName.vue'
import ModalPortSelect from './ModalPortSelect.vue'
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
    BFormCheckbox,
    BFormSelect,

    ModalEditObjectName,
    ModalPortSelect,
  },
	directives: {
		Ripple,
	},
  props: {
    CardTitle: {type: String},
    Context: {type: String},
    PartitionAddressSelected: {type: Object},
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
    PortIsSelected: {
      get() {
        return (this.SelectedPortIndex !== null) ? true : false
      }
    },
    SelectedPortIndex: {
      get() {

        const vm = this
        const Context = vm.Context
        const Face = vm.PartitionAddressSelected[Context].object_face
        const PortID = vm.PartitionAddressSelected[Context].port_id[Face]

        return PortID
      },
      set() {}
    },
    Description: {
      get() {
        const vm = this
        const Context = vm.Context

        return 'DescriptionText'
      },
    },
    Populated: {
      get() {

        const vm = this
        const Context = vm.Context

        return ['yes']
      },
    },
    PortOptions: {
      get() {

        // Store variables
        const vm = this
        const Context = vm.Context
        let WorkingArray = []

        if(vm.PortIsSelected) {
          const Partition = vm.GetPartitionSelected(Context)
          const PortFormat = Partition.port_format
          const PortTotal = Partition.port_layout.cols * Partition.port_layout.rows

          // Populate working array with data to be used as select options
          for(let i = 0; i < PortTotal; i++) {

            const PortID = vm.GeneratePortID(i, PortTotal, PortFormat)
            WorkingArray.push({'value': i, 'text': PortID})
          }
        }

        return WorkingArray
      },
    },
  },
  methods: {
  }
}
</script>
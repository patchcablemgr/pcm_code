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
              v-b-modal.modal-port-connect
              :disabled="!PortIsSelected"
            >Connect
            </b-dropdown-item>

            <b-dropdown-item
              v-b-modal.modal-cable-select
              :disabled="!PortIsSelected"
            >Cable
            </b-dropdown-item>

            <b-dropdown-divider />

            <b-dropdown-item
              variant="danger"
              @click=" Clear() "
              :disabled="!PortIsSelected"
            >Clear Connection</b-dropdown-item>

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
            :disabled="!PortIsSelected"
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
            v-b-modal.modal-edit-port-description
            :disabled="!PortIsSelected"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{ComputedPortDescription}}
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
            plain
            :disabled="!PortIsSelected || PortIsConnected"
          >
          </b-form-checkbox>
        </td>
      </tr>
    </table>

  </b-card-body>
  
  </b-card>

    <!-- Modal Edit Port Description -->
    <modal-edit-port-description
      ModalID="modal-edit-port-description"
      ModalTitle="Port Description"
      :Context="Context"
    />

    <!-- Modal Port Connect -->
    <modal-port-connect
      ModalID="modal-port-connect"
      ModalTitle="Port Connect"
      TreeRef="TreeSelectPort"
      :Context="Context"
      PortSelectFunction="port"
    />

    <!-- Modal Port Cable -->
    <modal-cable-select
      ModalID="modal-cable-select"
      ModalTitle="Cable"
      :Context="Context"
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
import ModalEditPortDescription from './ModalEditPortDescription.vue'
import ModalPortConnect from './ModalPortConnect.vue'
import ModalCableSelect from './ModalCableSelect.vue'
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

    ModalEditPortDescription,
    ModalPortConnect,
    ModalCableSelect,
  },
	directives: {
		Ripple,
	},
  props: {
    CardTitle: {type: String},
    Context: {type: String},
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
    Ports() {
      return this.$store.state.pcmPorts.Ports
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    PortIsSelected: {
      get() {
        return (this.SelectedPortIndex == null || typeof this.SelectedPortIndex == 'undefined') ? false : true
      }
    },
    PortIsConnected: {
      get() {
        const vm = this
        const Context = vm.Context
        const ObjectID = vm.StateSelected[Context].object_id
        const Face = vm.StateSelected[Context].object_face
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const PortID = vm.StateSelected[Context].port_id[Face]

        const Connection = vm.GetConnection(ObjectID, Face, PartitionAddress, PortID)

        if(Connection) {
          return (Connection.data.a_id !== null && Connection.data.b_id !== null) ? true : false
        } else {
          return false
        }
      }
    },
    SelectedPortIndex: {
      get() {

        const vm = this
        const Context = vm.Context
        const Face = vm.StateSelected[Context].object_face
        const PortID = vm.StateSelected[Context].port_id[Face]

        return PortID
      },
      set(PortID) {

        const vm = this
        const Context = vm.Context

        if(vm.PortIsSelected) {
          const ObjectFace = vm.StateSelected[Context].object_face
          const ObjectID = vm.StateSelected[Context].object_id
          const TemplateID = vm.GetTemplateID(ObjectID, Context)
          const PartitionAddress = vm.StateSelected[Context].partition[ObjectFace]
          vm.PartitionClicked({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'TemplateID': TemplateID, 'PartitionAddress': PartitionAddress, 'PortID': PortID})
        }
      }
    },
    ComputedPortDescription() {

      const vm = this

      const ObjectID = vm.StateSelected.actual.object_id
      const ObjectFace = vm.StateSelected.actual.object_face
      const ObjectPartition = vm.StateSelected.actual.partition[ObjectFace]
      const PortID = vm.StateSelected.actual.port_id[ObjectFace]

      const PortIndex = vm.Ports.findIndex((port) => port.object_id == ObjectID && port.object_face == ObjectFace && JSON.stringify(port.object_partition) == JSON.stringify(ObjectPartition) && port.port_id == PortID)
      let PortDescription
      if (PortIndex != -1) {
        PortDescription = vm.Ports[PortIndex].description
      } else {
        PortDescription = 'None'
      }

      return PortDescription
    },
    Populated: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.StateSelected[Context].object_id
        const Face = vm.StateSelected[Context].object_face
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const PortID = vm.StateSelected[Context].port_id[Face]

        const Connection = vm.GetConnection(ObjectID, Face, PartitionAddress, PortID)

        return (Connection) ? true : false
      },
      set(NewValue) {

        const vm = this
        if(NewValue !== vm.Populated) {

          const Context = vm.Context
          const ObjectID = vm.StateSelected[Context].object_id
          const Face = vm.StateSelected[Context].object_face
          const PartitionAddress = vm.StateSelected[Context].partition[Face]
          const PortID = vm.StateSelected[Context].port_id[Face]

          //const Connection = vm.GetConnection(ObjectID, Face, PartitionAddress, PortID)

          if(NewValue) {

            // Compile POST data
            const data = {
              'a_id':ObjectID,
              'a_face':Face,
              'a_partition':PartitionAddress,
              'a_port':PortID
            }

            // POST Connection
            const URL = '/api/connections'
            vm.$http.post(URL, data).then(response => {

              // Add connection to store
              response.data.add.forEach(add => vm.$store.commit('pcmConnections/ADD_Connection', {data:add}))
              response.data.remove.forEach(remove => vm.$store.commit('pcmConnections/REMOVE_Connection', {data:remove}))

            }).catch(error => {vm.DisplayError(error)})
          } else {

            const Connection = vm.GetConnection(ObjectID, Face, PartitionAddress, PortID)

            if(Connection) {
              const ConnectionID = Connection.data.id
              // Delete Connection
              const URL = '/api/connections/'+ConnectionID
              vm.$http.delete(URL).then(response => {

                // Remove trunk from store
                vm.$store.commit('pcmConnections/REMOVE_Connection', {data:response.data})

              }).catch(error => {vm.DisplayError(error)})
            }
          }
        }
      }
    },
    PortOptions: {
      get() {

        // Store variables
        const vm = this
        const Context = vm.Context
        let WorkingArray = []

        if(vm.PortIsSelected) {

          // Get selected port details
          const ObjectID = vm.StateSelected.actual.object_id
          const ObjectFace = vm.StateSelected.actual.object_face
          const ObjectPartition = vm.StateSelected.actual.partition[ObjectFace]
          const Partition = vm.GetPartitionSelected(Context)
          const PortFormat = Partition.port_format
          const PortTotal = Partition.port_layout.cols * Partition.port_layout.rows
          let PortDescription = ''

          // Populate working array with data to be used as select options
          for(let i = 0; i < PortTotal; i++) {

            const PortName = vm.GeneratePortName(Context, ObjectID, ObjectFace, ObjectPartition, i)
            //const PortName = vm.GeneratePortID(i, PortTotal, PortFormat)
            const PortIndex = vm.Ports.findIndex((port) => port.object_id == ObjectID && port.object_face == ObjectFace && JSON.stringify(port.object_partition) == JSON.stringify(ObjectPartition) && port.port_id == i)
            
            if (PortIndex != -1) {
              PortDescription = ' ('+vm.Ports[PortIndex].description+')'
            }
            const OptionText = PortName+PortDescription
            WorkingArray.push({'value': i, 'text': OptionText})
            PortDescription = ''
          }
        }

        return WorkingArray
      },
    },
  },
  methods: {
    Clear: function() {

      const vm = this
      const Context = vm.Context

      const ConfirmMsg = "Clear connection?"
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {

        if (result === true) {
          const ObjectID = vm.StateSelected[Context].object_id
          const Face = vm.StateSelected[Context].object_face
          const Partition = vm.StateSelected[Context].partition[Face]
          const PortID = vm.StateSelected[Context].port_id[Face]
          const Connection = vm.GetConnection(ObjectID, Face, Partition, PortID)
          const ConnectionID = Connection.data.id
          
          // Delete Connection
          const URL = '/api/connections/'+ConnectionID
          vm.$http.delete(URL).then(response => {

            // Remove trunk from store
            vm.$store.commit('pcmConnections/REMOVE_Connection', {data:response.data})

            // Close modal
            vm.$root.$emit('bv::hide::modal', vm.ModalID)

          }).catch(error => {vm.DisplayError(error)})
        }
      })
    }
  }
}
</script>
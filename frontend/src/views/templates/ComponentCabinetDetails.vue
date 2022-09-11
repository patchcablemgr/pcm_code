<!-- Template/Object Details -->

<template>
  <div>
  <b-card>
    <b-card-title class="mb-0">
      <div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing">
          {{CardTitle}}
        </div>
      </div>
    </b-card-title>
		<b-card-body>
		
    <div
      class="h5 font-weight-bolder m-0 mt-2"
    >
			Properties:
    </div>
    <hr
      class="separator mt-0"
    >

    <table>

      <!-- RU Size -->
      <tr>
        <td class="text-right">
          RU Size:
        </td>
        <td>
          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-cabinet-size
            :disabled="ComputedCabinetSize == 'N/A'"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{ComputedCabinetSize}}
        </td>
      </tr>

      <!-- RU Orientation -->
      <tr>
        <td class="text-right">
          RU Orientation:
        </td>
        <td>
          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-cabinet-orientation
            :disabled="ComputedCabinetSize == 'N/A'"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          {{ComputedCabinetOrientation}}
        </td>
      </tr>
    </table>

    <div
      class="h5 font-weight-bolder m-0 mt-2"
    >
      Cable Paths:
      <feather-icon
        icon="HelpCircleIcon"
        v-b-tooltip.hover.html="TTCablePath"
      />
    </div>
    <hr
      class="separator mt-0"
    >

    <b-table
      small
      :fields="CablePathFields"
      :items="ComputedCablePaths"
      responsive="sm"
    >

      <template #cell(cabinet)="data">
        {{ data.item.cabinet }}
      </template>

      <template #cell(distance)="data">
        {{ data.item.distance }}
      </template>

      <template #cell(notes)="data">
        {{ data.item.notes }}
      </template>

      <template #cell(actions)="data">
        <b-button
          v-ripple.400="'rgba(40, 199, 111, 0.15)'"
          variant="flat-success"
          class="btn-icon"
          @click=EditCablePath(data.item.id)
          v-b-tooltip.hover.html="TTEditCablePath"
        >
          <feather-icon icon="EditIcon" />
        </b-button>
        <b-button
          v-ripple.400="'rgba(40, 199, 111, 0.15)'"
          variant="flat-success"
          class="btn-icon"
          @click="DeleteCablePath(data.item.id)"
          v-b-tooltip.hover.html="TTDeleteCablePath"
        >
          <feather-icon icon="TrashIcon" />
        </b-button>
      </template>

    </b-table>

    <!-- Add Cable Path -->
    <b-button
      v-ripple.400="'rgba(113, 102, 240, 0.15)'"
      size="sm"
      variant="primary"
      @click="AddCablePath()"
      :disabled="ComputedCabinetSize == 'N/A'"
    >
      <feather-icon
        icon="PlusIcon"
        class="mr-50"
      />
      <span class="align-middle">Add Cable Path</span>
    </b-button>

    <div
      class="h5 font-weight-bolder m-0 mt-2"
    >
      Cabinet Adjacencies:
    </div>
    <hr
      class="separator mt-0"
    >

    <table>

      <!-- Left -->
      <tr>
        <td class="text-right">
          Left:
        </td>
        <td>
          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            @click="EditAdjacency('left')"
            :disabled="ComputedCabinetSize == 'N/A'"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          Cab1
        </td>
      </tr>

      <!-- Right -->
      <tr>
        <td class="text-right">
          Right:
        </td>
        <td>
          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-cabinet-size
            :disabled="ComputedCabinetSize == 'N/A'"
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </td>
        <td>
          Cab2
        </td>
      </tr>
    </table>

    </b-card-body>
  </b-card>

    <modal-edit-cabinet-size
      ModalID="modal-edit-cabinet-size"
      ModalTitle="Cabinet RU Size"
      Context="actual"
    />

    <modal-edit-cabinet-orientation
      ModalID="modal-edit-cabinet-orientation"
      ModalTitle="Cabinet RU Orientation"
      Context="actual"
    />

    <modal-cable-path
      ModalID="modal-cable-path"
      :ModalTitle="CablePathModalTitle"
      Context="actual"
      :CablePathID="CablePathID"
    />

    <modal-edit-cabinet-adjacency
      ModalID="modal-edit-cabinet-adjacency"
      ModalTitle="Cabinet Adjacency"
      Context="actual"
      :AdjacencySide="AdjacencySide"
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
  BTable,
  VBTooltip,
} from 'bootstrap-vue'
import ModalEditCabinetSize from '@/views/templates/ModalEditCabinetSize.vue'
import ModalEditCabinetOrientation from '@/views/templates/ModalEditCabinetOrientation.vue'
import ModalCablePath from '@/views/templates/ModalCablePath.vue'
import ModalEditCabinetAdjacency from '@/views/templates/ModalEditCabinetAdjacency.vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

const CablePathID = null
const CablePathModalTitle = ""
const AdjacencySide = "left"

const TTCablePath = {
  title: `
    <div class="text-left">
    <div>A cable path represents a path (e.g. ladder tray or conduit) between two cabinets that can be used to run patch cables between them.  Cable paths are used by PCM Path Finder when calculating possible cable paths and estimating required lengths.</div>
    </div>
  `
}

const TTEditCablePath = {
  title: `
    <div class="text-left">
    <div>Edit cable path.</div>
    </div>
  `
}

const TTDeleteCablePath = {
  title: `
    <div class="text-left">
    <div>Delete cable path.</div>
    </div>
  `
}

const CablePathFields = [
  {key: 'cabinet', label: 'Cabinet'},
  {key: 'distance', label: 'Distance (m)'},
  {key: 'notes', label: 'Notes'},
  {key: 'actions', label: 'Actions'},
]

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
    BTable,

    ModalEditCabinetSize,
    ModalEditCabinetOrientation,
    ModalCablePath,
    ModalEditCabinetAdjacency,
  },
	directives: {
		Ripple,
    'b-tooltip': VBTooltip,
	},
  props: {
    CardTitle: {type: String},
    Context: {type: String},
  },
  data() {
    return {
      CablePathID,
      CablePathModalTitle,
      AdjacencySide,
      TTCablePath,
      TTEditCablePath,
      TTDeleteCablePath,
      CablePathFields,
    }
  },
  computed: {
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    CablePaths() {
      return this.$store.state.pcmCablePaths.CablePaths
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    LocationID() {

      const vm = this
      const Context = vm.Context

      return vm.StateSelected[Context].location_id
    },
    ComputedCabinetSize: {
      get() {

        const vm = this
        const Context = vm.Context
        const LocationID = vm.LocationID
        let CabinetSize = 'N/A'

        if(LocationID) {
          const LocationIndex = vm.GetLocationIndex(LocationID, Context)
          const Location = vm.Locations[Context][LocationIndex]
          const LocationType = Location.type
          if(LocationType == 'cabinet') {
            CabinetSize = Location.size
          }
        }
        
        return CabinetSize
      },
      set() {
        return true
      }
    },

    ComputedCabinetOrientation: {
      get() {

        const vm = this
        const Context = vm.Context
        const LocationID = vm.LocationID
        let CabinetOrientation = 'N/A'

        if(LocationID) {
          const LocationIndex = vm.GetLocationIndex(LocationID, Context)
          const Location = vm.Locations[Context][LocationIndex]
          const LocationType = Location.type
          if(LocationType == 'cabinet') {
            CabinetOrientation = Location.ru_orientation
          }
        }
        
        return CabinetOrientation
      },
      set() {
        return true
      }
    },
    ComputedCablePaths: {
      get() {

        const vm = this
        const Context = vm.Context
        const LocationID = vm.LocationID
        let CablePaths = []

        const WorkingCablePaths = vm.CablePaths[Context].filter(entry => entry.cabinet_a_id == LocationID || entry.cabinet_b_id == LocationID)
        WorkingCablePaths.forEach(function(CablePath){
          const RemoteCabinetID = (CablePath.cabinet_a_id == LocationID) ? CablePath.cabinet_b_id : CablePath.cabinet_a_id
          const RemoteCabinetDN = vm.GenerateLocationDN(Context, RemoteCabinetID)
          CablePaths.push({
            id: CablePath.id,
            cabinet: RemoteCabinetDN,
            distance: CablePath.distance,
            notes: CablePath.notes,
          })
        })
        
        return CablePaths
      },
      set() {
        return true
      }
    },
  },
  methods: {
    DeleteCablePath(CablePathID){

      const vm = this
      const Context = vm.Context

      // Confirm Deletion
      const ConfirmMsg = 'Delete cable path?'
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {
        if (result === true) {
          vm.$http.delete('/api/cable-paths/'+CablePathID).then(response => {
            vm.$store.commit('pcmCablePaths/REMOVE_CablePath', {pcmContext:Context, data:response.data})
          }).catch(error => {
            vm.DisplayError(error)
          })
        }
      })
    },
    EditCablePath(CablePathID){

      const vm = this

      // Set some variables to be used by the modal
      vm.CablePathModalTitle = "Edit"
      vm.CablePathID = CablePathID

      // Display the modal
      vm.$root.$emit("bv::show::modal", "modal-cable-path")
    },
    AddCablePath(){

    const vm = this

      // Set some variables to be used by the modal
      vm.CablePathModalTitle = "Add"
      vm.CablePathID = null

      // Display the modal
      vm.$root.$emit("bv::show::modal", "modal-cable-path")
    },
    EditAdjacency(Side){

      const vm = this

      // Set some variables to be used by the modal
      vm.AdjacencySide = Side

      // Display the modal
      vm.$root.$emit("bv::show::modal", "modal-edit-cabinet-adjacency")
    },
  }
}
</script>
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
    </div>
    <hr
      class="separator mt-0"
    >

    <b-table
      small
      :fields="CablePathFields"
      :items="CablePathItems"
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
        {{ data.item.id }}
      </template>

    </b-table>

    <!-- Add Cable Path -->
    <b-button
      v-ripple.400="'rgba(113, 102, 240, 0.15)'"
      size="sm"
      variant="primary"
      v-b-modal.modal-edit-cabinet-path
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
            v-b-modal.modal-edit-cabinet-size
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

    <modal-edit-cabinet-path
      ModalID="modal-edit-cabinet-path"
      ModalTitle="Cabinet Path"
      Context="actual"
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
} from 'bootstrap-vue'
import ModalEditCabinetSize from '@/views/templates/ModalEditCabinetSize.vue'
import ModalEditCabinetOrientation from '@/views/templates/ModalEditCabinetOrientation.vue'
import ModalEditCabinetPath from '@/views/templates/ModalEditCabinetPath.vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'

const CablePathFields = [
  {key: 'cabinet', label: 'Cabinet'},
  {key: 'distance', label: 'Distance (m)'},
  {key: 'notes', label: 'Notes'},
  {key: 'actions', label: 'Actions'},
]
const CablePathItems = [
  {id: 1, cabinet: 'cab1', distance: 3, notes: 'This is a note'},
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
    ModalEditCabinetPath,
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
      CablePathFields,
      CablePathItems,
    }
  },
  computed: {
    Locations() {
      return this.$store.state.pcmLocations.Locations
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
  },
  methods: {
  }
}
</script>
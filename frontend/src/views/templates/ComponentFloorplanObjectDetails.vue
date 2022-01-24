<!-- Template/Object Details -->

<template>
  <div>
	
    <b-card
      title="Floorplan Object Details"
    >
      <b-card-body>

        <table>
          <tr>
            <td class="text-right">
              Object Name:
            </td>
            <td>
              <b-button
                v-ripple.400="'rgba(40, 199, 111, 0.15)'"
                variant="flat-success"
                class="btn-icon"
                v-b-modal.modal-name-edit
                :disabled="!ComputedObjectSelected"
              >
                <feather-icon icon="EditIcon" />
              </b-button>
            </td>
            <td>
              {{ObjectName}}
            </td>
          </tr>
          <tr>
            <td class="text-right">
              Template Name:
            </td>
            <td>
            </td>
            <td>
              {{TemplateName}}
            </td>
          </tr>
          <tr>
            <td class="text-right">
              Trunked:
            </td>
            <td>
              <b-button
                v-ripple.400="'rgba(40, 199, 111, 0.15)'"
                variant="flat-success"
                class="btn-icon"
                v-b-modal.modal-port-select
                :disabled="!ComputedObjectSelected"
              >
                <feather-icon icon="EditIcon" />
              </b-button>
            </td>
            <td>
              {{ComputedTrunked}}
            </td>
          </tr>
        </table>

      </b-card-body>
    
    </b-card>

    <!-- Modal Edit Object Name -->
    <modal-edit-object-name
      ModalTitle="Object Name"
      :NameValue="ObjectName"
      @NameEdited=" $emit('ObjectEdited', $event) "
    />

    <!-- Modal Port Select -->
    <modal-port-select
      ModalTitle="ObjectName"
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
  VBModal,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalEditObjectName from '@/views/templates/ModalEditObjectName.vue'
import ModalPortSelect from '@/views/templates/ModalPortSelect.vue'

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

    VBModal,
    ModalEditObjectName,
    ModalPortSelect,
  },
	directives: {
		Ripple,
    'b-modal': VBModal,
	},
  props: {
    Context: {type: String},
    FloorplanTemplateData: {type: Array},
    PartitionAddressSelected: {type: Object},
  },
  data() {
    return {
    }
  },
  computed: {
    FloorplanTemplates() {
      return this.$store.state.pcmFloorplanTemplates.FloorplanTemplates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    ComputedObjectSelected: function() {

      const vm = this
      return (vm.PartitionAddressSelected.floorplan.object_id) ? true : false
    },
    ObjectName: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.PartitionAddressSelected.floorplan.object_id
      let ObjectName = '-'

      if(ObjectID) {
        const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
        const Object = vm.Objects[Context][ObjectIndex]
        ObjectName = Object.name
      }

      return ObjectName
    },
    TemplateName: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.PartitionAddressSelected.floorplan.object_id
      let TemplateName = '-'

      if(ObjectID) {
        const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
        const Object = vm.Objects[Context][ObjectIndex]
        const FloorplanObjectType = Object.floorplan_object_type
        const FloorplanTemplateIndex = vm.FloorplanTemplates.findIndex((floorplanTemplate) => floorplanTemplate.type == FloorplanObjectType)
        const FloorplanTemplate = vm.FloorplanTemplates[FloorplanTemplateIndex]

        TemplateName = FloorplanTemplate.name
      }

      return TemplateName
    },
    ComputedTrunked: function() {

      const vm = this
      let Trunked = '-'

      return Trunked
    },
  },
  methods: {
  }
}
</script>
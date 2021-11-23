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
              {{ComputedObjectName}}
            </td>
          </tr>
          <tr>
            <td class="text-right">
              Template Name:
            </td>
            <td>
            </td>
            <td>
              {{ComputedTemplateName}}
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
      :NameValue="ComputedObjectName"
      @NameEdited=" $emit('ObjectEdited', $event) "
    />

    <!-- Modal Port Select -->
    <modal-port-select
      ModalTitle="ObjectName"
      :ObjectData="ObjectData"
      :TemplateData="TemplateData"
      :LocationData="LocationData"
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
import { PCM } from '../../mixins/PCM.js'
import ModalEditObjectName from './ModalEditObjectName.vue'

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
  },
	directives: {
		Ripple,
    'b-modal': VBModal,
	},
  props: {
    FloorplanTemplateData: {type: Array},
    ObjectData: {type: Object},
    TemplateData: {type: Object},
    LocationData: {type: Array},
    PartitionAddressSelected: {type: Object},
  },
  data() {
    return {
    }
  },
  computed: {
    ComputedObjectSelected: function() {

      const vm = this
      return (vm.PartitionAddressSelected.floorplan.object_id) ? true : false
    },
    ComputedObjectName: function() {

      const vm = this
      const ObjectID = vm.PartitionAddressSelected.floorplan.object_id
      let ObjectName = '-'

      if(ObjectID) {
        const ObjectIndex = vm.GetObjectIndex(ObjectID, 'preview')
        const Object = vm.ObjectData.preview[ObjectIndex]
        ObjectName = Object.name
      }

      return ObjectName
    },
    ComputedTemplateName: function() {

      const vm = this
      const ObjectID = vm.PartitionAddressSelected.floorplan.object_id
      let TemplateName = '-'

      if(ObjectID) {
        const ObjectIndex = vm.GetObjectIndex(ObjectID, 'preview')
        const Object = vm.ObjectData.preview[ObjectIndex]
        const ObjectTemplateType = Object.floorplan_object_type
        const FloorplanTemplate = vm.FloorplanTemplateData.find((template) => template.type == ObjectTemplateType)
        console.log(FloorplanTemplate)

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
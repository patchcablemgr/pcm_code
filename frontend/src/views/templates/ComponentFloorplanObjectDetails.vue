<!-- Template/Object Details -->

<template>
  <div>
	
    <b-card>
      <b-card-title>
        <div class="d-flex flex-wrap justify-content-between">
          <div class="demo-inline-spacing">
            Floorplan Object Details
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
                variant="danger"
                @click=" Delete() "
                :disabled="!ComputedObjectSelected"
              >
                Delete
              </b-dropdown-item>

            </b-dropdown>
          </div>
        </div>
      </b-card-title>
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
                v-b-modal.modal-edit-object-name
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
                :disabled="!ComputedObjectSelected || !Trunkable"
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
      :Context="Context"
      :PartitionAddressSelected="PartitionAddressSelected"
      ModalTitle="Object Name"
    />

    <!-- Modal Port Select -->
    <modal-port-select
      ModalTitle="Trunk"
      TreeRef="PortSelect"
      :Context="Context"
      :PartitionAddressSelected="PartitionAddressSelected"
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
      const Context = vm.Context
      return (vm.PartitionAddressSelected[Context].object_id) ? true : false
    },
    Trunkable: function() {

      const vm = this
      let Trunkable = false

      if(vm.TemplateType == 'wap' || vm.TemplateType == 'walljack' || vm.TemplateType == 'camera') {
        Trunkable = true
      } else {
        Trunkable = false
      }

      return Trunkable
    },
    ObjectName: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
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
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
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
    TemplateType: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
      let TemplateType = null

      if(ObjectID) {
        const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
        const Object = vm.Objects[Context][ObjectIndex]
        TemplateType = Object.floorplan_object_type
      }

      return TemplateType
    },
    ComputedTrunked: function() {

      const vm = this
      let Trunked = '-'

      if(vm.TemplateType == 'wap' || vm.TemplateType == 'walljack' || vm.TemplateType == 'camera') {
        Trunked = 'maybe'
      } else {
        Trunked = 'N/A'
      }

      return Trunked
    },
  },
  methods: {
    Delete: function() {

      const vm = this
      const Context = vm.Context

      const ObjectID = vm.PartitionAddressSelected[Context].object_id
      const ObjectIndex = vm.Objects[Context].findIndex((object) => object.id == ObjectID)
      const Object = vm.Objects[Context][ObjectIndex]
      const ObjectName = Object.name
      const TemplateName = Object.floorplan_object_type

      // Confirm Deletion
      const ConfirmMsg = ObjectName+" ("+TemplateName+")"
      const ConfirmOpts = {
        title: "Delete?"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {

        if (result === true) {

          // Delete Object
          const URL = '/api/objects/'+ObjectID

          vm.$http.delete(URL).then(response => {

            // Clear user selection
            const PartitionAddressSelected = {
              Context,
              object_id: null,
              template_id: null,
              front: [0],
              rear: [0]
            }
            vm.$emit('SetPartitionAddressSelected', PartitionAddressSelected)

            // Remove object from store
            vm.$store.commit('pcmObjects/REMOVE_Object', {pcmContext:Context, data:response.data})
            

          }).catch(error => {
            vm.DisplayError(error)
          })
        }
      })
    },
  }
}
</script>
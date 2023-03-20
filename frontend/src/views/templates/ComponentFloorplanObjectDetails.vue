<!-- Template/Object Details -->

<template>
  <div>
	
    <b-card>
      <b-card-title>
        <div class="d-flex flex-wrap justify-content-between">
          <div class="demo-inline-spacing">
            Floorplan Object Details
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
                variant="danger"
                @click=" Delete() "
                :disabled="!ComputedObjectSelected"
              >
                Delete
              </b-dropdown-item>

              <b-dropdown-item
                variant="danger"
                @click=" Clear() "
                :disabled="!ComputedObjectSelected"
              >
                Clear Trunk
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
                v-if="DetailsAreEditable"
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
                v-if="DetailsAreEditable"
                v-ripple.400="'rgba(40, 199, 111, 0.15)'"
                variant="flat-success"
                class="btn-icon"
                v-b-modal.modal-trunk-select
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
      ModalID="modal-edit-object-name"
      ModalTitle="Object Name"
      :Context="Context"
    />

    <!-- Modal Tree Select -->
    <modal-trunk-select
      ModalID="modal-trunk-select"
      ModalTitle="Trunk"
      TreeRef="TrunkSelect"
      :Context="Context"
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
  VBModal,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalEditObjectName from '@/views/templates/ModalEditObjectName.vue'
import ModalTrunkSelect from '@/views/templates/ModalTrunkSelect.vue'

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
    ModalTrunkSelect,
  },
	directives: {
		Ripple,
    'b-modal': VBModal,
	},
  props: {
    Context: {type: String},
    FloorplanTemplateData: {type: Array},
    DetailsAreEditable: {type: Boolean},
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
    Trunks() {
      return this.$store.state.pcmTrunks.Trunks
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    ComputedObjectSelected: function() {

      const vm = this
      const Context = vm.Context
      return (vm.StateSelected[Context].object_id) ? true : false
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
      const ObjectID = vm.StateSelected[Context].object_id
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
      const ObjectID = vm.StateSelected[Context].object_id
      let TemplateName = '-'

      if(ObjectID) {
        const Template = vm.GetTemplate({ObjectID, Context})
        TemplateName = Template.name
      }

      return TemplateName
    },
    TemplateType: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.StateSelected[Context].object_id
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
      const Context = vm.Context
      let Trunked = '-'

      if(vm.TemplateType == 'wap' || vm.TemplateType == 'walljack' || vm.TemplateType == 'camera') {
        const ObjectID = vm.StateSelected[Context].object_id
        const Trunks = vm.GetTrunks(ObjectID, 'front', [0])
        Trunked = (Trunks.length) ? 'Yes' : 'No'
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

      const ObjectID = vm.StateSelected[Context].object_id
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
            vm.$store.commit('pcmState/DEFAULT_Selected_Object', {pcmContext:Context})

            // Remove object from store
            vm.$store.commit('pcmObjects/REMOVE_Object', {pcmContext:Context, data:response.data})
            

          }).catch(error => {
            vm.DisplayError(error)
          })
        }
      })
    },
    Clear: function() {

      const vm = this
      const Context = vm.Context
      const SelectedObjectID = vm.StateSelected[Context].object_id
      const SelectedObjectFace = vm.StateSelected[Context].object_face
      const SelectedObjectPartition = vm.StateSelected[Context].partition[SelectedObjectFace]
      const ConfirmMsg = "Clear trunk?"
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {
        if (result === true) {
          const Trunks = vm.GetTrunks(SelectedObjectID, SelectedObjectFace, SelectedObjectPartition)
          Trunks.forEach(function(trunk){
            const TrunkID = trunk.id
            // Delete Trunk
            const URL = '/api/trunks/'+TrunkID
            vm.$http.delete(URL).then(response => {
              // Remove trunk from store
              vm.$store.commit('pcmTrunks/REMOVE_Trunk', {data:response.data})
            }).catch(error => {vm.DisplayError(error)})
          })
        }
      })
    }
  }
}
</script>
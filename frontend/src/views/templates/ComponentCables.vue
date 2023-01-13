<template>
  <div>
    <b-card>
      <b-card-title class="mb-0">
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
                v-b-modal.modal-create-cable
              >
                Create
              </b-dropdown-item>

            </b-dropdown>
          </div>
        </div>
      </b-card-title>
      <b-card-body>

        <!-- Filter -->
        <b-form-tags
          v-model="CableFilter"
          placeholder="Add filter tags..."
          input-id="template-filter"
          class="mb-2"
        />
        
        <b-table
          small
          bordered
          no-border-collapse
          :fields="CableFields"
          :items="FilteredCables"
          responsive="sm"
          class="overflow-auto"
        >

          <template #thead-top="data">
            <b-tr>
              <b-th style="text-align:center" colspan="3">A Side</b-th>
              <b-th style="text-align:center" colspan="3">B Side</b-th>
              <b-th style="text-align:center" colspan="3">Properties</b-th>
            </b-tr>
          </template>

          <template #cell(a_connector_id)="data">
            {{ CableConnectors.find(element => element.value == data.item.a_connector_id).name }}
          </template>

          <template #cell(a_port)="data">
            <div
              class="pcm_box"
              :style="{ background: GetCategoryColor(data.item.a_id)}"
            >
              {{ GetObjectDN(data.item.a_id) }}
            </div>
          </template>

          <template #cell(b_connector_id)="data">
            {{ CableConnectors.find(element => element.value == data.item.b_connector_id).name }}
          </template>

          <template #cell(b_port)="data">
            <div
              class="pcm_box"
              :style="{ background: GetCategoryColor(data.item.b_id)}"
            >
            {{ GetObjectDN(data.item.b_id) }}
            </div>
          </template>

          <template #cell(media_id)="data">
            {{ Media.find(element => element.value == data.item.media_id).name }}
          </template>

          <template #cell(length)="data">
            {{ FormatLength(data.item.media_id, data.item.length) }}
          </template>

          <template #cell(actions)="data">
            <b-button
              v-ripple.400="'rgba(40, 199, 111, 0.15)'"
              variant="flat-success"
              class="btn-icon"
              @click="DeleteCable(data.item.id)"
              :disabled="data.item.editable == false"
            >
              <feather-icon icon="TrashIcon" />
            </b-button>
          </template>

        </b-table>
      </b-card-body>
    </b-card>

  <!-- Modal Create Cable -->
  <modal-create-cable
    ModalID="modal-create-cable"
  />

  </div>
</template>

<script>
import {
  BContainer,
  BRow,
  BCol,
  BCard,
  BCardTitle,
  BCardBody,
  BTable,
  BTr,
  BTh,
  BThead,
  BDropdown,
  BDropdownItem,
  BDropdownDivider,
  BFormTags,
  BButton,
  VBTooltip,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ModalCreateCable from '@/views/templates/ModalCreateCable.vue'

const CableFilter = []

const CableFields = [
  {key: 'a_id', label: 'ID'},
  {key: 'a_connector_id', label: 'Connector'},
  {key: 'a_port', label: 'Port'},
  {key: 'b_id', label: 'ID'},
  {key: 'b_connector_id', label: 'Connector'},
  {key: 'b_port', label: 'Port'},
  {key: 'media_id', label: 'Media'},
  {key: 'length', label: 'Length'},
  {key: 'actions', label: 'Actions'},
]

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    BCardTitle,
    BCard,
    BCardBody,
    BTable,
    BTr,
    BTh,
    BThead,
    BDropdown,
    BDropdownItem,
    BDropdownDivider,
    BFormTags,
    BButton,

    ModalCreateCable,
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
	},
  props: {
    CardTitle: {type: String},
  },
  data() {
    return {
      Context: 'actual',
      CableFields,
      CableFilter,
    }
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.CablesReady,
        vm.CategoriesReady.actual,
        vm.ObjectsReady.actual,
        vm.ConnectionsReady,
        vm.TemplatesReady.actual,
        vm.LocationsReady.actual,
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    Cables() {
      return this.$store.state.pcmCables.Cables
    },
    CablesReady: function() {
      return this.$store.state.pcmCables.CablesReady
    },
    CableConnectors() {
      return this.$store.state.pcmProps.CableConnectors
    },
    Media() {
      return this.$store.state.pcmProps.Media
    },
    MediaType() {
      return this.$store.state.pcmProps.MediaType
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    ConnectionsReady: function() {
      return this.$store.state.pcmConnections.ConnectionsReady
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    CategoriesReady: function() {
      return this.$store.state.pcmCategories.CategoriesReady
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    LocationsReady: function() {
      return this.$store.state.pcmLocations.LocationsReady
    },
    FilteredCables: function() {

      const vm = this
      const FilteredCables = vm.Cables.filter(function(cable) {

        let match = false

        // Include if no filter tags
        if(vm.CableFilter.length == 0) {
          match = true
        } else {

          // Compare each filter tag
          vm.CableFilter.forEach(function(tag){

            // Include if template name contains filter tag
            if(cable.a_id.toLowerCase().includes(tag.toLowerCase()) || cable.b_id.toLowerCase().includes(tag.toLowerCase())) {
              match = true
            }
          })
        }

        return match
      })

      return FilteredCables
    },
  },
  methods: {
    GetCategoryColor(CableEndID) {

      const vm = this
      let CategoryColor
      const ConnectionIndex = vm.Connections.findIndex(item => item.a_cable_id == CableEndID || item.b_cable_id == CableEndID)

      if(ConnectionIndex !== -1) {

        const Connection = vm.Connections[ConnectionIndex]
        const CableSide = (Connection.a_cable_id == CableEndID) ? 'a' : 'b'
        const ObjectID = Connection[CableSide+'_id']

        CategoryColor = vm.GetObjectCategoryColor(ObjectID)

      } else {
        CategoryColor = "#FFFFFFFF"
      }

      return CategoryColor
    },
    GetObjectDN(CableEndID) {

      const vm = this
      let ObjectDN
      const ConnectionIndex = vm.Connections.findIndex(item => item.a_cable_id == CableEndID || item.b_cable_id == CableEndID)

      if(ConnectionIndex !== -1) {

        const Connection = vm.Connections[ConnectionIndex]
        const CableSide = (Connection.a_cable_id == CableEndID) ? 'a' : 'b'
        const ObjectID = Connection[CableSide+'_id']
        const ObjectFace = Connection[CableSide+'_face']
        const ObjectPartition = Connection[CableSide+'_partition']
        const PortID = Connection[CableSide+'_port']

        if(ObjectID) {
          ObjectDN = vm.GenerateDN('port', ObjectID, ObjectFace, ObjectPartition, PortID)
        } else {
          ObjectDN = 'None'
        }

      } else {
        ObjectDN = 'None'
      }

      return ObjectDN
    },
    DeleteCable(CableID) {

      const vm = this

      // Confirm Deletion
      const ConfirmMsg = 'Delete cable?'
      const ConfirmOpts = {
        title: "Confirm"
      }
      vm.$bvModal.msgBoxConfirm(ConfirmMsg, ConfirmOpts).then(result => {
        if (result === true) {
          vm.$http.delete('/api/cables/'+CableID).then(response => {
            vm.$store.commit('pcmCables/REMOVE_Cable', {data:response.data})
          }).catch(error => {
            vm.DisplayError(error)
          })
        }
      })
    },
    FormatLength(MediaID, Length) {

      const vm = this
      let ConvertedLength
      const MediaIndex = vm.Media.findIndex((item) => item.value == MediaID)
      const Media = vm.Media[MediaIndex]
      const MediaTypeID = Media.type_id
      const MediaTypeIndex = vm.MediaType.findIndex((item) => item.value == MediaTypeID)
      const MediaType = vm.MediaType[MediaTypeIndex]
      if (MediaType.unit_of_length == 'ft.') {
        ConvertedLength = vm.ConvertCmToFeet(Length)
      } else {
        ConvertedLength = vm.ConvertCmToMeters(Length)
      }
      return ConvertedLength+' '+MediaType.unit_of_length
    }
  },
  mounted() {},
}
</script>

<style>

</style>

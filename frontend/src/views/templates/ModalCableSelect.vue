<template>
    <!-- Template port Format modal -->
    <b-modal
      :id="ModalID"
      :title="ModalTitle"
    >
      <b-card>
        <b-card-title>
          <feather-icon class="mr-1" icon="MapPinIcon" />
          {{SelectedDN}}
        </b-card-title>
        <b-card-text>
          <validation-observer
            ref="validation"
            v-slot="{ invalid }"
          >
            <b-form
              @submit.prevent="Submit"
            >
              <validation-provider
                name="Value"
                :rules="{regex: /^[A-Za-z0-9\/\/\_\-]+$/}"
                #default="{ errors }"
              >

                <!-- Cable ID -->
                <dl class="row">
                  <dt class="col-sm-4">
                    Local Cable End ID
                  </dt>
                  <dd class="col-sm-8">
                    <b-form-select
                      v-model="CableEndID"
                      :options="CableEndOptions"
                    />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </dd>
                </dl>

              </validation-provider>

            </b-form>
          </validation-observer>

          <!-- Local Connector -->
          <dl class="row">
            <dt class="col-sm-4">
              Local Connector
            </dt>
            <dd class="col-sm-8">
              {{ LocalConnector }}
            </dd>
          </dl>

          <!-- Local Port -->
          <dl class="row">
            <dt class="col-sm-4">
              Local Port
            </dt>
            <dd class="col-sm-8">
              <div
                class="pcm_box"
                :style="{ background: LocalCategoryColor}"
              >
                {{ SelectedDN }}
              </div>
            </dd>
          </dl>

          <!-- Remote Cable End ID -->
          <dl class="row">
            <dt class="col-sm-4">
              Remote Cable End ID
            </dt>
            <dd class="col-sm-8">
              {{ RemoteCableEndID }}
            </dd>
          </dl>

          <!-- Remote Connector -->
          <dl class="row">
            <dt class="col-sm-4">
              Remote Connector
            </dt>
            <dd class="col-sm-8">
              {{ RemoteConnector }}
            </dd>
          </dl>

          <!-- Remote Port -->
          <dl class="row">
            <dt class="col-sm-4">
              Remote Port
            </dt>
            <dd class="col-sm-8">
              <div
                class="pcm_box"
                :style="{ background: RemoteCategoryColor}"
              >
                {{ RemotePortDN }}
              </div>
            </dd>
          </dl>

          <!-- Cable Media -->
          <dl class="row">
            <dt class="col-sm-4">
              Media
            </dt>
            <dd class="col-sm-8">
              {{ CableMedia }}
            </dd>
          </dl>

          <!-- Cable Length -->
          <dl class="row">
            <dt class="col-sm-4">
              Length
            </dt>
            <dd class="col-sm-8">
              {{ CableLength }}
            </dd>
          </dl>

        </b-card-text>
      </b-card>

      <template #modal-footer>
        <b-button
          variant="secondary"
          class="float-right"
          @click="Cancel"
        >
          Cancel
        </b-button>
        <b-button
          variant="primary"
          class="float-right"
          @click="Submit"
        >
          Submit
        </b-button>
      </template>

    </b-modal>
</template>

<script>
import {
  BRow,
  BCol,
  BCard,
  BCardTitle,
  BCardText,
  BForm,
  BFormInput,
  BFormSelect,
  BButton,
  VBTooltip,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { configure, ValidationProvider, ValidationObserver } from 'vee-validate'
import { regex } from '@validations'
import { PCM } from '@/mixins/PCM.js'

const config = {
  useConstraintAttrs: false,
  defaultMessage: "invalid input"
}

configure(config)

const ToolTipPreview = {
  title: `
    <div class="text-left">
    <div>Preview Port IDs generated from Port Format settings above.</div>
    </div>
  `
}

export default {
  mixins: [PCM],
  components: {
    ValidationProvider,
    ValidationObserver,
    regex,
    BRow,
    BCol,
    BCard,
    BCardTitle,
    BCardText,
    BForm,
    BFormInput,
    BFormSelect,
    BButton,
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
  },
  props: {
    ModalID: {type: String},
    ModalTitle: {type: String},
    Context: {type: String},
    PortSelectFunction: {type: String},
  },
  data() {
    return {
      ToolTipPreview,
      CableEndID: null,
    }
  },
  computed: {
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
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
    Cables() {
      return this.$store.state.pcmCables.Cables
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    CableEndOptions: {
      get() {

        // Store variables
        const vm = this
        let OptionArray = []

        // Populate option array with data to be used as select options
        vm.Cables.forEach(element => OptionArray.push({'value': element.a_id, 'text': element.a_id}, {'value': element.b_id, 'text': element.b_id}))

        return OptionArray
      },
      set() {

      }
    },
    CableMediaOptions: {
      get() {

        // Store variables
        const vm = this
        let OptionArray = []

        // Populate option array with data to be used as select options
        vm.Media.forEach(element => OptionArray.push({'value': element.value, 'text': element.name}))

        return OptionArray
      },
      set() {

      }
    },
    SelectedCable() {

      const vm = this
      const CableEndID = vm.CableEndID

      const CableIndex = vm.Cables.findIndex((item) => item.a_id == CableEndID || item.b_id == CableEndID)
      if(CableIndex !== -1) {
        return vm.Cables[CableIndex]
      } else {
        return null
      }
    },
    LocalCableEndID() {

      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {
        const CableEndID = vm.CableEndID
        if(Cable.a_id == CableEndID) {
          return Cable.a_id
        } else {
          return Cable.b_id
        }
      } else {
        return 'N/A'
      }

    },
    LocalConnector() {

      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {
        const CableEndID = vm.CableEndID
        let ConnectorID
        if(Cable.a_id == CableEndID) {
          ConnectorID = Cable.a_connector_id
        } else {
          ConnectorID = Cable.b_connector_id
        }
        const ConnectorIndex = vm.CableConnectors.findIndex((item) => item.value == ConnectorID)
        return vm.CableConnectors[ConnectorIndex].name
      } else {
        return 'N/A'
      }

    },
    RemoteCableEndID() {

      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {
        const CableEndID = vm.CableEndID
        if(Cable.a_id == CableEndID) {
          return Cable.b_id
        } else {
          return Cable.a_id
        }
      } else {
        return 'N/A'
      }

    },
    RemoteConnector() {

      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {
        const CableEndID = vm.CableEndID
        let ConnectorID
        if(Cable.a_id == CableEndID) {
          ConnectorID = Cable.b_connector_id
        } else {
          ConnectorID = Cable.a_connector_id
        }
        const ConnectorIndex = vm.CableConnectors.findIndex((item) => item.value == ConnectorID)
        return vm.CableConnectors[ConnectorIndex].name
      } else {
        return 'N/A'
      }

    },
    CableMedia() {

      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {
        const MediaID = Cable.media_id
        const Media = vm.Media.find(element => element.value == MediaID)
        return Media.name
      } else {
        return 'N/A'
      }
    },
    CableLength() {
      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {
        const LenghtInCm = Cable.length
        const LengthUnit = vm.CableLengthUnit
        if(LengthUnit == 'ft.') {
          return vm.ConvertCmToFeet(LenghtInCm)+' '+LengthUnit
        } else {
          return vm.ConvertCmToMeters(LenghtInCm)+' '+LengthUnit
        }
      } else {
        return 'N/A'
      }
    },
    CableLengthUnit() {
      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {
        const MediaID = Cable.media_id
        const MediaIndex = vm.Media.findIndex((element) => element.value == MediaID )
        const MediaTypeID = vm.Media[MediaIndex]['type_id']
        const MediaTypeIndex = vm.MediaType.findIndex((element) => element.value == MediaTypeID)
        return vm.MediaType[MediaTypeIndex]['unit_of_length']
      } else {
        return 'N/A'
      }
    },
    SelectedDN: function() {

      const vm = this
      const SelectedDN = vm.GenerateSelectedPortDN(vm.PortSelectFunction)

      return SelectedDN
    },
    RemotePortDN() {

      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {

        const CableEndID = vm.CableEndID

        // Get remote cable end ID
        const RemoteCableEndID = (Cable.a_id == CableEndID) ? Cable.b_id : Cable.a_id
        const ConnectionIndex = vm.Connections.findIndex((entry) => entry.a_cable_id == RemoteCableEndID || entry.b_cable_id == RemoteCableEndID)

        if(ConnectionIndex != -1) {
          const Connection = vm.Connections[ConnectionIndex]
          const RemoteSide = (Connection.a_cable_id == RemoteCableEndID) ? 'a' : 'b'
          const ObjectID = Connection[RemoteSide+'_id']
          const Face = Connection[RemoteSide+'_face']
          const Partition = Connection[RemoteSide+'_partition']
          const PortID = Connection[RemoteSide+'_port']

          return vm.GenerateDN('port', ObjectID, Face, Partition, PortID)
        } else {
          return 'N/A'
        }

      } else {
        return 'N/A'
      }
    },
    RemoteCategoryColor() {

      const vm = this
      const Cable = vm.SelectedCable

      if(Cable !== null) {

        const CableEndID = vm.CableEndID

        // Get remote cable end ID
        const RemoteCableEndID = (Cable.a_id == CableEndID) ? Cable.b_id : Cable.a_id
        const ConnectionIndex = vm.Connections.findIndex((entry) => entry.a_cable_id == RemoteCableEndID || entry.b_cable_id == RemoteCableEndID)

        if(ConnectionIndex != -1) {
          const Connection = vm.Connections[ConnectionIndex]
          const RemoteSide = (Connection.a_cable_id == RemoteCableEndID) ? 'a' : 'b'
          const ObjectID = Connection[RemoteSide+'_id']

          return vm.GetObjectCategoryColor(ObjectID)
        } else {
          return 'N/A'
        }

      } else {
        return 'N/A'
      }
    },
    LocalCategoryColor() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.StateSelected[Context].object_id
      return vm.GetObjectCategoryColor(ObjectID)
    },
  },
  methods: {
    Cancel: function() {

      const vm = this

      // Close modal
      vm.$root.$emit('bv::hide::modal', vm.ModalID)
    },
    Submit: function() {

      const vm = this
      const Context = vm.Context

      vm.$refs.validation.validate().then((Valid) => {
        if(Valid) {
          const ObjectID = vm.StateSelected[Context].object_id
          const Face = vm.StateSelected[Context].object_face
          const Partition = vm.StateSelected[Context].partition[Face]
          const PortID = vm.StateSelected[Context].port_id[Face]
          const CableID = vm.CableEndID
          const data = {
            'id': ObjectID,
            'face': Face,
            'partition': Partition,
            'port_id': PortID,
            'cable_id': CableID,
          }
          vm.$http.post('/api/connections', data).then(response => {

            // Add connection to store
            response.data.add.forEach(add => vm.$store.commit('pcmConnections/ADD_Connection', {data:add}))
            response.data.remove.forEach(remove => vm.$store.commit('pcmConnections/REMOVE_Connection', {data:remove}))

          }).catch(error => {vm.DisplayError(error)})

          // Hide modal, this is necessary to close modal after submitting by click or enter
          vm.$bvModal.hide(vm.ModalID)
        }
      })
    },
  },
  mounted() {}
}
</script>
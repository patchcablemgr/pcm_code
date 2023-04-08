<template>
    
  <b-row>
    
    <b-form
      @submit.prevent="onSubmit"
      @reset="onReset"
      style="width:100%"
    >

      <div
        class="h5 font-weight-bolder m-0"
      >
        General:</div>
      <hr
        class="separator mt-0"
      >

      <!-- Name -->
      <dl class="row">
        <dt class="col-sm-4">
          Name
        </dt>
        <dd class="col-sm-8">
          <b-form-input
            name="name"
            v-model="TemplateName"
            placeholder="New_Template"
          />
        </dd>
      </dl>

      <!-- Category -->
      <dl class="row">
        <dt class="col-sm-3">
          Category
        </dt>
        <dd class="col-sm-1 my-auto">
          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-templates-category
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </dd>
        <dd class="col-sm-8">
          <b-form-select
            name="category"
            v-model="TemplateCategory"
            :options="GetCategoryOptions()"
          />
        </dd>
      </dl>

      <!-- Template Type -->
      <dl class="row">
        <dt class="col-sm-4">
          Template Type
        </dt>
        <dd class="col-sm-8">
          <b-form-radio-group
            name="type"
            v-model="TemplateType"
            :options="optionsTemplateType"
            stacked
          />
        </dd>
      </dl>

      <!-- RU Size -->
      <dl class="row">
        <dt class="col-sm-4">
          RU Size
        </dt>
        <dd class="col-sm-8">
          <b-form-input
            name="size"
            v-model.number="TemplateRUSize"
            ref="ElementTemplateRUSize"
            type="number"
            :min="GetRUSizeMin()"
            max=25
            :formatter="CastRUSizeToInteger"
            :disabled="TemplateType=='insert'"
          />
        </dd>
      </dl>

      <!-- Template Function -->
      <dl class="row">
        <dt class="col-sm-4">
          Template Function
        </dt>
        <dd class="col-sm-8">
          <b-form-radio-group
            name="function"
            v-model="TemplateFunction"
            :options="optionsTemplateFunction"
            stacked
            :disabled="TemplateType=='insert'"
          />
        </dd>
      </dl>

      <!-- Mounting Configuration -->
      <dl class="row">
        <dt class="col-sm-4">
          Mounting Configuration
        </dt>
        <dd class="col-sm-8">
          <b-form-radio-group
              name="mountConfig"
              v-model="TemplateMountConfig"
              :options="optionsTemplateMountConfig"
              stacked
              :disabled="TemplateType=='insert'"
            />
        </dd>
      </dl>

<!--
  ### Partition ###
-->
      <div class="h5 font-weight-bolder m-0">Partition:</div>
      <hr class="separator mt-0">

      <!-- Partition Type -->
      <dl class="row">
        <dt class="col-sm-4">
          Partition Type
        </dt>
        <dd class="col-sm-8">
          <b-form-radio-group
            name="partitionType"
            v-model="PartitionType"
            :options="optionsTemplatePartitionType"
            stacked
            :disabled="PartitionTypeDisabled"
          />
        </dd>
      </dl>

      <!-- Add/remove Partition -->
      <dl class="row">
        <dt class="col-sm-4">
          Add/Remove Partition
        </dt>
        <dd class="col-sm-8">
          <b-button
            title="Insert inside selected partition"
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="outline-primary"
            class="btn-icon"
            @click="PartitionAdd('inside')"
            :disabled="AddChildPartitionDisabled"
          >
            <feather-icon icon="PlusSquareIcon" />
          </b-button>
          <b-button
            title="Insert after selected partition"
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="outline-primary"
            class="btn-icon"
            @click="PartitionAdd('after')"
            :disabled="AddSiblingPartitionDisabled"
          >
            <feather-icon
              icon="MoreVerticalIcon"
              v-show="ComputedSelectedPartitionDirection == 'col'"
            />
            <feather-icon
              icon="MoreHorizontalIcon"
              v-show="ComputedSelectedPartitionDirection == 'row'"
            />
            <br
              v-show="ComputedSelectedPartitionDirection == 'col'"
            >
            <feather-icon icon="PlusIcon" />
          </b-button>
          <b-button
            title="Insert before selected partition"
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="outline-primary"
            class="btn-icon"
            @click="PartitionAdd('before')"
            :disabled="AddSiblingPartitionDisabled"
          >
            <feather-icon icon="PlusIcon" />
            <br
              v-show="ComputedSelectedPartitionDirection == 'col'"
            >
            <feather-icon
              icon="MoreVerticalIcon"
              v-show="ComputedSelectedPartitionDirection == 'col'"
            />
            <feather-icon
              icon="MoreHorizontalIcon"
              v-show="ComputedSelectedPartitionDirection == 'row'"
            />
          </b-button>
          <b-button
            title="Delete selected partition"
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="outline-primary"
            class="btn-icon"
            @click="PartitionDelete()"
            :disabled="RemovePartitionDisabled"
          >
            <feather-icon icon="MinusSquareIcon" />
          </b-button>
        </dd>
      </dl>

      <!-- Partition Size -->
      <dl class="row">
        <dt class="col-sm-4">
          Partition Size
        </dt>
        <dd class="col-sm-8">
          <b-form-input
            name="partitionSize"
            v-model.number="PartitionSize"
            type="number"
            :min="GetPartitionSelectedSizeMin()"
            :max="GetPartitionSelectedSizeMax()"
            :formatter="CastPartitionSizeToInteger"
          />
        </dd>
      </dl>

<!--
  ### Connectable ###
-->

      <div
        class="h5 font-weight-bolder m-0"
        v-show="PartitionType == 'connectable'"
      >
        Connectable:</div>
      <hr
        class="separator mt-0"
        v-show="PartitionType == 'connectable'"
      >

      <!-- Port ID -->
      <dl
        class="row"
        v-show="PartitionType == 'connectable'"
      >
        <dt class="col-sm-3">
          Port ID
        </dt>
        <dd class="col-sm-1 my-auto">
          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-edit-template-port-id-form
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </dd>
        <dd class="col-sm-8 my-auto">
          {{PortPreview}}
        </dd>
      </dl>

      <!-- Port Layout -->
      <dl
        class="row"
        v-show="PartitionType == 'connectable'"
      >
        <dt class="col-sm-4">
          Port Layout
        </dt>
        <dd class="col-sm-8">
          <b-container>
            <b-row>
              <b-col>
                Col:
              </b-col>
              <b-col>
                <b-form-input
                  name="portLayoutCol"
                  :disabled="PartitionType != 'connectable'"
                  v-model="PortLayoutCols"
                  type="number"
                  min=1
                />
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                Row:
              </b-col>
              <b-col>
                <b-form-input
                  name="portLayoutRow"
                  :disabled="PartitionType != 'connectable'"
                  v-model="PortLayoutRows"
                  type="number"
                  min=1
                />
              </b-col>
            </b-row>
          </b-container>
        </dd>
      </dl>

      <!-- Media -->
      <dl
        v-if="TemplateFunction == 'passive'"
        class="row"
        v-show="PartitionType == 'connectable'"
      >
        <dt class="col-sm-4">
          Media
        </dt>
        <dd class="col-sm-8">
          <b-form-select
            name="media"
            v-model="PortMedia"
            :options="GetMediaOptions()"
          />
        </dd>
      </dl>

      <!-- Port Connector -->
      <dl
        class="row"
        v-show="PartitionType == 'connectable'"
      >
        <dt class="col-sm-4">
          Port Type
        </dt>
        <dd class="col-sm-8">
          <b-form-select
            name="portType"
            v-model="PortConnector"
            :options="GetPortConnectorOptions()"
          />
        </dd>
      </dl>

      <!-- Port Orientation -->
      <dl
        class="row"
        v-show="PartitionType == 'connectable'"
      >
        <dt class="col-sm-4">
          Port Orientation
        </dt>
        <dd class="col-sm-8">
          <b-form-select
            name="portOrientation"
            v-model="PortOrientation"
            :options="GetPortOrientationOptions()"
          />
        </dd>
      </dl>

<!--
  ### Enclosure ###
-->

      <div
        class="h5 font-weight-bolder m-0"
        v-show="PartitionType == 'enclosure'"
      >
        Enclosure:</div>
      <hr
        class="separator mt-0"
        v-show="PartitionType == 'enclosure'"
      >

      <!-- Enclosure Layout -->
      <dl
        class="row"
        v-show="PartitionType == 'enclosure'"
      >
        <dt class="col-sm-4">
          Enclosure Layout
        </dt>
        <dd class="col-sm-8">
          <b-container>
            <b-row>
              <b-col>
                Col:
              </b-col>
              <b-col>
                <b-form-input
                  name="encLayoutCol"
                  :disabled="PartitionType != 'enclosure'"
                  v-model="EncLayoutCols"
                  type="number"
                  min=1
                />
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                Row:
              </b-col>
              <b-col>
                <b-form-input
                  name="encLayoutRow"
                  :disabled="PartitionType != 'enclosure'"
                  v-model="EncLayoutRows"
                  type="number"
                  min=1
                />
              </b-col>
            </b-row>
          </b-container>
        </dd>
      </dl>

      <!-- Submit and Reset -->
      <div offset-md="4">
        <b-button
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            type="submit"
            variant="primary"
            class="mr-1"
        >
            Submit
        </b-button>
        <b-button
            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
            type="reset"
            variant="outline-secondary"
        >
            Reset
        </b-button>
      </div>
    </b-form>

    <!-- Category Modal -->
    <modal-templates-category/>

    <!-- Port ID Modal -->
    <modal-edit-template-port-id
      ModalID="modal-edit-template-port-id-form"
      v-if="PartitionType == 'connectable'"
      Context="workspace"
      :TemplateFaceSelected="TemplateFaceSelected"
    />

  </b-row>
</template>

<script>
import { BContainer, BRow, BCol, BForm, BFormGroup, BFormInput, BFormSelect, BFormRadioGroup, BButton, VBModal, } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import ModalTemplatesCategory from './ModalTemplatesCategory.vue'
import ModalEditTemplatePortId from './ModalEditTemplatePortId.vue'
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    BForm,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BFormRadioGroup,
    BButton,
    VBModal,

    ModalTemplatesCategory,
    ModalEditTemplatePortId,
  },
  directives: {
    Ripple,
    'b-modal': VBModal,
  },
  props: {
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionTypeDisabled: {type: Boolean},
  },
  data() {
    return {
      selected: null,
      optionsCategory: [],
      optionsTemplateType: [
        { text: 'Standard', value: 'standard' },
        { text: 'Insert', value: 'insert' },
      ],
      optionsTemplateFunction: [
        { text: 'Endpoint', value: 'endpoint' },
        { text: 'Passive', value: 'passive' },
      ],
      optionsTemplateMountConfig: [
        { text: '2-Post', value: '2-post' },
        { text: '4-Post', value: '4-post' },
      ],
      optionsTemplatePartitionType: [
        { text: 'Generic', value: 'generic' },
        { text: 'Connectable', value: 'connectable' },
        { text: 'Enclosure', value: 'enclosure' },
      ],
    }
  },
  computed: {
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
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    ComputedSelectedPartitionDirection: {
      get() {
        // Store variables
        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]

        return vm.GetPartitionDirection(PartitionAddress)
      }
    },
    TemplateName: {
      get() {

        const Context = this.Context
        const Template = this.GetTemplateSelected(Context)
        const ReturnData = (Template) ? Template.name : ''

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))

        Template.name = newValue
        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'TemplateName'})
      }
    },
    TemplateCategory: {
      get() {

        const Context = this.Context
        const Template = this.GetTemplateSelected(Context)
        const ReturnData = (Template) ? Template.category_id : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))

        Template.category_id = newValue
        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'TemplateCategory'})
      }
    },
    TemplateType: {
      get() {

        const Context = this.Context
        const Template = this.GetTemplateSelected(Context)
        const ReturnData = (Template) ? Template.type : ''

        return ReturnData
      },
      set(newValue) {
        
        const vm = this
        const Context = vm.Context
        const WorkspaceStandardID = vm.$store.state.pcmProps.WorkspaceStandardID
        const WorkspaceInsertID = vm.$store.state.pcmProps.WorkspaceInsertID
			
        // Set active preview template index
        const ActiveWorkspaceID = (newValue == 'insert') ? WorkspaceInsertID : WorkspaceStandardID
        
        // Update StateSelected
        const UpdateData = {
          object_id: ActiveWorkspaceID,
          object_face: 'front',
          partition: {
            front: [0],
            rear: [0]
          },
          location_id: ActiveWorkspaceID,
          location_face: 'front'
        }
        vm.$store.commit('pcmState/UPDATE_Selected', {pcmContext:Context, data:UpdateData})

      }
    },
    TemplateRUSize: {
      get() {

        const Context = this.Context
        const Template = this.GetTemplateSelected(Context)
        const ReturnData = (Template) ? Template.ru_size : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))

        Template.ru_size = newValue
        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'TemplateRUSize'})
      }
    },
    TemplateFunction: {
      get() {

        const Context = this.Context
        const Template = this.GetTemplateSelected(Context)
        const ReturnData = (Template) ? Template.function : ''

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))

        Template.function = newValue
        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'TemplateFunction'})
      }
    },
    TemplateMountConfig: {
      get() {

        const Context = this.Context
        const Template = this.GetTemplateSelected(Context)
        const ReturnData = (Template) ? Template.mount_config : ''

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))

        Template.mount_config = newValue
        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'TemplateMountConfig'})

        // Set TemplateFaceSelected
        if(newValue == '2-post') {
          const Face = 'front'
          vm.$emit('SetTemplateFaceSelected', {Context, Face})
        }
      }
    },
    PartitionType: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition) ? Partition.type : ''

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const TemplateFace = vm.TemplateFaceSelected[Context]
        const TemplatePartitionAddress = vm.StateSelected[Context].partition[TemplateFace]

        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const Blueprint = Template.blueprint[TemplateFace]
        const Partition = vm.GetPartition(Blueprint, TemplatePartitionAddress)

        Partition.type = newValue

        if(newValue == 'connectable') {

          // Default port data unless template cloned
          if(!Template.hasOwnProperty('clone')) {
            Partition.port_format = vm.$store.state.pcmTemplates.DefaultPortFormat
            Partition.port_layout = vm.$store.state.pcmTemplates.DefaultPortLayout
          }

          // Port media type
          const defaultMediaIndex = vm.$store.state.pcmProps.Media.findIndex((media) => media.default)
          const defaultMediaValue = vm.$store.state.pcmProps.Media[defaultMediaIndex].value
          Partition.media = defaultMediaValue

          // Port media type
          const defaultPortConnectorIndex = vm.$store.state.pcmProps.Connectors.findIndex((connector) => connector.default)
          const defaultPortConnectorValue = vm.$store.state.pcmProps.Connectors[defaultPortConnectorIndex].value
          Partition.port_connector = defaultPortConnectorValue

          // Port media type
          const defaultPortOrientationIndex = vm.$store.state.pcmProps.Orientations.findIndex((orientation) => orientation.default)
          const defaultPortOrientationValue = vm.$store.state.pcmProps.Orientations[defaultPortOrientationIndex].value
          Partition.port_orientation = defaultPortOrientationValue

        } else if(newValue == 'enclosure') {
          Partition.enc_layout = vm.$store.state.pcmTemplates.DefaultEncLayout
        }

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'PartitionType'})

      }
    },
    PartitionSize: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition) ? Partition.units : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        Partition.units = newValue

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'PartitionSize'})
      }
    },
    PortLayoutCols: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition && this.PartitionType == 'connectable') ? Partition.port_layout.cols : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        Partition.port_layout.cols = parseInt(newValue)

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'PortLayoutCols'})

      }
    },
    PortLayoutRows: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition && this.PartitionType == 'connectable') ? Partition.port_layout.rows : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        Partition.port_layout.rows = parseInt(newValue)

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'PortLayoutRows'})

      }
    },
    PortMedia: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition && this.PartitionType == 'connectable') ? Partition.media : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const Template = vm.GetTemplateSelected(Context)
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        Partition.media = newValue

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'PortMedia'})

      }
    },
    PortConnector: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition && this.PartitionType == 'connectable') ? Partition.port_connector : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        Partition.port_connector = newValue

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'PortConnector'})

      }
    },
    PortOrientation: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition && this.PartitionType == 'connectable') ? Partition.port_orientation : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        Partition.port_orientation = newValue

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'PortOrientation'})

      }
    },
    EncLayoutCols: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition && this.PartitionType == 'enclosure') ? Partition.enc_layout.cols : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        Partition.enc_layout.cols = parseInt(newValue)

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'EncLayoutCols'})

      }
    },
    EncLayoutRows: {
      get() {

        const Context = this.Context
        const Partition = this.GetPartitionSelected(Context)
        const ReturnData = (Partition && this.PartitionType == 'enclosure') ? Partition.enc_layout.rows : 0

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        Partition.enc_layout.rows = parseInt(newValue)

        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template, src:'EncLayoutRows'})

      }
    },
    AddChildPartitionDisabled: function() {
      // Store variables
      const vm = this
      const Context = vm.Context
      const Face = vm.TemplateFaceSelected[Context]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]

      // Get Partition
      const Partition = vm.GetPartitionSelected(Context)

      return (!vm.GetPartitionUnitsAvailable(PartitionAddress) || Partition.type != 'generic')
    },
    AddSiblingPartitionDisabled: function() {
      // Store variables
      const vm = this
      const Context = vm.Context
      const Face = vm.TemplateFaceSelected[Context]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]
      const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)

      return !vm.GetPartitionUnitsAvailable(PartitionParentAddress)

    },
    RemovePartitionDisabled: function() {
      
      // Store variables
      const vm = this
      const Context = vm.Context
      const Face = vm.TemplateFaceSelected[Context]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]
      let RemovePartitionDisabled = false

      if(PartitionAddress.length == 1) {

        const Template = vm.GetTemplateSelected(Context)
        const Layer1Partitions = Template.blueprint[Face]

        if(Layer1Partitions.length == 1) {
          RemovePartitionDisabled = true
        }
      }

      return RemovePartitionDisabled
    },
    PortPreview: function() {

      const vm = this
      const Context = vm.Context
      return vm.GeneratePortPreview(Context)
    },
  },
  methods: {
    PartitionAdd: function(Position) {

      const vm = this
      const Context = vm.Context
      const Face = vm.TemplateFaceSelected[Context]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]
      const GenericPartition = vm.$store.state.pcmProps.GenericBlueprintGeneric
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
      const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
      const Blueprint = Template.blueprint[Face]

      if(Position == 'inside') {
        
        if(vm.GetPartitionUnitsAvailable(PartitionAddress)) {

          const Partition = vm.GetPartition(Blueprint, PartitionAddress)
          Partition.children.push(GenericPartition)
          
        }
      } else {

        const PartitionIndex = PartitionAddress[PartitionAddress.length - 1]
        const ParentPartitionAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
        let ParentTemplate = vm.GetPartition(Blueprint, ParentPartitionAddress)

        if(vm.GetPartitionUnitsAvailable(ParentPartitionAddress)) {

          const ParentTemplateChildren = ParentTemplate.children

          if(Position == 'after') {

            // Insert new partition after selected partition
            ParentTemplateChildren.splice(PartitionIndex + 1, 0, GenericPartition)

          } else if (Position == 'before') {

            // Insert new partition before selected partition
            ParentTemplateChildren.splice(PartitionIndex, 0, GenericPartition)

            // Shift selected partition
            PartitionAddress[PartitionAddress.length - 1] = PartitionIndex + 1

            // Update StateSelected
            const UpdateData = {
              partition: {
                front: (Face == 'front') ? PartitionAddress : vm.StateSelected[Context].partition.front,
                rear: (Face == 'rear') ? PartitionAddress : vm.StateSelected[Context].partition.rear
              }
            }
            vm.$store.commit('pcmState/UPDATE_Selected', {pcmContext:Context, data:UpdateData})

          }
        }
      }

      // Update template
      vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})
    },
    PartitionDelete: function() {
      
      // Store variables
      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
      const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
      const Face = vm.TemplateFaceSelected[Context]
      const ParentPartitionAddress = JSON.parse(JSON.stringify(vm.StateSelected[Context].partition[Face]))
      const PartitionIndex = ParentPartitionAddress.pop()
      let ParentPartitionSet = Template.blueprint[Face]

      // Delete partition
      ParentPartitionAddress.forEach(function(PartitionIndex, Index){
        ParentPartitionSet = ParentPartitionSet[PartitionIndex].children
      })
      ParentPartitionSet.splice(PartitionIndex, 1)

      // Adjust selected partition
      let PartitionAddress = JSON.parse(JSON.stringify(vm.StateSelected[Context].partition[Face]))
      if(ParentPartitionSet.length) {
        if(PartitionIndex == 0) {
          PartitionAddress[PartitionAddress.length - 1] = 0
        } else {
          PartitionAddress[PartitionAddress.length - 1] = PartitionIndex - 1
        }
      } else {
        PartitionAddress = ParentPartitionAddress
      }

      // Update StateSelected
      const UpdateData = {
        partition: {
          front: (Face == 'front') ? PartitionAddress : vm.StateSelected[Context].partition.front,
          rear: (Face == 'rear') ? PartitionAddress : vm.StateSelected[Context].partition.rear
        }
      }
      vm.$store.commit('pcmState/UPDATE_Selected', {pcmContext:Context, data:UpdateData})

      // Update template
      vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})

    },
    GetPartitionUnitsAvailable: function(PartitionAddress) {

      // Store variables
      const vm = this
      const Context = vm.Context
      let UnitsAvailable = 0
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)

      if(TemplateIndex !== -1) {
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))

        // Get Partition
        const Face = vm.TemplateFaceSelected[Context]
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)

        UnitsAvailable = vm.GetGlobalPartitionMax(Template, PartitionAddress)
        let PartitionChildren

        if(PartitionAddress.length > 1) {
          const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
          const PartitionParent = vm.GetPartition(Blueprint, PartitionParentAddress)
          UnitsAvailable = PartitionParent.units
        }

        PartitionChildren = Partition.children

        PartitionChildren.forEach(function(PartitionChild) {
          UnitsAvailable = UnitsAvailable - PartitionChild.units
        })
      }

      return UnitsAvailable
    },
    GetGlobalPartitionMax: function(Template, PartitionAddress) {

      const vm = this
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)

      // Get working max
      let WorkingMax
      if (PartitionDirection == 'col') {

        if (Template.insert_constraints !== null) {

          // Partition is an insert with constraints
          const LastInsertConstraintIndex = Template.insert_constraints.length - 1
          WorkingMax = Template.insert_constraints[LastInsertConstraintIndex].part_layout.width
          //WorkingMax = Template.insert_constraints.part_layout.width
        } else {

          // Partition is standard
          WorkingMax = 24
        }
      } else {

        if (Template.insert_constraints !== null) {

          // Partition is an insert with constraints
          const LastInsertConstraintIndex = Template.insert_constraints.length - 1
          WorkingMax = Template.insert_constraints[LastInsertConstraintIndex].part_layout.height
          //WorkingMax = Template.insert_constraints.part_layout.height
        } else {

          // Partition is standard
          WorkingMax = Template.ru_size * 2
        }
      }

      return WorkingMax

    },
    onSubmit: function() {

      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
      const Template = vm.Templates[Context][TemplateIndex]
      const url = '/api/templates'

      // POST category form data
      this.$http.post(url, Template).then(function(response){
        
        const Template = response.data
        vm.GeneratePseudoData('template', Template)
				
        // Append new template to template array
        vm.$store.commit('pcmTemplates/ADD_Template', {pcmContext:'template', data:Template})

      }).catch(error => {vm.DisplayError(error)})
    },
    onReset: function() {
      console.log("Debug (Reset): ")
    },
    GetRUSizeMin() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.StateSelected[Context].object_id
      const TemplateID = vm.GetTemplateID(ObjectID, Context)
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
      const PreviewData = vm.Templates[Context][TemplateIndex]
      const TemplateFaceArray = ['front','rear']
      let RUSizeMin = 1

      // Find the largest vertically growing partition

      // Look at both front and rear of template
      TemplateFaceArray.forEach(function(TemplateFace){
        const Layer1Partitions = PreviewData.blueprint[TemplateFace]

        // Look at each horizontally growing layer 1 partition
        Layer1Partitions.forEach(function(Layer1Partition){
          let TotalPartitionUnits = 0
          const Layer2Partitions = Layer1Partition.children

          // Look at each vertically growing layer 2 partition
          Layer2Partitions.forEach(function(Layer2Partition){
            TotalPartitionUnits = TotalPartitionUnits + parseInt(Layer2Partition.units)
          })

          // Store largest partition size
          const WorkingRUSizeMin = Math.ceil(TotalPartitionUnits / 2)
          RUSizeMin = (WorkingRUSizeMin > RUSizeMin) ? WorkingRUSizeMin : RUSizeMin
        })
      })

      return RUSizeMin
    },
    GetPartitionSelectedSizeMin: function(){

      const vm = this
      const Context = vm.Context
      let PartitionSizeMin = 0
      const Partition = vm.GetPartitionSelected(Context)
      
      if(Partition) {
        PartitionSizeMin = vm.GetPartitionSelectedSizeMinRecursion(Partition.children)
      }

      return (PartitionSizeMin > 0) ? PartitionSizeMin : 1
    },
    GetPartitionSelectedSizeMinRecursion: function(PartitionArray, SelectedPartitionSizeMin = 0, RelativeDepth = 0) {

      // Store variables
      const vm = this

      PartitionArray.forEach(function(Partition){
        SelectedPartitionSizeMin = (RelativeDepth % 2) ? SelectedPartitionSizeMin + parseInt(Partition.units) : SelectedPartitionSizeMin
        SelectedPartitionSizeMin = vm.GetPartitionSelectedSizeMinRecursion(Partition.children, SelectedPartitionSizeMin, RelativeDepth + 1)
      })

      return SelectedPartitionSizeMin;
    },
    GetPartitionSelectedSizeMax: function() {

      // Store variables
      const vm = this
      const Context = vm.Context
      const Template = vm.GetTemplateSelected(Context)
      const Face = vm.TemplateFaceSelected[Context]
      let PartitionSizeMax = 0
      
      
      if(Template) {
        const Blueprint = Template.blueprint[Face]
        const TemplateType = Template.type
        
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const ParentPartitionAddress = JSON.parse(JSON.stringify(PartitionAddress))
        const PartitionIndex = ParentPartitionAddress.pop()
        const ParentPartition = vm.GetPartition(Blueprint, ParentPartitionAddress)

        // Determine starting partition size max
        if(ParentPartitionAddress.length == 0) {

          // Parent partition is root partition.  Start with max template width
          PartitionSizeMax = (TemplateType == 'insert') ? Template.insert_constraints[Template.insert_constraints.length-1].part_layout.width : 24
        } else if (ParentPartitionAddress.length == 1) {

          // Parent partition is first level partition.  Start with RU size
          PartitionSizeMax = (TemplateType == 'insert') ? Template.insert_constraints[Template.insert_constraints.length-1].part_layout.height : Template.ru_size * 2
        } else {

          // Parent partition is deply nested.  Start with grand parent partition size
          const GrandParentPartitionAddress = JSON.parse(JSON.stringify(ParentPartitionAddress))
          GrandParentPartitionAddress.pop()
          const GrandParentPartition = vm.GetPartition(Blueprint, GrandParentPartitionAddress)
          PartitionSizeMax = GrandParentPartition.units
        }

        // Subtract child partitions
        const ParentPartitionChildren = ParentPartition.children
        ParentPartitionChildren.forEach(function(ChildPartition, Index){
          if(Index != PartitionIndex) {
            let ChildPartitionSize = ChildPartition.units
            PartitionSizeMax = PartitionSizeMax - ChildPartitionSize
          }
        })
      }

      return PartitionSizeMax
    },
    GetCategoryOptions: function() {

      // Store variables
      const vm = this
      const Context = vm.Context
      let WorkingArray = []

      // Populate working array with data to be used as select options
      for(let i = 0; i < vm.Categories[Context].length; i++) {

        let Category = vm.Categories[Context][i]
        WorkingArray.push({'value': Category.id, 'text': Category.name})
      }

      return WorkingArray
    },
    GetMediaOptions: function() {

      // Store variables
      const vm = this
      let WorkingArray = []

      // Populate working array with data to be used as select options
      for(let i = 0; i < vm.Media.length; i++) {

        let Media = vm.Media[i]
        if(Media.display) {
          WorkingArray.push({'value': Media.value, 'text': Media.name})
        }
      }

      return WorkingArray
    },
    GetPortConnectorOptions: function() {

      // Store variables
      const vm = this
      let WorkingArray = []

      // Populate working array with data to be used as select options
      for(let i = 0; i < vm.Connectors.length; i++) {

        let PortConnector = vm.Connectors[i]
        WorkingArray.push({'value': PortConnector.value, 'text': PortConnector.name})
      }

      return WorkingArray
    },
    GetPortOrientationOptions: function() {

      // Store variables
      const vm = this
      let WorkingArray = []

      // Populate working array with data to be used as select options
      for(let i = 0; i < vm.Orientations.length; i++) {

        let PortOrientation = vm.Orientations[i]
        WorkingArray.push({'value': PortOrientation.value, 'text': PortOrientation.name})
      }

      return WorkingArray
    },
    CastPartitionSizeToInteger: function(value) {

      // Store variables
      const vm = this
      const Element = vm.$refs.ElementTemplateRUSize.$el
      const min = vm.GetPartitionSelectedSizeMin()
      const max = vm.GetPartitionSelectedSizeMax()

      // Convert value from String to Integer
      let formattedValue = parseInt(value)

      // Restrict input to min/max
      if(formattedValue < min) {
        formattedValue = min
      }
      if(formattedValue > max) {
        formattedValue = max
      }

      // Return input as integer
      return formattedValue
    },
    CastRUSizeToInteger: function(value) {

      // Store variables
      const vm = this
      const min = vm.GetRUSizeMin()
      const max = 25

      // Convert value from String to Integer
      let formattedValue = parseInt(value)

      // Restrict input to min/max
      if(formattedValue < min) {
        formattedValue = min
      }
      if(formattedValue > max) {
        formattedValue = max
      }

      // Return input as integer
      return formattedValue
    },
  },
}
</script>

<style>

</style>

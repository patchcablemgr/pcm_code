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
            v-model="TemplateData[0].name"
            @change="$emit('TemplateNameUpdated', $event)"
            id="h-name"
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
            v-model="TemplateData[0].category_id"
            :options="GetCategoryOptions()"
            @change="$emit('TemplateCategoryUpdated', $event)"
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
            v-model="inputTemplateType"
            :options="optionsTemplateType"
            @change="$emit('TemplateTypeUpdated', $event)"
            name="radios-template-type"
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
            v-model.number="InputTemplateRUSize"
            @change="$emit('TemplateRUSizeUpdated', $event)"
            id="h-ru-size"
            ref="ElementTemplateRUSize"
            type="number"
            :min="GetRUSizeMin()"
            max=25
            :formatter="CastRUSizeToInteger"
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
          v-model="inputTemplateFunction"
          :options="optionsTemplateFunction"
          name="radios-template-function"
          stacked
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
              v-model="inputTemplateMountConfig"
              :options="optionsTemplateMountConfig"
              @change="$emit('TemplateMountConfigUpdated', $event)"
              name="radios-mounting-configuration"
              stacked
            />
        </dd>
      </dl>

      <div class="h5 font-weight-bolder m-0">Partition:</div>
      <hr class="separator mt-0">

      <!-- Partition Type -->
      <dl class="row">
        <dt class="col-sm-4">
          Partition Type
        </dt>
        <dd class="col-sm-8">
          <b-form-radio-group
            v-model="ComputedInputTemplatePartitionType"
            :options="optionsTemplatePartitionType"
            name="radios-partition-type"
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
            @click="$emit('TemplatePartitionAdd', 'inside')"
            :disabled="AddChildPartitionDisabled"
          >
            <feather-icon icon="PlusSquareIcon" />
          </b-button>
          <b-button
            title="Insert after selected partition"
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="outline-primary"
            class="btn-icon"
            @click="$emit('TemplatePartitionAdd', 'after')"
            :disabled="AddSiblingPartitionDisabled"
          >
            <feather-icon
              icon="MoreVerticalIcon"
              v-show="ComputedSelectedPartitionDirection == 'row'"
            />
            <feather-icon
              icon="MoreHorizontalIcon"
              v-show="ComputedSelectedPartitionDirection == 'column'"
            />
            <br
              v-show="ComputedSelectedPartitionDirection == 'row'"
            >
            <feather-icon icon="PlusIcon" />
          </b-button>
          <b-button
            title="Insert before selected partition"
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="outline-primary"
            class="btn-icon"
            @click="$emit('TemplatePartitionAdd', 'before')"
            :disabled="AddSiblingPartitionDisabled"
          >
            <feather-icon icon="PlusIcon" />
            <br
              v-show="ComputedSelectedPartitionDirection == 'row'"
            >
            <feather-icon
              icon="MoreVerticalIcon"
              v-show="ComputedSelectedPartitionDirection == 'row'"
            />
            <feather-icon
              icon="MoreHorizontalIcon"
              v-show="ComputedSelectedPartitionDirection == 'column'"
            />
          </b-button>
          <b-button
            title="Delete selected partition"
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="outline-primary"
            class="btn-icon"
            @click="$emit('TemplatePartitionRemove')"
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
          id="h-partition-size"
          v-model="ComputedInputTemplatePartitionSize"
          type="number"
          :min="GetSelectedPartitionSizeMin()"
          :max="GetSelectedPartitionSizeMax()"
          :formatter="CastPartitionSizeToInteger"
          />
        </dd>
      </dl>

<!--
  ### Connectable ###
-->

      <div
        class="h5 font-weight-bolder m-0"
        v-show="ComputedInputTemplatePartitionType == 'connectable'"
      >
        Connectable:</div>
      <hr
        class="separator mt-0"
        v-show="ComputedInputTemplatePartitionType == 'connectable'"
      >

      <!-- Port ID -->
      <dl
        class="row"
        v-show="ComputedInputTemplatePartitionType == 'connectable'"
      >
        <dt class="col-sm-3">
          Port ID
        </dt>
        <dd class="col-sm-1 my-auto">
          <b-button
            v-ripple.400="'rgba(40, 199, 111, 0.15)'"
            variant="flat-success"
            class="btn-icon"
            v-b-modal.modal-templates-port-id
          >
            <feather-icon icon="EditIcon" />
          </b-button>
        </dd>
        <dd class="col-sm-8 my-auto">
          Port1...
        </dd>
      </dl>

      <!-- Port Layout -->
      <dl
        class="row"
        v-show="ComputedInputTemplatePartitionType == 'connectable'"
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
                  id="h-port-layout-column"
                  v-model="ComputedInputTemplatePortLayoutCols"
                  type="number"
                />
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                Row:
              </b-col>
              <b-col>
                <b-form-input
                  id="h-port-layout-row"
                  v-model="ComputedInputTemplatePortLayoutRows"
                  type="number"
                />
              </b-col>
            </b-row>
          </b-container>
        </dd>
      </dl>

      <!-- Media -->
      <dl
        class="row"
        v-show="ComputedInputTemplatePartitionType == 'connectable'"
      >
        <dt class="col-sm-4">
          Medium
        </dt>
        <dd class="col-sm-8">
          <b-form-select
            v-model="ComputedInputTemplateMedia"
            :options="GetMediaOptions()"
          />
        </dd>
      </dl>

      <!-- Port Connector -->
      <dl
        class="row"
        v-show="ComputedInputTemplatePartitionType == 'connectable'"
      >
        <dt class="col-sm-4">
          Port Type
        </dt>
        <dd class="col-sm-8">
          <b-form-select
            v-model="ComputedInputTemplatePortConnector"
            :options="GetPortConnectorOptions()"
          />
        </dd>
      </dl>

      <!-- Port Orientation -->
      <dl
        class="row"
        v-show="ComputedInputTemplatePartitionType == 'connectable'"
      >
        <dt class="col-sm-4">
          Port Orientation
        </dt>
        <dd class="col-sm-8">
          <b-form-select
            v-model="ComputedInputTemplatePortOrientation"
            :options="GetPortOrientationOptions()"
          />
        </dd>
      </dl>

<!--
  ### Enclosure ###
-->

      <div
        class="h5 font-weight-bolder m-0"
        v-show="ComputedInputTemplatePartitionType == 'enclosure'"
      >
        Enclosure:</div>
      <hr
        class="separator mt-0"
        v-show="ComputedInputTemplatePartitionType == 'enclosure'"
      >

      <!-- Enclosure Layout -->
      <dl
        class="row"
        v-show="ComputedInputTemplatePartitionType == 'enclosure'"
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
                  id="h-enc-layout-column"
                  v-model="ComputedInputTemplateEncLayoutCols"
                  type="number"
                />
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                Row:
              </b-col>
              <b-col>
                <b-form-input
                  id="h-enc-layout-row"
                  v-model="ComputedInputTemplateEncLayoutRows"
                  type="number"
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
    <modal-templates-category
      :CategoryData="CategoryData"
      @TemplateCategoriesUpdated="$emit('TemplateCategoriesUpdated')"
      v-on:categoriesUpdatedOrig="updateCategories($event)"
      v-on:categoriesSetDefault="setDefaultCategory($event)"
    />

    <!-- Port ID Modal -->
    <modal-templates-port-id/>

  </b-row>
</template>

<script>
import { BContainer, BRow, BCol, BForm, BFormGroup, BFormInput, BFormSelect, BFormRadioGroup, BButton, VBModal, } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import ModalTemplatesCategory from './ModalTemplatesCategory.vue'
import ModalTemplatesPortId from './ModalTemplatesPortId.vue'

export default {
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
    ModalTemplatesPortId,
  },
  directives: {
    Ripple,
    'b-modal': VBModal,
  },
  props: {
    CategoryData: {type: Array},
    PortConnectorData: {type: Array},
    PortOrientationData: {type: Array},
    MediaData: {type: Array},
    TemplateData: {type: Array},
    CabinetFace: {type: String},
    SelectedPartitionAddress: {type: Object},
    AddChildPartitionDisabled: {type: Boolean},
    AddSiblingPartitionDisabled: {type: Boolean},
    RemovePartitionDisabled: {type: Boolean},
    PartitionTypeDisabled: {type: Boolean},
  },
  data() {
    return {
      inputTemplateType: this.TemplateData[0].type,
      InputTemplateRUSize: this.TemplateData[0].ru_size,
      inputTemplateFunction: this.TemplateData[0].function,
      inputTemplateMountConfig: this.TemplateData[0].mount_config,
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
    ComputedSelectedPartitionDirection: {
      get() {
        // Store variables
        const vm = this
        const CabinetFace = vm.CabinetFace
        const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
        const SelectedPartitionDepth = SelectedPartitionAddress.length
        const SelectedPartitionDirection = (SelectedPartitionDepth % 2) ? 'column' : 'row'

        return SelectedPartitionDirection
      }
    },
    ComputedInputTemplatePartitionType: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        
        // Return selected partition type
        return SelectedPartition.type
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionTypeUpdated', newValue)

      }
    },
    ComputedInputTemplatePartitionSize: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        
        // Return selected partition type
        return SelectedPartition.units
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionSizeUpdated', newValue)

      }
    },
    ComputedInputTemplatePortLayoutCols: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        const PortLayoutCols = (SelectedPartition.type == 'connectable') ? SelectedPartition.port_layout.cols : 0
        
        // Return selected partition type
        return PortLayoutCols
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionPortLayoutColsUpdated', newValue)

      }
    },
    ComputedInputTemplatePortLayoutRows: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        const PortLayoutCols = (SelectedPartition.type == 'connectable') ? SelectedPartition.port_layout.rows : 0
        
        // Return selected partition type
        return PortLayoutCols
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionPortLayoutRowsUpdated', newValue)

      }
    },
    ComputedInputTemplateMedia: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        const DefaultMediaIndex = vm.MediaData.findIndex((media) => media.default);
        const DefaultMediaValue = vm.MediaData[DefaultMediaIndex].value

        const MediaValue = (SelectedPartition.media) ? SelectedPartition.media : DefaultMediaValue
        
        console.log('Debug (ComputedInputTemplateMedia): '+MediaValue)
        
        // Return selected partition type
        return MediaValue
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionMediaUpdated', newValue)

      }
    },
    ComputedInputTemplatePortConnector: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        const DefaultPortConnectorIndex = vm.PortConnectorData.findIndex((PortConnector) => PortConnector.default);
        const DefaultPortConnectorValue = vm.PortConnectorData[DefaultPortConnectorIndex].value

        const PortConnectorValue = (SelectedPartition.port_connector) ? SelectedPartition.port_connector : DefaultPortConnectorValue
        
        // Return selected partition type
        return PortConnectorValue
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionPortConnectorUpdated', newValue)

      }
    },
    ComputedInputTemplatePortOrientation: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        const DefaultPortOrientationIndex = vm.PortOrientationData.findIndex((PortOrientation) => PortOrientation.default);
        const DefaultPortOrientationValue = vm.PortOrientationData[DefaultPortOrientationIndex].value

        const PortOrientationValue = (SelectedPartition.port_orientation) ? SelectedPartition.port_orientation : DefaultPortOrientationValue
        
        // Return selected partition type
        return PortOrientationValue
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionPortOrientationUpdated', newValue)

      }
    },
    ComputedInputTemplateEncLayoutCols: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        const EncLayoutCols = (SelectedPartition.type == 'enclosure') ? SelectedPartition.enc_layout.cols : 0
        
        // Return selected partition type
        return EncLayoutCols
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionEncLayoutColsUpdated', newValue)

      }
    },
    ComputedInputTemplateEncLayoutRows: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.GetSelectedPartition()
        const EncLayoutCols = (SelectedPartition.type == 'enclosure') ? SelectedPartition.enc_layout.rows : 0
        
        // Return selected partition type
        return EncLayoutCols
      },
      set(newValue) {

        // Store variables
        const vm = this

        // Emit new value
        vm.$emit('TemplatePartitionEncLayoutRowsUpdated', newValue)

      }
    },
  },
  methods: {
    onSubmit: function() {
      console.log("Debug (Submit): "+JSON.stringify(this.TemplateData[0]))
    },
    onReset: function() {
      console.log("Debug (Reset): ")
      this.$emit('FormReset')
    },
    GetRUSizeMin() {

      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFaceArray = ['front','rear']
      let RUSizeMin = 1

      CabinetFaceArray.forEach(function(CabinetFace){
        TemplateData.blueprint[CabinetFace].children.forEach(function(FirstLayerColumn){
          let TotalPartitionUnits = 0
          FirstLayerColumn.children.forEach(function(FirstLayerRow){
            TotalPartitionUnits = TotalPartitionUnits + FirstLayerRow.units
          })
          const WorkingRUSizeMin = Math.ceil(TotalPartitionUnits / 2)
          RUSizeMin = (WorkingRUSizeMin > RUSizeMin) ? WorkingRUSizeMin : RUSizeMin
        })
      })

      return RUSizeMin
    },
    GetSelectedPartitionParentSize: function() {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = JSON.parse(JSON.stringify(vm.SelectedPartitionAddress[CabinetFace]))
      const SelectedPartitionDirection = vm.ComputedSelectedPartitionDirection
      let WorkingMax = (SelectedPartitionDirection == 'column') ? 24 : TemplateData.ru_size * 2
      let WorkingPartition = JSON.parse(JSON.stringify(TemplateData.blueprint[CabinetFace]))

      
      SelectedPartitionAddress.pop()
      SelectedPartitionAddress.forEach(function(PartitionAddressIndex, Depth){
        let WorkingPartitionDirection = ((Depth + 1) % 2) ? 'column' : 'row'
        if(WorkingPartitionDirection == SelectedPartitionDirection) {
          WorkingMax = WorkingPartition.children[PartitionAddressIndex].units
        }
        WorkingPartition.children[PartitionAddressIndex]
      })
      
      return WorkingMax
    },
    GetSelectedPartitionSizeMin: function(){

      const vm = this
      const SelectedPartition = vm.GetSelectedPartition()
      const PartitionSizeMin = vm.GetSelectedPartitionSizeMinRecursion(SelectedPartition.children)

      return (PartitionSizeMin > 0) ? PartitionSizeMin : 1
    },
    GetSelectedPartitionSizeMinRecursion: function(PartitionArray, SelectedPartitionSizeMin = 0, RelativeDepth = 0) {

      // Store variables
      const vm = this

      PartitionArray.forEach(function(Partition){
        SelectedPartitionSizeMin = (RelativeDepth % 2) ? SelectedPartitionSizeMin + parseInt(Partition.units) : SelectedPartitionSizeMin
        SelectedPartitionSizeMin = vm.GetSelectedPartitionSizeMinRecursion(Partition.children, SelectedPartitionSizeMin, RelativeDepth + 1)
      })

      return SelectedPartitionSizeMin;
    },
    GetSelectedPartitionSizeMax: function() {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const SelectedPartitionDepth = SelectedPartitionAddress.length
      const SelectedPartitionParentSize = vm.GetSelectedPartitionParentSize()
      let SelectedPartitionSizeMax = SelectedPartitionParentSize
      let WorkingPartition = JSON.parse(JSON.stringify(TemplateData.blueprint[CabinetFace]))
      let WorkingPartitionAddress = []
      let WorkingSiblingPartitionAddress = []

      SelectedPartitionAddress.forEach(function(PartitionAddressIndex, Depth) {
        
        const WorkingPartitionDepth = Depth + 1

        if(WorkingPartitionDepth == SelectedPartitionDepth) {

          WorkingPartition.children.forEach(function(Sibling, SiblingIndex){

            // Set the partition address appropriate for the current partition
            WorkingSiblingPartitionAddress = WorkingPartitionAddress.concat([SiblingIndex])
            
            // Subtract units if partition is not selected
            if(WorkingSiblingPartitionAddress.length !== SelectedPartitionAddress.length || WorkingSiblingPartitionAddress.every(function(value, index) { return value === SelectedPartitionAddress[index]}) === false) {
              SelectedPartitionSizeMax = SelectedPartitionSizeMax - Sibling.units
            }
          })
        }

        WorkingPartition = WorkingPartition.children[PartitionAddressIndex]
        WorkingPartitionAddress.push(PartitionAddressIndex)
      })

      return SelectedPartitionSizeMax;
    },
    GetSelectedPartition: function() {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]

      // Set to default ("0") first partition on currently selected face
      let SelectedPartition = TemplateData.blueprint[CabinetFace]

      SelectedPartitionAddress.forEach(function(AddressIndex, Index) {
        SelectedPartition = SelectedPartition.children[AddressIndex]
      })

      // Return selected partition
      return SelectedPartition
    },
    GetCategoryOptions: function() {

      // Store variables
      const vm = this
      let WorkingArray = []

      // Populate working array with data to be used as select options
      for(let i = 0; i < vm.CategoryData.length; i++) {

        let Category = vm.CategoryData[i]
        WorkingArray.push({'value': Category.id, 'text': Category.name})
      }

      return WorkingArray
    },
    GetMediaOptions: function() {

      // Store variables
      const vm = this
      let WorkingArray = []

      // Populate working array with data to be used as select options
      for(let i = 0; i < vm.MediaData.length; i++) {

        let Media = vm.MediaData[i]
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
      for(let i = 0; i < vm.PortConnectorData.length; i++) {

        let PortConnector = vm.PortConnectorData[i]
        WorkingArray.push({'value': PortConnector.value, 'text': PortConnector.name})
      }

      return WorkingArray
    },
    GetPortOrientationOptions: function() {

      // Store variables
      const vm = this
      let WorkingArray = []

      // Populate working array with data to be used as select options
      for(let i = 0; i < vm.PortOrientationData.length; i++) {

        let PortOrientation = vm.PortOrientationData[i]
        WorkingArray.push({'value': PortOrientation.value, 'text': PortOrientation.name})
      }

      return WorkingArray
    },
    updateCategories: function(v) {
      this.optionsCategory = [];
      let x;
      for (x in v) {
        this.optionsCategory.push({value: v[x].id, text: v[x].name});
      }
    },
    setDefaultCategory: function(v) {
      this.selectedCategory = v;
    },
    CastPartitionSizeToInteger: function(value) {

      // Store variables
      const vm = this
      const Element = vm.$refs.ElementTemplateRUSize.$el
      const min = vm.GetSelectedPartitionSizeMin()
      const max = vm.GetSelectedPartitionSizeMax()

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
      const Element = vm.$refs.ElementTemplateRUSize.$el
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

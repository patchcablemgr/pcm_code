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
            v-model="TemplateData[Context][PreviewDataIndex].name"
            @change="$emit('TemplateNameUpdated', $event)"
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
            v-model="TemplateData[Context][PreviewDataIndex].category_id"
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
            name="type"
            v-model="TemplateData[Context][PreviewDataIndex].type"
            :options="optionsTemplateType"
            @change="$emit('TemplateTypeUpdated', $event)"
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
            v-model.number="InputTemplateRUSize"
            @change="$emit('TemplateRUSizeUpdated', $event)"
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
            name="function"
            v-model="inputTemplateFunction"
            :options="optionsTemplateFunction"
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
              name="mountConfig"
              v-model="inputTemplateMountConfig"
              :options="optionsTemplateMountConfig"
              @change="$emit('TemplateMountConfigUpdated', $event)"
              stacked
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
            v-model="ComputedInputTemplatePartitionType"
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
            name="partitionSize"
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
          {{ PreviewPortID }}
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
                  name="portLayoutCol"
                  :disabled="ComputedInputTemplatePartitionType != 'connectable'"
                  v-model="ComputedInputTemplatePortLayoutCols"
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
                  :disabled="ComputedInputTemplatePartitionType != 'connectable'"
                  v-model="ComputedInputTemplatePortLayoutRows"
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
        class="row"
        v-show="ComputedInputTemplatePartitionType == 'connectable'"
      >
        <dt class="col-sm-4">
          Medium
        </dt>
        <dd class="col-sm-8">
          <b-form-select
            name="media"
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
            name="portType"
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
            name="portOrientation"
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
                  name="encLayoutCol"
                  :disabled="ComputedInputTemplatePartitionType != 'enclosure'"
                  v-model="ComputedInputTemplateEncLayoutCols"
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
                  :disabled="ComputedInputTemplatePartitionType != 'enclosure'"
                  v-model="ComputedInputTemplateEncLayoutRows"
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
    <modal-templates-category
      :TemplateData="TemplateData"
      :CategoryData="CategoryData"
      :SelectedCategoryID="SelectedCategoryID"
      @TemplateCategoriesUpdated="$emit('TemplateCategoriesUpdated')"
      @categoriesUpdatedOrig="updateCategories($event)"
      @TemplateCategoryDeleted="$emit('TemplateCategoryDeleted', $event)"
      @TemplateCategorySubmitted="$emit('TemplateCategorySubmitted', $event)"
      @TemplateCategorySelected="$emit('TemplateCategorySelected', $event)"
      @categoriesSetDefault="setDefaultCategory($event)"
    />

    <!-- Port ID Modal -->
    <modal-templates-port-id
      v-if="ComputedInputTemplatePartitionType == 'connectable'"
      :SelectedPortFormatIndex="SelectedPortFormatIndex"
      :SelectedPortFormat="SelectedPortFormat"
      :PreviewPortID="PreviewPortID"
      v-on:TemplatePartitionPortFormatFieldSelected="$emit('TemplatePartitionPortFormatFieldSelected', $event)"
      v-on:TemplatePartitionPortFormatValueUpdated="$emit('TemplatePartitionPortFormatValueUpdated', $event)"
      v-on:TemplatePartitionPortFormatTypeUpdated="$emit('TemplatePartitionPortFormatTypeUpdated', $event)"
      v-on:TemplatePartitionPortFormatCountUpdated="$emit('TemplatePartitionPortFormatCountUpdated', $event)"
      v-on:TemplatePartitionPortFormatOrderUpdated="$emit('TemplatePartitionPortFormatOrderUpdated', $event)"
      v-on:TemplatePartitionPortFormatFieldMove="$emit('TemplatePartitionPortFormatFieldMove', $event)"
      v-on:TemplatePartitionPortFormatFieldCreate="$emit('TemplatePartitionPortFormatFieldCreate', $event)"
      v-on:TemplatePartitionPortFormatFieldDelete="$emit('TemplatePartitionPortFormatFieldDelete', $event)"
    />

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
    TemplateData: {type: Array},
    CategoryData: {type: Array},
    SelectedCategoryID: {type: Number},
    PortConnectorData: {type: Array},
    PortOrientationData: {type: Array},
    MediaData: {type: Array},
    PreviewData: {type: Array},
    PreviewDataIndex: {type: Number},
    SelectedPortFormatIndex: {type: Number},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    AddChildPartitionDisabled: {type: Boolean},
    AddSiblingPartitionDisabled: {type: Boolean},
    RemovePartitionDisabled: {type: Boolean},
    PartitionTypeDisabled: {type: Boolean},
    SelectedPartition: {type: Object},
    SelectedPortFormat: {type: Array},
    PreviewPortID: {type: String},
  },
  data() {
    return {
      inputTemplateType: this.TemplateData[Context][PreviewDataIndex].type,
      InputTemplateRUSize: this.TemplateData[Context][PreviewDataIndex].ru_size,
      inputTemplateFunction: this.TemplateData[Context][PreviewDataIndex].function,
      inputTemplateMountConfig: this.TemplateData[Context][PreviewDataIndex].mount_config,
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
        const Context = vm.Context
        const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
        const PartitionAddressSelected = vm.PartitionAddressSelected[Context][TemplateFaceSelected]
        const SelectedPartitionDepth = PartitionAddressSelected.length
        const SelectedPartitionDirection = (SelectedPartitionDepth % 2) ? 'column' : 'row'

        return SelectedPartitionDirection
      }
    },
    ComputedInputTemplatePartitionType: {
      get() {

        // Store variables
        const vm = this
        const SelectedPartition = vm.SelectedPartition
        
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
        const SelectedPartition = vm.SelectedPartition
        
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
        const SelectedPartition = vm.SelectedPartition
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
        const SelectedPartition = vm.SelectedPartition
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
        const SelectedPartition = vm.SelectedPartition
        const DefaultMediaIndex = vm.MediaData.findIndex((media) => media.default);
        const DefaultMediaValue = vm.MediaData[DefaultMediaIndex].value

        const MediaValue = (SelectedPartition.media) ? SelectedPartition.media : DefaultMediaValue
        
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
        const SelectedPartition = vm.SelectedPartition
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
        const SelectedPartition = vm.SelectedPartition
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
        const SelectedPartition = vm.SelectedPartition
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
        const SelectedPartition = vm.SelectedPartition
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
      console.log("Debug (Submit): "+JSON.stringify(this.PreviewData[0]))
      this.$emit('FormSubmit')
    },
    onReset: function() {
      console.log("Debug (Reset): ")
      this.$emit('FormReset')
    },
    GetRUSizeMin() {

      const vm = this
      const PreviewData = vm.PreviewData[0]
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
            TotalPartitionUnits = TotalPartitionUnits + Layer2Partition.units
          })

          // Store largest partition size
          const WorkingRUSizeMin = Math.ceil(TotalPartitionUnits / 2)
          RUSizeMin = (WorkingRUSizeMin > RUSizeMin) ? WorkingRUSizeMin : RUSizeMin
        })
      })

      return RUSizeMin
    },
    GetSelectedPartitionParentSize: function() {

      // Store variables
      const vm = this
      const PreviewData = vm.PreviewData[0]
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressSelected = JSON.parse(JSON.stringify(vm.PartitionAddressSelected[Context][TemplateFaceSelected]))
      const SelectedPartitionDirection = vm.ComputedSelectedPartitionDirection
      let WorkingMax = (SelectedPartitionDirection == 'column') ? 24 : PreviewData.ru_size * 2
      let WorkingPartitionChildren = JSON.parse(JSON.stringify(PreviewData.blueprint[TemplateFaceSelected]))

      
      PartitionAddressSelected.pop()
      PartitionAddressSelected.forEach(function(PartitionAddressIndex, Depth){
        let WorkingPartitionDirection = ((Depth + 1) % 2) ? 'column' : 'row'
        if(WorkingPartitionDirection == SelectedPartitionDirection) {
          WorkingMax = WorkingPartitionChildren[PartitionAddressIndex].units
        }
        WorkingPartitionChildren = WorkingPartitionChildren[PartitionAddressIndex].children
      })
      
      return WorkingMax
    },
    GetSelectedPartitionSizeMin: function(){

      const vm = this
      const SelectedPartition = vm.SelectedPartition
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
      const PreviewData = vm.PreviewData[0]
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressSelected = vm.PartitionAddressSelected[Context][TemplateFaceSelected]
      const SelectedPartitionDepth = PartitionAddressSelected.length
      const SelectedPartitionParentSize = vm.GetSelectedPartitionParentSize()
      let SelectedPartitionSizeMax = SelectedPartitionParentSize
      let WorkingPartitionChildren = JSON.parse(JSON.stringify(PreviewData.blueprint[TemplateFaceSelected]))
      let WorkingPartitionAddress = []
      let WorkingSiblingPartitionAddress = []

      PartitionAddressSelected.forEach(function(PartitionAddressIndex, Depth) {
        
        const WorkingPartitionDepth = Depth + 1

        if(WorkingPartitionDepth == SelectedPartitionDepth) {

          WorkingPartitionChildren.forEach(function(Sibling, SiblingIndex){

            // Set the partition address appropriate for the current partition
            WorkingSiblingPartitionAddress = WorkingPartitionAddress.concat([SiblingIndex])
            
            // Subtract units if partition is not selected
            if(WorkingSiblingPartitionAddress.length !== PartitionAddressSelected.length || WorkingSiblingPartitionAddress.every(function(value, index) { return value === PartitionAddressSelected[index]}) === false) {
              SelectedPartitionSizeMax = SelectedPartitionSizeMax - Sibling.units
            }
          })
        }

        WorkingPartitionChildren = WorkingPartitionChildren[PartitionAddressIndex].children
        WorkingPartitionAddress.push(PartitionAddressIndex)
      })

      return SelectedPartitionSizeMax;
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

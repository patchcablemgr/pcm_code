<template>
    <!-- Template port Format modal -->
    <b-modal
      :id="ModalID"
      title="Port ID"
      ok-only
      ok-title="OK"
    >
      <b-card
        title="Port Format"
        class="position-static"
      >
        <validation-observer ref="simpleRules">
          <b-form
            v-if="SelectedPortFormat"
          >

            <div
             class="mb-3 pcm_template_port_format_field_container"
            >
              <div
                v-for="(PortFormat, PortFormatIndex) in SelectedPortFormat"
                :key="PortFormatIndex"
                class="mr-1 pcm_template_port_format_field"
              >
                <validation-provider
                  #default="{ errors }"
                  name="Value"
                  :rules="ComputedPortFieldValidate( PortFormat.type )"
                >
                  <div style="height: 20px;">
                    <small
                      v-show="PortFormat.type == 'incremental' || PortFormat.type == 'series'"
                    >
                      {{ PortFormat.order }}{{ GetNumericalSuffix(PortFormat.order) }}
                    </small>
                  </div>
                  <div
                    :class="{
                      pcm_template_port_format_field_selected: SelectedPortFormatIndex == PortFormatIndex,
                    }"
                  >
                    <b-form-input
                      v-model="PortFormat.value"
                      @click="PortFormatFieldSelected(PortFormatIndex)"
                      @change="UpdateValue($event)"
                      :state="errors.length > 0 ? false:null"
                    />
                  </div>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </div>
            </div>

            <!-- Create/Delete-->
            <dl
              class="row"
            >
              <dt class="col-sm-4">
                Create/Delete
                <feather-icon
                  icon="HelpCircleIcon"
                  v-b-tooltip.hover.html="ToolTipCreateDelete"
                />
              </dt>
              <dd class="col-sm-8">

                <!-- Create Before -->
                <b-button
                  title="Create a new field before the one selected"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                  class="btn-icon"
                  @click="$emit('TemplatePartitionPortFormatFieldCreate', {'context': Context, 'position': 'before'} )"
                >
                  <feather-icon icon="PlusIcon" />
                  <feather-icon
                    icon="MoreHorizontalIcon"
                  />
                </b-button>

                <!-- Create After -->
                <b-button
                  title="Create a new field after the one selected"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                  class="btn-icon"
                  @click="$emit('TemplatePartitionPortFormatFieldCreate', {'context': Context, 'position': 'after'} )"
                >
                  <feather-icon
                    icon="MoreHorizontalIcon"
                  />
                  <feather-icon icon="PlusIcon" />
                </b-button>

                <!-- Delete -->
                <b-button
                  title="Delete selected field"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                  class="btn-icon"
                  @click="$emit('TemplatePartitionPortFormatFieldDelete', {'context': Context} )"
                >
                  <feather-icon icon="MinusIcon" />
                </b-button>
              </dd>
            </dl>

            <!-- Move-->
            <dl
              class="row"
            >
              <dt class="col-sm-4">
                Move
                <feather-icon
                  icon="HelpCircleIcon"
                  v-b-tooltip.hover.html="ToolTipMove"
                />
              </dt>
              <dd class="col-sm-8">

                <!-- Move Left -->
                <b-button
                  title="Move selected field left"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                  class="btn-icon"
                  @click="$emit('TemplatePartitionPortFormatFieldMove', {'context': Context, 'direction': 'left'} )"
                >
                  <feather-icon icon="ChevronLeftIcon" />
                </b-button>

                <!-- Move Right -->
                <b-button
                  title="Move selected field right"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                  class="btn-icon"
                  @click="$emit('TemplatePartitionPortFormatFieldMove', {'context': Context, 'direction': 'right'} )"
                >
                  <feather-icon icon="ChevronRightIcon" />
                </b-button>
              </dd>
            </dl>

            <!-- Type-->
            <dl
              class="row"
            >
              <dt class="col-sm-4">
                Type
                <feather-icon
                  icon="HelpCircleIcon"
                  v-b-tooltip.hover.html="ToolTipType"
                />
              </dt>
              <dd class="col-sm-8">
                <b-form-select
                  v-model="SelectedPortFormat[SelectedPortFormatIndex].type"
                  :options="TypeOptions"
                  @change="$emit('TemplatePartitionPortFormatTypeUpdated', {'context': Context, 'value': $event} )"
                />
              </dd>
            </dl>

            <!-- Count-->
            <dl
              class="row"
            >
              <dt class="col-sm-4">
                Count
                <feather-icon
                  icon="HelpCircleIcon"
                  v-b-tooltip.hover.html="ToolTipCount"
                />
              </dt>
              <dd class="col-sm-8">
                <b-form-input
                  v-model="SelectedPortFormat[SelectedPortFormatIndex].count"
                  :disabled=" SelectedPortFormat[SelectedPortFormatIndex].type == 'static' || SelectedPortFormat[SelectedPortFormatIndex].type == 'series' "
                  @change="$emit('TemplatePartitionPortFormatCountUpdated', {'context': Context, 'value': $event} )"
                  type=number
                />
              </dd>
            </dl>

            <!-- Order-->
            <dl
              class="row"
            >
              <dt class="col-sm-4">
                Order
                <feather-icon
                  icon="HelpCircleIcon"
                  v-b-tooltip.hover.html="ToolTipOrder"
                />
              </dt>
              <dd class="col-sm-8">
                <b-form-select
                  :value=" SelectedPortFormat[SelectedPortFormatIndex].order "
                  :options=" ComputedOrderOptions "
                  :disabled=" SelectedPortFormat[SelectedPortFormatIndex].type == 'static' "
                  @change=" $emit('TemplatePartitionPortFormatOrderUpdated', {'context': Context, 'value': $event} ) "
                />
              </dd>
            </dl>

            <!-- Preview-->
            <dl
              class="row"
            >
              <dt class="col-sm-4">
                Preview
                <feather-icon
                  icon="HelpCircleIcon"
                  v-b-tooltip.hover.html="ToolTipPreview"
                />
              </dt>
              <dd class="col-sm-8">
                {{ PreviewPortID }}
              </dd>
            </dl>
            
          </b-form>
        </validation-observer>
      </b-card>
    </b-modal>
</template>

<script>
import {
  BRow,
  BCol,
  BCard,
  BCardText,
  BForm,
  BFormInput,
  BFormSelect,
  BButton,
  VBTooltip,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { configure, ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, regex } from '@validations'
import { PCM } from '@/mixins/PCM.js'

const config = {
  useConstraintAttrs: false,
  defaultMessage: "invalid input"
}

configure(config)

const TypeOptions = [
  {
    "value": "static",
    "text": "Static"
  },
  {
    "value": "incremental",
    "text": "Incremental"
  },
  {
    "value": "series",
    "text": "Series"
  },
]
const ToolTipCreateDelete = {
  title: `
    <div class="text-left">
    <div>Create new fields or delete existing fields.</div>
    </div>
  `
}
const ToolTipCreateBefore = {
  title: `
    <div class="text-left">
    <div>Create a new field before the selected field.</div>
    </div>
  `
}
const ToolTipCreateAfter = {
  title: `
    <div class="text-left">
    <div>Create a new field after the selected field.</div>
    </div>
  `
}
const ToolTipDelete = {
  title: `
    <div class="text-left">
    <div>Delete the selected field.</div>
    </div>
  `
}
const ToolTipMove = {
  title: `
    <div class="text-left">
    <div>Reposition field by moving it left or right.</div>
    </div>
  `
}
const ToolTipType = {
  title: `
    <div class="text-left">
    <div><strong>Static:</strong></div>
    <div>Any string, positioned as displayed, does not repeat.</div>
    <br>
    <div><strong>Incremental:</strong></div>
    <div>Number or letter, positioned as displayed, incremented in Order for Count before repeating.</div>
    <br>
    <div><strong>Series:</strong></div>
    <div>Comma delimited series of any string, positioned as displayed, incremented in Order before repeating.</div>
    <br>
    </div>
  `
}
const ToolTipCount = {
  title: `
    <div class="text-left">
    <div>Number of times an Incremental field increments before the next incrementing field is processed in order.</div>
    </div>
  `
}
const ToolTipOrder = {
  title: `
    <div class="text-left">
    <div>Order in which an incrementing field (Incremental or Series) increments in relation to other incrementing fields.  Numerically lower Order is processed before numerically higher Order.</div>
    </div>
  `
}
const ToolTipPreview = {
  title: `
    <div class="text-left">
    <div>Preview Port IDs generated from Port Format settings above.</div>
    </div>
  `
}
const SelectedPortFormatIndex = 0

export default {
  mixins: [PCM],
  components: {
    ValidationProvider,
    ValidationObserver,
    BRow,
    BCol,
    BCard,
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
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PreviewPortID: {type: String},
  },
  data() {
    return {
      TypeOptions,
      required,
      regex,
      ToolTipCreateDelete,
      ToolTipCreateBefore,
      ToolTipCreateAfter,
      ToolTipDelete,
      ToolTipMove,
      ToolTipType,
      ToolTipCount,
      ToolTipOrder,
      ToolTipPreview,
      SelectedPortFormatIndex,
    }
  },
  computed: {
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    SelectedPortFormat: function() {

      const vm = this
      const Context = vm.Context
      let PortFormat = null
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
      if(ObjectID) {
        const TemplateID = vm.GetTemplateID(ObjectID, Context)
        const Face = vm.TemplateFaceSelected[Context]
        const PartitionAddress = vm.PartitionAddressSelected[Context][Face]
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
        const Template = vm.Templates[Context][TemplateIndex]
        const Blueprint = Template.blueprint[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        PortFormat = (Partition.type == 'connectable') ? Partition.port_format : null
      }

      return PortFormat
    },
    ComputedOrderOptions: {
      get() {
        const vm = this
        let OrderOptions = []
        let OrderValue = 1
        let NumericalSuffix = ""
        let OrderText = ""

        // Loop through port format fields
        vm.SelectedPortFormat.forEach(function(PortFormat){

          if(PortFormat.type == "series" || PortFormat.type == "incremental") {

            // Determine numerical suffix of order value
            NumericalSuffix = vm.GetNumericalSuffix(OrderValue)
            
            // Compile order text
            OrderText = OrderValue+NumericalSuffix

            // Append option
            OrderOptions.push({"value": OrderValue, "text": OrderText})

            // Increment order value
            OrderValue = OrderValue + 1

          }
        })

        return OrderOptions
      },
      set() {}
    },
  },
  methods: {
    UpdateValue: function(Value) {

      const vm = this
      const Context = vm.Context

    },
    PortFormatFieldSelected: function(PortFormatIndex) {

      const vm = this

      vm.SelectedPortFormatIndex = PortFormatIndex
      return true

    },
    GetTemplateIndex: function(TemplateID) {

      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.Templates[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    GetPartition: function(Blueprint, PartitionAddress) {
			
			// Locate template partition
			let Partition = Blueprint
			let PartitionCollection = Blueprint
			PartitionAddress.forEach(function(PartitionIndex) {
				if(typeof PartitionCollection[PartitionIndex] !== 'undefined') {
					Partition = PartitionCollection[PartitionIndex]
					PartitionCollection = PartitionCollection[PartitionIndex]['children']
				} else {
					return false
				}
			})
			
			return Partition
      
    },
    ComputedPortFieldValidate: function(PortFormatFieldType) {

      if(PortFormatFieldType == 'static') {
        return { required: true, regex: '^[a-zA-Z0-9\\\/\-\_\=\+\|\*]+$' }
      } else if(PortFormatFieldType == 'incremental') {
        return { required: true, regex: '^([1-9][0-9]*|[a-z]|[A-Z])$' }
      } else if(PortFormatFieldType == 'series') {
        return { required: true, regex: '^(,?[a-zA-Z0-9\\\/\-\_\=\+\|\*])*$' }
      }
    },
    GetNumericalSuffix: function(Number) {

      let NumericalSuffix = ""
      
      if(Number == 1) {
        NumericalSuffix = "st"
      } else if(Number == 2) {
        NumericalSuffix = "nd"
      } else if(Number == 3) {
        NumericalSuffix = "rd"
      } else {
        NumericalSuffix = "th"
      }

      return NumericalSuffix
    },
  },
  mounted() {
  }
}
</script>
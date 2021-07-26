<template>
    <!-- Template port Format modal -->
    <b-modal
      id="modal-templates-port-id"
      title="Port ID"
      ok-only
      ok-title="OK"
    >
      <b-card
        title="Port Format"
        class="position-static"
      >
        <validation-observer ref="simpleRules">
          <b-form @submit.prevent="(CategorySelected) ? categoryPUT() : categoryPOST()">

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
                  :rules="ComputedPortFieldValidate( SelectedPortFormat[PortFormatIndex].type )"
                >
                  <div style="height: 20px;">
                    <small
                      v-show="SelectedPortFormat[PortFormatIndex].type == 'incremental' || SelectedPortFormat[PortFormatIndex].type == 'series'"
                    >
                      {{ SelectedPortFormat[PortFormatIndex].order }}{{ GetNumericalSuffix(SelectedPortFormat[PortFormatIndex].order) }}
                    </small>
                  </div>
                  <div
                    :class="{
                      pcm_template_port_format_field_selected: SelectedPortFormatIndex == PortFormatIndex,
                    }"
                  >
                    <b-form-input
                      v-model="SelectedPortFormat[PortFormatIndex].value"
                      @click="$emit('TemplatePartitionPortFormatFieldSelected', {'index': PortFormatIndex} )"
                      @change="$emit('TemplatePartitionPortFormatValueUpdated', {'index': PortFormatIndex, 'value': $event} )"
                      :formatter="ValueFormat"
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
                  @click="$emit('TemplatePartitionPortFormatFieldCreate', {'direction': 'before'} )"
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
                  @click="$emit('TemplatePartitionPortFormatFieldCreate', {'direction': 'after'} )"
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
                  @click="$emit('TemplatePartitionPortFormatFieldDelete', {} )"
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
                  @click="$emit('TemplatePartitionPortFormatFieldMove', {'direction': 'left'} )"
                >
                  <feather-icon icon="ChevronLeftIcon" />
                </b-button>

                <!-- Move Right -->
                <b-button
                  title="Move selected field right"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                  class="btn-icon"
                  @click="$emit('TemplatePartitionPortFormatFieldMove', {'direction': 'right'} )"
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
                  @change="$emit('TemplatePartitionPortFormatTypeUpdated', {'value': $event} )"
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
                  @change="$emit('TemplatePartitionPortFormatCountUpdated', {'value': $event} )"
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
                  @change=" $emit('TemplatePartitionPortFormatOrderUpdated', {'index': SelectedPortFormatIndex, 'value': $event} ) "
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
                {{ PortIDPreview }}
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
const SelectedPortFormatIndex = 0
ToolTipCreateDelete
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

export default {
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
    SelectedPortFormatIndex: {type: Number},
    SelectedPortFormat: {type: Array},
    PortIDPreview: {type: String},
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
    }
  },
  computed: {
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
    ValueFormat: function(value) {

      // Store variables
      const vm = this
      const PortFormat = vm.SelectedPortFormat
      const PortFormatIndex = vm.SelectedPortFormatIndex
      const PortFormatField = PortFormat[PortFormatIndex]
      const PortFormatFieldType = PortFormatField.type

      return value

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
}
</script>
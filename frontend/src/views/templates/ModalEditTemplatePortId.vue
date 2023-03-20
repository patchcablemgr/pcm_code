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
                      v-model="FieldValue[PortFormatIndex].value"
                      @change="FieldValueUpdate"
                      @click="PortFormatFieldSelected(PortFormatIndex)"
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
                  @click="AddField('before')"
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
                  @click="AddField('after')"
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
                  @click="DeleteField()"
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
                  @click="MoveField('left')"
                >
                  <feather-icon icon="ChevronLeftIcon" />
                </b-button>

                <!-- Move Right -->
                <b-button
                  title="Move selected field right"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  variant="outline-primary"
                  class="btn-icon"
                  @click="MoveField('right')"
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
                  v-model="FieldType"
                  :options="TypeOptions"
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
                  v-model.number="FieldCount"
                  :disabled=" SelectedPortFormat[SelectedPortFormatIndex].type == 'static' || SelectedPortFormat[SelectedPortFormatIndex].type == 'series' "
                  :formatter="CastToInteger"
                  type="number"
                  min="1"
                  debounce="500"
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
                  v-model="FieldOrder"
                  :options=" ComputedOrderOptions "
                  :disabled=" SelectedPortFormat[SelectedPortFormatIndex].type == 'static' "
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
                {{ PortPreview }}
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
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    PortPreview: function() {

      const vm = this
      const Context = vm.Context
      return vm.GeneratePortPreview(Context)
    },
    SelectedPortFormat: function() {

      const vm = this
      const Context = vm.Context
      let PortFormat = null
      const ObjectID = vm.StateSelected[Context].object_id
      const Template = vm.GetTemplate({ObjectID, Context})
      if(ObjectID) {
        const Face = vm.StateSelected[Context].object_face
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
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
        const Partition = vm.GetPartitionSelected()

        // Loop through port format fields
        Partition.port_format.forEach(function(PortFormat){

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
    FieldValue: {
      get() {

        const vm = this
        const Context = vm.Context
        const FieldIndex = vm.SelectedPortFormatIndex
        const Partition = vm.GetPartitionSelected(Context)

        return Partition.port_format
      }
    },
    FieldType: {
      get() {

        const vm = this
        const Context = vm.Context
        const FieldIndex = vm.SelectedPortFormatIndex
        const Partition = vm.GetPartitionSelected(Context)

        return Partition.port_format[FieldIndex].type
      },
      set(Type) {

        const vm = this
        const Context = vm.Context
        const FieldIndex = vm.SelectedPortFormatIndex
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const TemplateID = Template.id
        const URL = '/api/templates/'+TemplateID

        const Face = vm.TemplateFaceSelected[Context]
        const Blueprint = Template.blueprint[Face]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        let Order = 0
        let Value = ''

        const TypeOrig = Partition.port_format[FieldIndex].type

        if(Type != TypeOrig) {

          // Determine field order
          if(Type == 'series' || Type == 'incremental') {

            Order = 1
            let WorkingOrderArray = []

            // Gather all incrementable field orders
            Partition.port_format.forEach(function(PortFormatField, PortFormatFieldIndex){

              const PortFormatFieldType = PortFormatField.type
              
              if((PortFormatFieldType == 'series' || PortFormatFieldType == 'incremental') && PortFormatFieldIndex != FieldIndex) {
                const PortFormatFieldOrder = PortFormatField.order
                WorkingOrderArray.push(PortFormatFieldOrder)
              }
            })

            // Sort incrementable field orders
            WorkingOrderArray.sort(function(a, b){return a - b})

            // Find first available
            WorkingOrderArray.forEach(function(WorkingOrder){
              if(WorkingOrder == Order) {
                Order = Order + 1
              }
            })
          }

          // Determine field value
          if(Type == 'series') {
            Value = 'A,B,C'
          } else if(Type == 'incremental') {
            Value = '1'
          } else if(Type == 'static') {
            Value = 'Port'
          }
          const DefaultCount = vm.$store.state.pcmTemplates.DefaultCount

          // Apply new values
          Partition.port_format[FieldIndex].value = Value
          Partition.port_format[FieldIndex].type = Type
          Partition.port_format[FieldIndex].count = DefaultCount
          Partition.port_format[FieldIndex].order = Order

          if(Context == 'template') {

            vm.$http.patch(URL, Template).then(response => {
              vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:response.data})
            }).catch(error => {
              vm.DisplayError(error)
            })
          } else {

            // Insert new field
            vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})
          }
        }
      }
    },
    FieldCount: {
      get() {

        const vm = this
        const Context = vm.Context
        const FieldIndex = vm.SelectedPortFormatIndex
        const Partition = vm.GetPartitionSelected(Context)
        const Count = Partition.port_format[FieldIndex].count

        return Count
      },
      set(Count) {

        const vm = this
        const Context = vm.Context
        const FieldIndex = vm.SelectedPortFormatIndex
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const TemplateID = Template.id
        const URL = '/api/templates/'+TemplateID

        const Face = vm.TemplateFaceSelected[Context]
        const Blueprint = Template.blueprint[Face]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        const CountOrig = Partition.port_format[FieldIndex].count
        const CountNew = parseInt(Count)

        if(CountNew != CountOrig) {

          Partition.port_format[FieldIndex].count = CountNew

          if(Context == 'template') {

            vm.$http.patch(URL, Template).then(response => {
              vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:response.data})
            }).catch(error => {
              vm.DisplayError(error)
            })
          } else {

            // Insert new field
            vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})
          }
        }
      }
    },
    FieldOrder: {
      get() {

        const vm = this
        const Context = vm.Context
        const FieldIndex = vm.SelectedPortFormatIndex
        const Partition = vm.GetPartitionSelected(Context)

        return Partition.port_format[FieldIndex].order
      },
      set(Order) {

        const vm = this
        const Context = vm.Context
        const FieldIndex = vm.SelectedPortFormatIndex
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const TemplateID = Template.id
        const URL = '/api/templates/'+TemplateID

        const Face = vm.TemplateFaceSelected[Context]
        const Blueprint = Template.blueprint[Face]
        const PartitionAddress = vm.StateSelected[Context].partition[Face]
        const Partition = vm.GetPartition(Blueprint, PartitionAddress)
        const OrderOrig = Partition.port_format[FieldIndex].order

        if(Order != OrderOrig) {
          Partition.port_format.forEach(function(PortFormatField, index){

            if(index != FieldIndex) {

              // Is field incrementable?
              const PortFormatFieldType = PortFormatField.type
              if(PortFormatFieldType == 'incremental' || PortFormatFieldType == 'series') {
                
                // Adjust field order
                const PortFormatFieldOrder = PortFormatField.order
                if(PortFormatFieldOrder > Order && PortFormatFieldOrder < OrderOrig) {

                  // Increment
                  Partition.port_format[index].order = PortFormatFieldOrder + 1
                } else if(PortFormatFieldOrder < Order && PortFormatFieldOrder > OrderOrig) {

                  // Decrement
                  Partition.port_format[index].order = PortFormatFieldOrder - 1
                } else if(PortFormatFieldOrder == Order) {
                  if(Order > OrderOrig) {

                    // Decrement
                    Partition.port_format[index].order = PortFormatFieldOrder - 1
                  } else if(Order < OrderOrig) {

                    // Increment
                    Partition.port_format[index].order = PortFormatFieldOrder + 1
                  }
                }
              }
            }
          })

          Partition.port_format[FieldIndex].order = Order

          if(Context == 'template') {

            vm.$http.patch(URL, Template).then(response => {
              vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:response.data})
            }).catch(error => {
              vm.DisplayError(error)
            })
          } else {

            // Insert new field
            vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})
          }
        }
      }
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
    AddField: function(Position) {

      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
      const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
      const TemplateID = Template.id
      const URL = '/api/templates/'+TemplateID

      const Face = vm.TemplateFaceSelected[Context]
      const Blueprint = Template.blueprint[Face]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]
      const Partition = vm.GetPartition(Blueprint, PartitionAddress)
      const PortFormatLength = Partition.port_format.length
      const PortFormatIndex = vm.SelectedPortFormatIndex
      const InsertPosition = (Position == 'before') ? PortFormatIndex : PortFormatIndex + 1
      const DefaultCount = vm.$store.state.pcmTemplates.DefaultCount
      const DefaultPortFormatField = {'type': 'static', 'value': 'Port', 'count': DefaultCount, 'order': 0}
      Partition.port_format.splice(InsertPosition, 0, DefaultPortFormatField)
      

      // Limit number of fields to 5
      if(PortFormatLength < 5) {

        if(Context == 'template') {

          vm.$http.patch(URL, Template).then(response => {
            vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:response.data})
            vm.SelectedPortFormatIndex = (Position == 'before') ? PortFormatIndex + 1 : PortFormatIndex
          }).catch(error => {
            vm.DisplayError(error)
          })
        } else {

          // Insert new field
          vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})
          vm.SelectedPortFormatIndex = (Position == 'before') ? PortFormatIndex + 1 : PortFormatIndex
        }
      }
    },
    DeleteField: function() {

      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
      const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
      const TemplateID = Template.id
      const URL = '/api/templates/'+TemplateID

      const Face = vm.TemplateFaceSelected[Context]
      const Blueprint = Template.blueprint[Face]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]
      const Partition = vm.GetPartition(Blueprint, PartitionAddress)
      const PortFormatLength = Partition.port_format.length

      // Prevent deleting all port format fields
      if(PortFormatLength > 1) {

        const PortFormatIndex = vm.SelectedPortFormatIndex
        const FieldType = Partition.port_format[PortFormatIndex].type
        const FieldOrder = Partition.port_format[PortFormatIndex].order

        // Adjust incremental order
        if(FieldType == 'series' || FieldType == 'incremental') {
          Partition.port_format.forEach(function(PortFormatField){
            const PortFormatFieldType = PortFormatField.type
            if(PortFormatFieldType == 'series' || PortFormatFieldType == 'incremental') {
              const PortFormatFieldOrder = PortFormatField.order
              if(PortFormatFieldOrder > FieldOrder) {
                PortFormatField.order = PortFormatFieldOrder - 1
              }
            }
          })
        }

        // Delete selected field
        Partition.port_format.splice(PortFormatIndex, 1)

        if(Context == 'template') {

          vm.$http.patch(URL, Template).then(response => {

            // Insert new field
            vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:response.data})

            // Adjust port format index
            if(PortFormatIndex >= (PortFormatLength - 1)) {
              vm.SelectedPortFormatIndex = PortFormatIndex - 1
            }
          }).catch(error => {
            vm.DisplayError(error)
          })
        } else {

          // Insert new field
          vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})

          // Adjust port format index
          if(PortFormatIndex >= (PortFormatLength - 1)) {
            vm.SelectedPortFormatIndex = PortFormatIndex - 1
          }
        }
      }

      
    },
    MoveField: function(Direction) {
      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
      const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
      const TemplateID = Template.id
      const URL = '/api/templates/'+TemplateID

      const Face = vm.TemplateFaceSelected[Context]
      const Blueprint = Template.blueprint[Face]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]
      const Partition = vm.GetPartition(Blueprint, PartitionAddress)

      const PortFormatIndex = vm.SelectedPortFormatIndex

      // Determine new position
      let NewIndex
      if(Direction == 'left') {
        NewIndex = (PortFormatIndex == 0) ? 0 : PortFormatIndex - 1
      } else {
        NewIndex = (PortFormatIndex == (Partition.port_format.length - 1)) ? PortFormatIndex : PortFormatIndex + 1
      }

      // Move field to new position
      Partition.port_format.splice(NewIndex, 0, Partition.port_format.splice(PortFormatIndex, 1)[0])

      if(Context == 'template') {

        vm.$http.patch(URL, Template).then(response => {

          // Insert new field
          vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:response.data})

          // Adjust port format index
          vm.SelectedPortFormatIndex = NewIndex

        }).catch(error => {

          vm.DisplayError(error)
        })
      } else {

        // Insert new field
        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})

        // Adjust port format index
        vm.SelectedPortFormatIndex = NewIndex
      }
    },
    FieldValueUpdate: function(Value) {
      const vm = this
      const Context = vm.Context
      const FieldIndex = vm.SelectedPortFormatIndex
      const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
      const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
      const TemplateID = Template.id
      const URL = '/api/templates/'+TemplateID

      const Face = vm.TemplateFaceSelected[Context]
      const Blueprint = Template.blueprint[Face]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]
      const Partition = vm.GetPartition(Blueprint, PartitionAddress)

      Partition.port_format[FieldIndex].value = Value

      if(Context == 'template') {

        vm.$http.patch(URL, Template).then(response => {

          // Insert new field
          vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:response.data})

        }).catch(error => {

          vm.DisplayError(error)
        })
      } else {

        // Insert new field
        vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:Context, data:Template})

      }
    },
    CastToInteger: function(value) {

      // Convert value from String to Integer
      return parseInt(value)
    },
  },
  mounted() {
  }
}
</script>
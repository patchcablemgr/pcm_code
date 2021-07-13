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
        <b-form @submit.prevent="(CategorySelected) ? categoryPUT() : categoryPOST()">

          <div
            v-for="(PortFormat, PortFormatIndex) in SelectedPortFormat"
            :key="PortFormatIndex"
            :class="{
              pcm_template_port_format_field_selected: SelectedPortFormatIndex == PortFormatIndex,
            }"
          >
            <b-form-input
              v-model="SelectedPortFormat[PortFormatIndex].value"
              @click="SelectedPortFormatIndex = PortFormatIndex"
              @change="$emit('TemplatePartitionPortFormatValueUpdated', {'index': PortFormatIndex, 'value': $event} )"
            />
          </div>

          <!-- Type-->
          <dl
            class="row"
          >
            <dt class="col-sm-4">
              Type
            </dt>
            <dd class="col-sm-8">
              <b-form-select
                v-model="SelectedPortFormat[SelectedPortFormatIndex].type"
                :options="TypeOptions"
                @change="$emit('TemplatePartitionPortFormatTypeUpdated', {'index': SelectedPortFormatIndex, 'value': $event} )"
              />
            </dd>
          </dl>

          <!-- Count-->
          <dl
            class="row"
          >
            <dt class="col-sm-4">
              Count
            </dt>
            <dd class="col-sm-8">
              <b-form-select
                v-model="SelectedPortFormat[SelectedPortFormatIndex].type"
                :options="TypeOptions"
                @change="$emit('TemplatePartitionPortFormatTypeUpdated', {'index': SelectedPortFormatIndex, 'value': $event} )"
              />
            </dd>
          </dl>

          <!-- Order-->
          <dl
            class="row"
          >
            <dt class="col-sm-4">
              Order
            </dt>
            <dd class="col-sm-8">
              <b-form-select
                v-model="SelectedPortFormat[SelectedPortFormatIndex].type"
                :options="TypeOptions"
                @change="$emit('TemplatePartitionPortFormatTypeUpdated', {'index': SelectedPortFormatIndex, 'value': $event} )"
              />
            </dd>
          </dl>
          
        </b-form>
      </b-card>
    </b-modal>
</template>

<script>
import {
  BCard,
  BCardText,
  BForm,
  BFormInput,
  BFormSelect,
} from 'bootstrap-vue'

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
const DefaultPortFormat = [
  {'type': 'static', 'value': 'Port', 'count': 0, 'order': 0},
  {'type': 'incremental', 'value': '1', 'count': 0, 'order': 0},
]
const SelectedPortFormatIndex = 0

export default {
  components: {
    BCard,
    BCardText,
    BForm,
    BFormInput,
    BFormSelect,
  },
  props: {
    TemplateData: {type: Array},
    CabinetFace: {type: String},
    SelectedPartitionAddress: {type: Object},
    SelectedPortFormat: {type: Array},
  },
  data() {
    return {
      TypeOptions,
      DefaultPortFormat,
      SelectedPortFormatIndex,
    }
  },
  methods: {
  },
}
</script>
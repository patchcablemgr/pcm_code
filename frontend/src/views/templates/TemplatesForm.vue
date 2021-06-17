<template>
    
  <b-row>
    <b-form
      @submit.prevent="onSubmit"
      @reset="onReset"
    >

      <!-- Name -->
      <dl class="row">
        <dt class="col-sm-4">
          Name
        </dt>
        <dd class="col-sm-8">
          <b-form-input
            v-model="InputTemplateName"
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
            v-model="InputTemplateCategory"
            :options="optionsCategory"
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
            min=1
            max=5
            :formatter="CastToInteger"
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
          />
        </dd>
      </dl>

      <!-- Add/remove Partition -->
      <dl class="row">
        <dt class="col-sm-4">
          Add/Remove Partition
        </dt>
        <dd class="col-sm-8">
          <b-form-radio-group
          v-model="selected"
          :options="optionsAddRemovePartition"
          name="radios-add-remove-partition"
          stacked
          />
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
          type="number"
          />
        </dd>
      </dl>

      <!-- Port ID -->
      <dl class="row"
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
      <dl class="row">
        <dt class="col-sm-4">
          Partition Size
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
      v-on:categoriesUpdated="updateCategories($event)"
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
    TemplateData: {type: Array},
    CabinetFace: {type: String},
    SelectedPartitionAddress: {type: Object},
  },
  data() {
    return {
      InputTemplateName: this.TemplateData[0].name,
      InputTemplateCategory: this.TemplateData[0].category_id,
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
      optionsAddRemovePartition: [
        { text: 'Horizontal', value: 0 },
        { text: 'Vertical', value: 1 },
      ],
    }
  },
  computed: {
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
    }
  },
  methods: {
    onSubmit: function() {
      console.log("Debug (Submit): "+JSON.stringify(this.TemplateData[0]))
    },
    onReset: function() {
      console.log("Debug (Reset): ")
      this.$emit('FormReset')
    },
    GetSelectedPartition: function() {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const SelectedPartitionAddressArray = SelectedPartitionAddress.split('-')
      let SelectedPartition = TemplateData.blueprint[CabinetFace]

      // Traverse blueprint until selected partition is reached
      SelectedPartitionAddressArray.forEach(function(AddressIndex, Index) {
        if(Index) {
          SelectedPartition = SelectedPartition.children[AddressIndex]
        } else {
          SelectedPartition = SelectedPartition[AddressIndex]
        }
      })

      // Return selected partition
      return SelectedPartition
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
    CastToInteger: function(value) {

      // Store variables
      const Element = this.$refs.ElementTemplateRUSize.$el
      const min = Element.getAttribute('min')
      const max = Element.getAttribute('max')

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

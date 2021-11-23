<template>
    <!-- Template edit modal -->
    <b-modal
      id="modal-templates-edit"
      title="Edit"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>
          <b-card
            title="Properties"
          >
            <b-card-text>
              <div
                class="h5 font-weight-bolder m-0"
              >
                General:</div>
              <hr
                class="separator mt-0"
              >

              <!-- Object Name -->
              <dl
                v-if="Context == 'preview'"
                class="row"
              >
                <dt class="col-sm-4">
                  Name
                </dt>
                <dd class="col-sm-8">
                  <b-form-input
                    v-model="ObjectName"
                    @change=" $emit('ObjectEdited', {'name': $event}) "
                  />
                </dd>
              </dl>

              <!-- Template Name -->
              <dl
                v-if="Context == 'template'"
                class="row"
              >
                <dt class="col-sm-4">
                  Name
                </dt>
                <dd class="col-sm-8">
                  <b-form-input
                    v-model="TemplateName"
                    @change=" $emit('TemplateEdited', {'name': $event}) "
                  />
                </dd>
              </dl>

              <!-- Category -->
              <dl
                v-if="Context == 'template'"
                class="row"
              >
                <dt class="col-sm-4">
                  Category
                </dt>
                <dd class="col-sm-8">
                  <b-form-select
                    v-model="TemplateCategoryID"
                    :options="GetCategoryOptions()"
                    @change=" $emit('TemplateEdited', {'category_id': $event}) "
                  />
                </dd>
              </dl>

              <div
                class="h5 font-weight-bolder m-0"
              >
                Partition:</div>
              <hr
                class="separator mt-0"
              >

              <!-- Port ID -->
              <dl
                v-if="Context == 'template'"
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
                    v-b-modal.modal-templates-port-id
                  >
                    <feather-icon icon="EditIcon" />
                  </b-button>
                </dd>
                <dd class="col-sm-8 my-auto">
                  {{ TemplatePartitionPortRange }}
                </dd>
              </dl>
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>

      <!-- Port ID Modal -->
      <modal-edit-template-port-id
        v-if="PartitionType == 'connectable'"
        :Context="Context"
        :TemplateData="TemplateData"
        :TemplateFaceSelected="TemplateFaceSelected"
        :PartitionAddressSelected="PartitionAddressSelected"
        :PreviewPortID="PreviewPortID"
        v-on:TemplatePartitionPortFormatFieldSelected="$emit('TemplatePartitionPortFormatFieldSelected', $event)"
        v-on:TemplatePartitionPortFormatValueUpdated="$emit('TemplatePartitionPortFormatValueUpdated', $event)"
        v-on:TemplatePartitionPortFormatTypeUpdated="$emit('TemplatePartitionPortFormatTypeUpdated', $event)"
        v-on:TemplatePartitionPortFormatCountUpdated="$emit('TemplatePartitionPortFormatCountUpdated', $event)"
        v-on:TemplatePartitionPortFormatOrderUpdated="$emit('TemplatePartitionPortFormatOrderUpdated', $event)"
        @TemplatePartitionPortFormatFieldMove="$emit('TemplatePartitionPortFormatFieldMove', $event)"
        v-on:TemplatePartitionPortFormatFieldCreate="$emit('TemplatePartitionPortFormatFieldCreate', $event)"
        v-on:TemplatePartitionPortFormatFieldDelete="$emit('TemplatePartitionPortFormatFieldDelete', $event)"
      />

    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import ModalEditTemplatePortId from './ModalEditTemplatePortId.vue'

export default {
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BForm,
    BButton,
    BFormInput,
    BFormSelect,
    BFormCheckbox,
    BCardText,

    ModalEditTemplatePortId,
  },
  directives: {
    Ripple,
  },
  props: {
    TemplateData: {type: Object},
    CategoryData: {type: Array},
    ObjectData: {type: Object},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    TemplatePartitionPortRange: {type: String},
    PreviewPortID: {type: String},
  },
  data () {
    return {
    }
  },
  computed: {
    ObjectName: {
      get() {

        const vm = this
        const Object = vm.GetObject()

        if(typeof Object !== 'undefined') {
          return Object.name
        } else {
          return '-'
        }
      },
      set(value) {

        return true
      }
    },
    TemplateName: {
      get() {

        const vm = this
        const Template = vm.GetTemplate()

        if(typeof Template !== 'undefined') {
          return Template.name
        } else {
          return '-'
        }
      },
      set(value) {

        return true
      }
    },
    TemplateCategoryID: {
      get() {

        const vm = this
        const Template = vm.GetTemplate()

        if(typeof Template !== 'undefined') {
          return Template.category_id
        } else {
          return '-'
        }
      },
      set(value) {

        return true
      }
    },
    PartitionType: function() {

      const vm = this
      const Partition = vm.GetPartition()

      if(Partition) {
        
        return Partition.type
      } else {
        return '-'
      }
    },
  },
  methods: {
    GetObjectIndex: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);

      return ObjectIndex
    },
    GetTemplateIndex: function() {

      const vm = this
      const Context = vm.Context
      const TemplateID = vm.PartitionAddressSelected[Context].template_id
      const TemplateIndex = vm.TemplateData[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    GetObject: function() {

      const vm = this
      const Context = vm.Context
      const ObjectIndex = vm.GetObjectIndex()
      const Object = vm.ObjectData[Context][ObjectIndex]

      return Object
    },
    GetTemplate: function() {

      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.GetTemplateIndex()
      const Template = vm.TemplateData[Context][TemplateIndex]

      return Template
    },
    GetPartition: function() {

      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context
      const TemplateIndex = vm.GetTemplateIndex()
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFaceSelected]

      if(TemplateIndex !== -1) {

        // Store variables
        let WorkingPartition = TemplateData[Context][TemplateIndex].blueprint[TemplateFaceSelected]
        let Partition

        PartitionAddress.forEach(function(PartitionAddressIndex){
          Partition = WorkingPartition[PartitionAddressIndex]
          WorkingPartition = WorkingPartition[PartitionAddressIndex].children
        })

        return Partition
      } else {

        return false
      }

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
  },
}
</script>
<template>
    <!-- Template edit modal -->
    <b-modal
      id="modal-templates-edit"
      title="Edit Template"
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

              <!-- Name -->
              <dl class="row">
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
              <dl class="row">
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
    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'

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
  },
  data () {
    return {
    }
  },
  computed: {
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
        console.log('Debug (TemplateType): '+Partition.type)
        return Partition.type
      } else {
        return '-'
      }
    },
  },
  methods: {
    GetTemplateIndex: function() {

      const vm = this
      const Context = vm.Context
      const TemplateID = vm.PartitionAddressSelected[Context].template_id
      const TemplateIndex = vm.TemplateData[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
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
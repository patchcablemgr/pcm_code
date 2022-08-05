<template>
    <!-- Template edit modal -->
    <b-modal
      id="modal-edit-template-category"
      title="Edit"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>
          <b-card
            :title="ModalTitle"
          >
            <b-card-text>

              <b-form-select
                v-model="TemplateCategoryID"
                :options="CategoryOptions"
              />
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>

    </b-modal>
</template>

<script>
import {
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
} from 'bootstrap-vue'
import { PCM } from '../../mixins/PCM.js'

export default {
  mixins: [PCM],
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
  directives: {},
  props: {
    Context: {type: String},
    ModalTitle: {type: String},
    PartitionAddressSelected: {type: Object},
  },
  data () {
    return {
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
    TemplateCategoryID: {
      get() {
      
        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateIndex = vm.GetTemplateIndex(TemplateID)
        const Template = vm.Templates[Context][TemplateIndex]

        if(typeof Template !== 'undefined') {
          return Template.category_id
        } else {
          return '-'
        }
      },
      set(newValue){

        const vm = this
        const Context = vm.Context
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const TemplateID = Template.id
        const URL = '/api/templates/'+TemplateID

        Template.category_id = newValue
        vm.$http.patch(URL, Template).then(response => {
          vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:'template', data:response.data})
        }).catch(error => {
          vm.DisplayError(error)
        })
      }
    },
    CategoryOptions: function() {

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
  },
  methods: {
  },
}
</script>
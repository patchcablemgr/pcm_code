<template>
    <!-- Template edit modal -->
    <b-modal
      id="modal-edit-template-name"
      title="Edit"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>
          <b-card
            title="Template Names"
          >
            <b-card-text>

              <b-form-input
                v-model="TemplateName"
              />
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>

    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'

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
    TemplateName: {
      get() {

        const Context = this.Context
        const Template = this.GetTemplateSelected(Context)
        const ReturnData = (Template) ? Template.name : ''

        return ReturnData
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
        const Template = JSON.parse(JSON.stringify(vm.Templates[Context][TemplateIndex]))
        const TemplateID = Template.id
        const URL = '/api/templates/'+TemplateID

        Template.name = newValue
        vm.$http.patch(URL, Template).then(response => {
          vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:'template', data:response.data})
        }).catch(error => {
          vm.DisplayError(error)
        })
      }
    },
  },
  methods: {
  },
}
</script>
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
            :title="ModalTitle"
          >
            <b-card-text>

              <b-form-input
                v-model="ComputedNameValue"
                @change=" $emit('TemplateNameEdited', {'name': $event}) "
              />
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>

    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'

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
  directives: {},
  props: {
    ModalTitle: {type: String},
    NameValue: {type: String},
  },
  data () {
    return {
    }
  },
  computed: {
    ComputedNameValue: {
      get() {

        const vm = this
        return vm.NameValue
      },
      set() {

        return true
      }
    },
  },
  methods: {
  },
}
</script>
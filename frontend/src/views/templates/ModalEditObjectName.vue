<template>
    <!-- Template edit modal -->
    <b-modal
      id="modal-edit-object-name"
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
                debounce="500"
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
    ModalTitle: {type: String},
    PartitionAddressSelected: {type: Object},
  },
  data () {
    return {
    }
  },
  computed: {
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    ComputedNameValue: {
      get() {

        const vm = this
        const Context = vm.Context
        const Object = vm.GetObjectSelected(Context)

        return Object.name
      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        const Object = vm.GetObjectSelected(Context)
        Object.name = newValue
        const ObjectID = Object.id
        const URL = '/api/objects/'+ObjectID

        vm.$http.patch(URL, Object).then(response => {
          vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:Context, data:response.data})

        }).catch(error => { vm.DisplayError(error) })
      }
    },
  },
  methods: {
  },
}
</script>
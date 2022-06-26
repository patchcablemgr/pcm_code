<template>
    <!-- Template edit modal -->
    <b-modal
      id="modal-edit-license-key"
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
                v-model="LicenseKey"
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
    ModalTitle: {type: String},
  },
  data () {
    return {}
  },
  computed: {
    LicenseKey: {
      get() {

        return 'abc123'
      },
      set(newValue) {
        console.log(newValue)
        /*
        const vm = this
        const Context = vm.Context
        const Object = vm.GetObjectSelected(Context)
        Object.name = newValue
        const ObjectID = Object.id
        const URL = '/api/objects/'+ObjectID

        vm.$http.patch(URL, Object).then(response => {
          vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:Context, data:response.data})

        }).catch(error => { vm.DisplayError(error) })
        */
      }
    },
  },
  methods: {
  },
}
</script>
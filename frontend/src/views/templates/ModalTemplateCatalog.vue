<template>
    <!-- Template edit modal -->
    <b-modal
      :id="ModalID"
      title="Template Catalog"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>
            <component-templates
              Context="catalog"
              :TemplateFaceSelected="TemplateFaceSelected"
              :PartitionAddressSelected="PartitionAddressSelected"
              :PartitionAddressHovered="PartitionAddressHovered"
              @SetTemplateFaceSelected=" $emit('SetTemplateFaceSelected', $event) "
            />
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

    ComponentTemplates: () => import('./ComponentTemplates.vue')
  },
  directives: {},
  props: {
    ModalID: {type: String},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  data () {
    return {
    }
  },
  computed: {
  },
  methods: {
  },
  mounted() {
    const vm = this

    vm.$root.$on('bv::modal::shown', (bvEvent, modalId) => {

      // Only trigger on intended modal
      if(modalId == vm.ModalID) {

      }
    })
  },
  beforeDestroy(){

    const vm = this

    // Clean up
    vm.$root.$off('bv::modal::shown')
  }
}
</script>
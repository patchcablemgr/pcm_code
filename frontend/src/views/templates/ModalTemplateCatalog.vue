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
        <b-col>
          <component-template-object-details
            CardTitle="Template Details"
						Context="catalog"
						:TemplateFaceSelected="TemplateFaceSelected"
						:PartitionAddressSelected="PartitionAddressSelected"
            :DetailsAreEditable="true"
            @SetPartitionAddressSelected=" $emit('SetPartitionAddressSelected', $event) "
            @SetTemplateFaceSelected=" $emit('SetTemplateFaceSelected', $event) "
					/>
        </b-col>
      </b-row>

    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormSelect, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'
import ComponentTemplateObjectDetails from '@/views/templates/ComponentTemplateObjectDetails.vue'

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

    ComponentTemplates: () => import('./ComponentTemplates.vue'),
    ComponentTemplateObjectDetails,
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
    const Context = vm.Context

    vm.$root.$on('bv::modal::shown', (bvEvent, modalId) => {

      // Only trigger on intended modal
      if(modalId == vm.ModalID) {

        // Fetch catalog categories
        vm.$http.get('/api/catalog/categories')
        .then(response => {
          vm.$store.commit('pcmCategories/SET_Categories', {pcmContext:Context, data:response.data})
          vm.$store.commit('pcmCategories/SET_Ready', {pcmContext:Context, ReadyState:true})
        }).catch(error => {
          vm.DisplayError(error)
        })

        // Fetch catalog templates
        vm.$http.get('/api/catalog/templates')
        .then(response => {

          // Store template data
          vm.$store.commit('pcmTemplates/SET_Templates', {pcmContext:Context, data:response.data})

          // Store pseudo object data
          response.data.forEach(function(template) {
            vm.GeneratePseudoData(Context, template)
          })

          // Mark as ready
          vm.$store.commit('pcmObjects/SET_Ready', {pcmContext:Context, ReadyState:true})
          vm.$store.commit('pcmTemplates/SET_Ready', {pcmContext:Context, ReadyState:true})
        }).catch(error => {
          vm.DisplayError(error)
        })
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
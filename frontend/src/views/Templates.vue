<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid="xs">
      <b-row
        cols="1"
        cols-md="2"
        cols-xl="3"
      >
        <b-col>
          <b-card
            title="Properties"
          >
            <b-card-body>
              <templates-form
                Context="workspace"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionTypeDisabled="PartitionTypeDisabled"
                @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
              />
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>

          <b-card
            :class="{ pcm_sticky: IsSticky, pcm_scroll: IsSticky }"
          >
            <b-card-title  class="mb-0">
              Preview
            </b-card-title>
            <b-card-body
              v-if=" PreviewDisplay == 'cabinet' "
            >
              <div class="demo-inline-spacing">
                
                <b-form-radio
                  v-model="TemplateFaceSelected.workspace"
                  value="front"
                  :disabled="TemplateFaceToggleIsDisabled"
                >Front
                </b-form-radio>

                <b-form-radio
                  v-model="TemplateFaceSelected.workspace"
                  value="rear"
                  :disabled="TemplateFaceToggleIsDisabled"
                >Rear
                </b-form-radio>

                <b-form-checkbox
                  v-model="IsSticky"
                  switch
                >Sticky
                </b-form-checkbox>
              </div>
              <component-cabinet
                Context="workspace"
                :TemplateFaceSelected="TemplateFaceSelected"
                :PartitionAddressHovered="PartitionAddressHovered"
              />
            </b-card-body>
            <b-card-body
              v-if=" PreviewDisplay == 'insertInstructions' "
            >
              Select enclosure
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>

          <component-template-object-details
            CardTitle="Template Details"
						Context="template"
						:TemplateFaceSelected="TemplateFaceSelected"
            :DetailsAreEditable="true"
            @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
					/>

          <component-templates
            Context="template"
            :TemplateFaceSelected="TemplateFaceSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @SetTemplateFaceSelected="SetTemplateFaceSelected($event)"
          />

        </b-col>
      </b-row>
    </b-container>

    <!-- Toast -->
    <toast-general/>

  </div>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BCardTitle, BCardBody, BCardText, BFormCheckbox, BFormRadio, } from 'bootstrap-vue'
import TemplatesForm from './templates/TemplatesForm.vue'
import ToastGeneral from './templates/ToastGeneral.vue'
import ComponentCabinet from './templates/ComponentCabinet.vue'
import ComponentTemplateObjectDetails from './templates/ComponentTemplateObjectDetails.vue'
import ComponentTemplates from './templates/ComponentTemplates.vue'
import ModalTemplatesEdit from './templates/ModalTemplatesEdit.vue'
import { PCM } from '@/mixins/PCM.js'

const IsSticky = false
const TemplateFaceSelected = {
  'workspace': 'front',
  'template': 'front',
  'catalog': 'front',
}
const PartitionAddressHovered = {
  'workspace': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': false,
    'rear': false,
    'port_id': {
      'front': null,
      'rear': null,
    }
  },
  'template': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': false,
    'rear': false,
    'port_id': {
      'front': null,
      'rear': null,
    }
  },
  'catalog': {
    'object_id': null,
    'object_face': null,
    'template_id': null,
    'front': false,
    'rear': false,
    'port_id': {
      'front': null,
      'rear': null,
    }
  }
}

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardTitle,
    BCardBody,
    BCardText,
    BFormCheckbox,
    BFormRadio,

    TemplatesForm,
    ToastGeneral,
    ComponentCabinet,
    ComponentTemplateObjectDetails,
    ComponentTemplates,
    ModalTemplatesEdit,
  },
  data() {
    return {
      IsSticky,
      TemplateFaceSelected,
      PartitionAddressHovered,
    }
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.CategoriesReady.template,
        vm.CategoriesReady.workspace,
        vm.TemplatesReady.template,
        vm.TemplatesReady.workspace,
        vm.ObjectsReady.template,
        vm.ObjectsReady.workspace,
        vm.LocationsReady.workspace,
      ]

      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    CategoriesReady: function() {
      return this.$store.state.pcmCategories.CategoriesReady
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    LocationsReady: function() {
      return this.$store.state.pcmLocations.LocationsReady
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    PreviewDisplay: function(){

      const vm = this
      const Template = vm.GetTemplateSelected('workspace')
      let PreviewDisplay = 'cabinet'

      if(Template.type == 'insert') {

        if(Template.hasOwnProperty('clone')) {

          PreviewDisplay = 'cabinet'
        } else {

          const Partition = vm.GetPartitionSelected('template')
          if(Partition) {
            if(Partition.type == 'enclosure') {
              PreviewDisplay = 'cabinet'
            } else {
              PreviewDisplay = 'insertInstructions'
            }
          } else {
            PreviewDisplay = 'insertInstructions'
          }
        }
      }

      return PreviewDisplay
    },
    TemplateFaceToggleIsDisabled: function() {

      const vm = this
      const Context = 'workspace'
      const Template = vm.GetTemplateSelected(Context)
      const MountConfig = Template.mount_config
      const TemplateType = Template.type

      return MountConfig === '2-post'  || TemplateType === 'insert'
    },
    PartitionTypeDisabled: function() {
      // Store variables
      const vm = this
      const Context = 'workspace'
      const Face = vm.TemplateFaceSelected[Context]
      const PartitionAddress = vm.StateSelected[Context].partition[Face]

      return !PartitionAddress.length
    },
  },
  methods: {
    SetTemplateFaceSelected: function({Context, Face}) {

      const vm = this
      vm.TemplateFaceSelected[Context] = Face
    },
  },
  mounted() {},
}
</script>

<style>

</style>

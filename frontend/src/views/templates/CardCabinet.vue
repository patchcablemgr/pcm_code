<!-- Template/Object Details -->

<template>

  <b-card
    :class="{ pcm_sticky: IsSticky, pcm_scroll: IsSticky }"
  >
    <b-card-title class="mb-0">
      <div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing" style="align-content:start">
          {{CardTitle}}
        </div>
        <b-form-group
          class="w-25"
          label-for="select-face"
          description="Face"
        >
          <b-form-select
            id="select-face"
            class="m-0"
            v-model="SelectedCabinetFace"
            :options="SelectedCabinetFaceOptions"
            :disabled="CabinetFaceSelectIsDisabled"
          />
        </b-form-group>
        <b-form-group
          class="w-25"
          label-for="select-view"
          description="View"
        >
          <b-form-select
          id="select-view"
            class="m-0"
            v-model="TemplateView"
            :options="TemplateViewOptions"
          />
        </b-form-group>
        <b-form-group
          class="w-25"
          label-for="toggle-sticky"
          description="Sticky"
        >
        <b-form-checkbox
          id="toggle-sticky"
          class="m-0"
          v-model="IsSticky"
          switch
        />
        </b-form-group>
      </div>
    </b-card-title>

    <b-card-body
      v-if=" PreviewDisplay == 'none' "
    >
      Please select a cabinet from the Locations and Cabinets tree.
    </b-card-body>

    <b-card-body
      v-if=" PreviewDisplay == 'insertInstructions' "
    >
      Select enclosure
    </b-card-body>

    <b-card-body
      v-if=" PreviewDisplay == 'cabinet' "
    >
      <component-cabinet
        :Context="Context"
        :SelectedCabinetFace="SelectedCabinetFace"
        :ObjectsAreDraggable="ObjectsAreDraggable"
        :TemplateView="TemplateView"
      />
    </b-card-body>
  </b-card>

</template>

<script>
import {
  BCard,
  BCardTitle,
  BCardBody,
  BButton,
  BFormGroup,
  BFormRadio,
  BFormCheckbox,
  BFormSelect,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { PCM } from '@/mixins/PCM.js'
import ComponentCabinet from '@/views/templates/ComponentCabinet.vue'

const IsSticky = false
const SelectedCabinetFace = 'front'
const SelectedCabinetFaceOptions = [
  {'value': 'front', 'text': 'Front'},
  {'value': 'rear', 'text': 'Rear'},
]
const TemplateView = 'template'
const TemplateViewOptions = [
  {'value': 'template', 'text': 'Template'},
  {'value': 'image', 'text': 'Image'},
]

export default {
  mixins: [PCM],
  components: {
    BCard,
    BCardTitle,
    BCardBody,
    BButton,
    BFormGroup,
    BFormRadio,
    BFormCheckbox,
    BFormSelect,

    ComponentCabinet,
  },
	directives: {
    Ripple,
	},
  props: {
    Context: {type: String},
    CardTitle: {type: String},
    PreviewDisplay: {type: String},
    ObjectsAreDraggable: {type: Boolean},
    CabinetFaceSelectIsDisabled: {type: Boolean},
  },
  data() {
    return {
      IsSticky,
      SelectedCabinetFace,
      SelectedCabinetFaceOptions,
      TemplateView,
      TemplateViewOptions,
    }
  },
  watch: {
    SelectedCabinetFace(newValue, oldValue) {
      const vm = this
      const Context = vm.Context
      vm.$emit('SetTemplateFaceSelected', {Context, Face:newValue})
    },
    CabinetFaceSelectIsDisabled(newValue, oldValue) {
      const vm = this
      if(newValue) {
        vm.SelectedCabinetFace = 'front'
      }
    }
  },
  computed: {},
  methods: {},
}
</script>
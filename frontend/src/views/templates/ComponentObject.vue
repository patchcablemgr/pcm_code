<template>
<!--
  'background-color': GetObjectCategoryColor(ObjectID, Context),
  'background-image': 'url(data:image/gif;base64,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)',
  'background-size': '100% 100%',
-->
  <div
    :draggable="!IsPseudoObject && ObjectsAreDraggable"
    @dragstart.stop="StartDrag({ context: Context, object_id: ObjectID, template_id: GetTemplateID(ObjectID), template_face: CabinetFace, object_face: ObjectFace }, $event)"
    :class="{
      pcm_template_partition_selected: PartitionIsSelected({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': InitialPartitionAddress, 'PortID': null}),
      pcm_template_partition_hovered: PartitionIsHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': InitialPartitionAddress, 'PortID': null}),
    }"
    :style="ObjectStyle"
    @click.stop=" PartitionClicked({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': InitialPartitionAddress, 'PortID': null}) "
    @mouseover.stop=" PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': InitialPartitionAddress, 'PortID': null, 'HoverState': true}) "
    @mouseleave.stop=" PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': InitialPartitionAddress, 'PortID': null, 'HoverState': false}) "
  >
    <component-template
      :InitialPartitionAddress=" InitialPartitionAddress "
      :Context="Context"
      :ObjectID="ObjectID"
      :CabinetFace="CabinetFace"
      :ObjectFace="ObjectFace"
      :ObjectsAreDraggable="ObjectsAreDraggable"
      :SelectedPortInfo="SelectedPortInfo"
      :HoveredPortInfo="HoveredPortInfo"
    />
  </div>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import ComponentTemplate from './ComponentTemplate.vue'
import { PCM } from '../../mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,

    ComponentTemplate,
  },
  props: {
    InitialPartitionAddress: {type: Array},
    Context: {type: String},
    ObjectID: {},
    CabinetFace: {type: String},
    ObjectsAreDraggable: {type: Boolean},
    TemplateView: {type: String},
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
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    StateHovered() {
      return this.$store.state.pcmState.Hovered
    },
    IsPseudoObject: function() {

      const vm = this
      const ObjectID = vm.ObjectID
      const TemplateID = String(vm.GetTemplateID(ObjectID))

      return TemplateID.includes('pseudo')

    },
    ObjectFace: function() {

      const vm = this
      const ObjectFace = vm.GetObjectFace(vm.ObjectID, vm.CabinetFace)
      
      return ObjectFace
    },
    SelectedPortInfo() {
      const vm = this
      const Context = vm.Context
      const SelectedObjectID = vm.StateSelected[Context]['object_id']
      const SelectedObjectFace = vm.StateSelected[Context]['object_face']

      if(vm.ObjectID == SelectedObjectID && vm.ObjectFace == SelectedObjectFace) {
        return vm.StateSelected[Context]
      } else {
        return false
      }
    },
    HoveredPortInfo() {
      const vm = this
      const Context = vm.Context
      const HoveredObjectID = vm.StateHovered[Context]['object_id']
      const HoveredObjectFace = vm.StateHovered[Context]['object_face']

      if(vm.ObjectID == HoveredObjectID && vm.ObjectFace == HoveredObjectFace) {
        return vm.StateHovered[Context]
      } else {
        return false
      }
    },
    ObjectStyle() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID
      const TemplateView = vm.TemplateView
      const ObjectFace = vm.ObjectFace

      let ObjectStyle = {
        'height': '100%',
      }

      if(TemplateView == 'template') {

        ObjectStyle['background-color'] = vm.GetObjectCategoryColor(ObjectID, Context)

      } else if(TemplateView == 'image') {

        const Template = vm.GetTemplate({ObjectID, Context})
        const TemplateImgData = Template['img_'+ObjectFace]

        if(TemplateImgData) {
          ObjectStyle['background-image'] = 'url('+TemplateImgData+')'
          ObjectStyle['background-size'] = '100% 100%'
        } else {
          ObjectStyle['background-color'] = '#808080FF'
        }
      }

      return ObjectStyle
    }
  },
  methods: {
    StartDrag: function(TransferData, e) {
      e.dataTransfer.setData('context', TransferData.context)
      e.dataTransfer.setData('object_id', TransferData.object_id)
      e.dataTransfer.setData('template_id', TransferData.template_id)
      e.dataTransfer.setData('template_face', TransferData.template_face)
      e.dataTransfer.setData('object_face', TransferData.object_face)
    },
  },
  mounted() {
  }
}
</script>
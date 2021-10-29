<template>

  <div
    :draggable="!IsPseudoObject"
    @dragstart.stop="StartDrag({ context: Context, object_id: ObjectID, template_id: GetTemplateID(ObjectID), template_face: TemplateFaceSelected[Context] }, $event)"
    :templateID="GetTemplateID(ObjectID)"
    :class="{
      pcm_template_partition_selected: PartitionIsSelected(),
      pcm_template_partition_hovered: PartitionIsHovered(),
    }"
    :style="{
      'background-color': TemplateColor(ObjectID),
      'height': '100%',
    }"
    @click.stop=" $emit('PartitionClicked', {'Context': Context, 'ObjectID': ObjectID, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress}) "
    @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'ObjectID': ObjectID, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress, 'HoverState': true}) "
    @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'ObjectID': ObjectID, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress, 'HoverState': false}) "
  >
    <component-template
      :ObjectData="ObjectData"
      :TemplateData="TemplateData"
      :CategoryData="CategoryData"
      :TemplateRUSize="TemplateRUSize"
      :InitialPartitionAddress=" InitialPartitionAddress "
      :Context="Context"
      :ObjectID="ObjectID"
      :TemplateFaceSelected="TemplateFaceSelected"
      :PartitionAddressSelected="PartitionAddressSelected"
      :PartitionAddressHovered="PartitionAddressHovered"
      @PartitionClicked=" $emit('PartitionClicked', $event) "
      @PartitionHovered=" $emit('PartitionHovered', $event) "
      @ObjectDropped=" $emit('ObjectDropped', $event) "
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
    ObjectData: {type: Object},
    TemplateData: {type: Object},
    CategoryData: {type: Array},
    TemplateRUSize: {type: Number},
    InitialPartitionAddress: {type: Array},
    Context: {type: String},
    ObjectID: {},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  computed: {
    IsPseudoObject: function() {

      const vm = this
      const ObjectID = vm.ObjectID
      const TemplateID = String(vm.GetTemplateID(ObjectID))

      return TemplateID.includes('pseudo')

    },
  },
  methods: {
    StartDrag: function(TransferData, e) {
      e.dataTransfer.setData('context', TransferData.context)
      e.dataTransfer.setData('object_id', TransferData.object_id)
      e.dataTransfer.setData('template_id', TransferData.template_id)
      e.dataTransfer.setData('template_face', TransferData.template_face)
    },
    GetPreviewData: function(ObjectID) {

      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get object index
      const ObjectIndex = vm.GetObjectIndex(ObjectID)

      // Get template index
      const TemplateID = vm.ObjectData[Context][ObjectIndex].template_id
      const TemplateIndex = vm.GetTemplateIndex(TemplateID)

      // Get template
      const ObjectPreviewData = TemplateData[Context][TemplateIndex]

      // Return template
      return ObjectPreviewData
    },
    TemplateColor: function(ObjectID) {

      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context
      let TemplateColor

      // Get Template
      const TemplateID = vm.GetTemplateID(ObjectID)
      const TemplateIndex = vm.GetTemplateIndex(TemplateID)
      const Template = TemplateData[Context][TemplateIndex]

      if (Template.hasOwnProperty('pseudo') && !Template.hasOwnProperty('pseudoParentTemplate')) {
        TemplateColor = '#FFFFFF00'
      } else {

        // Get category index
        const ObjectCategoryID = Template.category_id
        const ObjectCategoryIndex = vm.CategoryData.findIndex((category) => category.id == ObjectCategoryID);

        // Get category
        const ObjectCategoryData = vm.CategoryData[ObjectCategoryIndex]
        TemplateColor = ObjectCategoryData.color
      }

      // Return category
      return TemplateColor
    },
  },
  mounted() {
  }
}
</script>
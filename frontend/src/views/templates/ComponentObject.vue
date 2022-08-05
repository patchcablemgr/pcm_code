<template>

  <div
    :draggable="!IsPseudoObject && ObjectsAreDraggable"
    @dragstart.stop="StartDrag({ context: Context, object_id: ObjectID, template_id: GetTemplateID(ObjectID), template_face: CabinetFace, object_face: ObjectFace }, $event)"
    :class="{
      pcm_template_partition_selected: PartitionIsSelected({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': InitialPartitionAddress}),
      pcm_template_partition_hovered: PartitionIsHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': InitialPartitionAddress}),
    }"
    :style="{
      'background-color': TemplateColor(ObjectID),
      'height': '100%',
    }"
    @click.stop=" PartitionClicked({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress, 'PortID': null}) "
    @mouseover.stop=" PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress, 'HoverState': true}) "
    @mouseleave.stop=" PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': InitialPartitionAddress, 'HoverState': false}) "
  >
    <component-template
      :TemplateRUSize="TemplateRUSize"
      :InitialPartitionAddress=" InitialPartitionAddress "
      :Context="Context"
      :ObjectID="ObjectID"
      :CabinetFace="CabinetFace"
      :ObjectFace="ObjectFace"
      :PartitionAddressSelected="PartitionAddressSelected"
      :PartitionAddressHovered="PartitionAddressHovered"
      :ObjectsAreDraggable="ObjectsAreDraggable"
      @InsertObjectDropped=" $emit('InsertObjectDropped', $event) "
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
    TemplateRUSize: {type: Number},
    InitialPartitionAddress: {type: Array},
    Context: {type: String},
    ObjectID: {},
    CabinetFace: {type: String},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
    ObjectsAreDraggable: {type: Boolean},
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
  },
  methods: {
    StartDrag: function(TransferData, e) {
      e.dataTransfer.setData('context', TransferData.context)
      e.dataTransfer.setData('object_id', TransferData.object_id)
      e.dataTransfer.setData('template_id', TransferData.template_id)
      e.dataTransfer.setData('template_face', TransferData.template_face)
      e.dataTransfer.setData('object_face', TransferData.object_face)
    },
    TemplateColor: function(ObjectID) {

      // Initial variables
      const vm = this
      const Context = vm.Context
      let TemplateColor = '#FFFFFF00'

      // Get Template
      const TemplateID = vm.GetTemplateID(ObjectID, Context)
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)

      if (TemplateIndex !== -1) {

        const Template = vm.Templates[Context][TemplateIndex]
        if (!Template.id.toString().includes('pseudo')) {

          // Get category index
          const ObjectCategoryID = Template.category_id
          const ObjectCategoryIndex = vm.Categories[Context].findIndex((category) => category.id == ObjectCategoryID)

          // Get category
          console.log('ObjectCategoryIndex: '+ObjectCategoryIndex)
          const Category = vm.Categories[Context][ObjectCategoryIndex]
          console.log('Category: '+Category)
          TemplateColor = Category.color
        }
      }

      // Return category
      return TemplateColor
    },
  },
  mounted() {
  }
}
</script>
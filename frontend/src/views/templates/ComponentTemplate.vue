<template>

  <div
    :class="TemplateClasses"
  >
    
    <div
      v-for="(Partition, PartitionIndex) in GetPartitionCollection()"
      :key=" GetPartitionAddress(PartitionIndex).join('-') "
      class=" pcm_template_partition_box "
      :class="{
        pcm_template_partition_selected: PartitionIsSelected(PartitionIndex),
        pcm_template_partition_hovered: PartitionIsHovered(PartitionIndex),
      }"
      :style="{ 'flex-grow': GetPartitionFlexGrow(Partition.units, PartitionIndex) }"
      @click.stop=" $emit('PartitionClicked', {'Context': Context, 'ObjectID': ObjectID, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetPartitionAddress(PartitionIndex)}) "
      @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'ObjectID': ObjectID, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetPartitionAddress(PartitionIndex), 'HoverState': true}) "
      @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'ObjectID': ObjectID, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetPartitionAddress(PartitionIndex), 'HoverState': false}) "
    >
      <!-- Generic partition -->
      <ComponentTemplate
        v-if=" Partition.type == 'generic' "
        :TemplateRUSize="TemplateRUSize"
        :InitialPartitionAddress="GetPartitionAddress(PartitionIndex)"
        :Context="Context"
        :ObjectID="ObjectID"
        :TemplateFaceSelected="TemplateFaceSelected"
        :PartitionAddressSelected="PartitionAddressSelected"
        :PartitionAddressHovered="PartitionAddressHovered"
        @PartitionClicked=" $emit('PartitionClicked', $event) "
        @PartitionHovered=" $emit('PartitionHovered', $event) "
      />

      <!-- Connectable partition -->
      <div
        v-if=" Partition.type == 'connectable' "
        class=" pcm_template_connectable_container pcm_template_partition_border "
        :style="{
          'grid-template-rows': GetPartitionGrid(Partition.port_layout.rows),
          'grid-template-columns': GetPartitionGrid(Partition.port_layout.cols),
          'grid-template-areas': GetPartitionAreas(Partition.port_layout.rows, Partition.port_layout.cols),
        }"
      >
        <div
          v-for=" portIndex in (Partition.port_layout.rows * Partition.port_layout.cols) "
          :key=" portIndex "
          class=" pcm_template_connectable_port_unk "
          :style="{ 'grid-area': 'area'+(portIndex-1) }"
        >
        </div>
      </div>

      <!-- Enclosure partition -->
      <div
        v-if=" Partition.type == 'enclosure' "
        :class="TemplateEnclosureClasses"
        :style="{
          'grid-template-rows': GetPartitionGrid(Partition.enc_layout.rows),
          'grid-template-columns': GetPartitionGrid(Partition.enc_layout.cols),
          'grid-template-areas': GetPartitionAreas(Partition.enc_layout.rows, Partition.enc_layout.cols),
        }"
      >
        <div
          v-for=" encIndex in (Partition.enc_layout.rows * Partition.enc_layout.cols) "
          :key=" encIndex "
          :class="TemplateEnclosureAreaClasses"
          :style="{ 'grid-area': 'area'+(encIndex-1) }"
        >
          <component-object
            v-if="GetEnclosureInsertID(encIndex-1, Partition.enc_layout.cols)"
            :TemplateRUSize="TemplateRUSize"
            :InitialPartitionAddress=[]
            :Context="Context"
            :ObjectID="GetEnclosureInsertID(encIndex-1, Partition.enc_layout.cols)"
            :TemplateFaceSelected="TemplateFaceSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @PartitionClicked=" $emit('PartitionClicked', $event) "
            @PartitionHovered=" $emit('PartitionHovered', $event) "
            @InsertObjectDropped=" $emit('InsertObjectDropped', $event) "
          />
          <div
            v-else
            @drop="HandleDrop(ObjectID, 'front', GetPartitionAddress(PartitionIndex), GetEnclosureAddress(encIndex-1, Partition.enc_layout.cols), $event)"
            @dragover.prevent
            @dragenter.prevent
            style="height:100%"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'
import { PCM } from '../../mixins/PCM.js'

export default {
  name: "ComponentTemplate",
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    ComponentObject: () => import('./ComponentObject.vue'),
  },
  props: {
    TemplateRUSize: {type: Number},
    InitialPartitionAddress: {type: Array},
    Context: {type: String},
    ObjectID: {},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
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
    TemplateClasses: function() {

      const vm = this
      const PartitionAddress = vm.GetPartitionAddress(0)
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
      const Template = vm.GetTemplate()
      const isPseudo = Template.hasOwnProperty("pseudo")
      const isPseudoParentTemplate = Template.hasOwnProperty("pseudoParentTemplate")

      return {
        "pcm_cabinet_object_container": true,
        "pcm_template_partition_layered": (isPseudo && !isPseudoParentTemplate) ? false : true,
        "pcm_template_partition_border": (isPseudo && !isPseudoParentTemplate) ? false : true,
        "pcm_template_partition_row": PartitionDirection == 'row',
        "pcm_template_partition_col": PartitionDirection == 'col',
      }
    },
    TemplateEnclosureClasses: function() {

      const vm = this
      const Template = vm.GetTemplate()
      const isPseudo = Template.hasOwnProperty("pseudo")
      const isPseudoParentTemplate = Template.hasOwnProperty("pseudoParentTemplate")

      return {
        "pcm_template_enclosure_container": true,
        "pcm_template_enclosure_container_background": (isPseudo && !isPseudoParentTemplate) ? false : true,
      }
    },
    TemplateEnclosureAreaClasses: function() {

      const vm = this
      const Template = vm.GetTemplate()
      const isPseudo = Template.hasOwnProperty("pseudo")
      const isPseudoParentTemplate = Template.hasOwnProperty("pseudoParentTemplate")

      return {
        "pcm_template_enclosure_area": true,
        "pcm_template_enclosure_area_border": (isPseudo && !isPseudoParentTemplate) ? false : true,
      }
    },
  },
  methods: {
    GetVisibleObjectFace: function() {

      // Initial variables
      const vm = this
      const Context = vm.Context
      const VisibleCabinetFace = vm.TemplateFaceSelected[Context]
      const ObjectID = vm.ObjectID
      const ObjectIndex = PCM.GetObjectIndex()

      const VisibleObjectFace = ObjectID

      return VisibleObjectFace

    },
    GetPartitionCollection: function() {

      // Initial variables
      const vm = this
      const Template = vm.GetTemplate()
      const TemplateFaceSelected = vm.TemplateFaceSelected
      const Context = vm.Context
      const PartitionAddress = vm.InitialPartitionAddress

      let PartitionCollection = Template.blueprint[TemplateFaceSelected[Context]]
      PartitionAddress.forEach(function(PartitionAddressIndex, Depth){
        PartitionCollection = PartitionCollection[PartitionAddressIndex].children
      })

      return PartitionCollection

    },
    GetTemplate: function() {

      // Initial variables
      const vm = this
      const ObjectID = vm.ObjectID
      const Templates = vm.Templates
      const Context = vm.Context

      // Get template index
      const TemplateID = vm.GetTemplateID(ObjectID, Context)
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)

      // Get template
      const Template = Templates[Context][TemplateIndex]

      // Return template
      return Template
    },
    GetEnclosureInsertID: function(encIndex, encCols) {
      
      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID
      const InsertIndex = vm.Objects[Context].findIndex((object) => object.parent_id == ObjectID)
      let EnclosureInsertID = false
			
      if(InsertIndex !== -1) {
        const Insert = vm.Objects[Context][InsertIndex]
        const InsertParentEnclosureAddress = Insert.parent_enclosure_address
        const EnclosureAddress = vm.GetEnclosureAddress(encIndex, encCols)

        if(InsertParentEnclosureAddress[0] == EnclosureAddress[0] && InsertParentEnclosureAddress[1] == EnclosureAddress[1]) {
          EnclosureInsertID = Insert.id
        }
      }

      return EnclosureInsertID
    },
    GetEnclosureAddress: function(encIndex, encCols) {
      const row = Math.floor(encIndex / encCols)
      const col = encIndex - (row * encCols)

      return [row, col]
    },
    HandleDrop: function(ParentID, ParentFace, ParentPartitionAddress, ParentEnclosureAddress, event) {
      
      // Store dataa
      const vm = this
      const Context = event.dataTransfer.getData('context')
      const InsertObjectID = event.dataTransfer.getData('object_id')
      const TemplateID = event.dataTransfer.getData('template_id')
      const TemplateFace = event.dataTransfer.getData('template_face')

      // Validate dropped object template type
      const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
      const Template = vm.Templates[Context][TemplateIndex]
      const TemplateType = Template.type

      if(TemplateType == 'insert') {

        const data = {
          "drop_type": "enclosure",
          "context": Context,
          "parent_id": ParentID,
          "parent_face": ParentFace,
          "parent_partition_address": ParentPartitionAddress,
          "parent_enclosure_address": ParentEnclosureAddress,
          "object_id": InsertObjectID,
          "template_id": TemplateID,
          "template_face": TemplateFace,
        }

        vm.$emit('InsertObjectDropped', data )
      }
    },
    GetGlobalPartitionMax: function(Template, PartitionAddress) {

      const vm = this
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)

      // Get working max
      let WorkingMax
      if (PartitionDirection == 'row') {

        if (Template.insert_constraints !== null) {

          // Partition is an insert with constraints
          WorkingMax = Template.insert_constraints.part_layout.width
        } else {

          // Partition is standard
          WorkingMax = 24
        }
      } else {

        if (Template.insert_constraints !== null) {

          // Partition is an insert with constraints
          WorkingMax = Template.insert_constraints.part_layout.height
        } else {

          // Partition is standard
          WorkingMax = Template.ru_size * 2
        }
      }

      return WorkingMax

    },
    GetPartitionParentSize: function(PartitionAddress) {

      // Store variables
      const vm = this
      const Context = vm.Context
      const Face = vm.TemplateFaceSelected[Context]
      const Template = vm.GetTemplate()
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
      let WorkingMax = vm.GetGlobalPartitionMax(Template, PartitionAddress)
      let WorkingPartition = JSON.parse(JSON.stringify(Template.blueprint[Face]))
      
      PartitionAddress.pop()
      PartitionAddress.forEach(function(PartitionAddressIndex, Depth){
          let WorkingPartitionDirection = vm.GetPartitionDirection(new Array(Depth + 1))
          if(WorkingPartitionDirection == PartitionDirection) {
            WorkingMax = WorkingPartition[PartitionAddressIndex].units
          }
          WorkingPartition = WorkingPartition[PartitionAddressIndex].children
      })

      return WorkingMax
    },
    GetPartitionFlexGrow: function(PartitionUnits, PartitionIndex) {

      const vm = this
      const Context = vm.Context
      let PartitionFlexGrow
      const PartitionAddress = vm.GetPartitionAddress(PartitionIndex)
      const PartitionParentSize = vm.GetPartitionParentSize(PartitionAddress)

      PartitionFlexGrow = PartitionUnits / PartitionParentSize

      return PartitionFlexGrow
    },
  },
}
</script>
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
      @click.stop=" $emit('PartitionClicked', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetPartitionAddress(PartitionIndex)}) "
      @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetPartitionAddress(PartitionIndex), 'HoverState': true}) "
      @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetPartitionAddress(PartitionIndex), 'HoverState': false}) "
    >
      <!-- Generic partition -->
      <ComponentTemplate
        v-if=" Partition.type == 'generic' "
        :ObjectData="ObjectData"
        :TemplateData="TemplateData"
				:CategoryData="CategoryData"
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
            :ObjectData="ObjectData"
            :TemplateData="TemplateData"
            :CategoryData="CategoryData"
            :TemplateRUSize="TemplateRUSize"
            :InitialPartitionAddress=[]
            :Context="Context"
            :ObjectID="GetEnclosureInsertID(encIndex-1, Partition.enc_layout.cols)"
            :TemplateFaceSelected="TemplateFaceSelected"
            :PartitionAddressSelected="PartitionAddressSelected"
            :PartitionAddressHovered="PartitionAddressHovered"
            @PartitionClicked=" $emit('PartitionClicked', $event) "
            @PartitionHovered=" $emit('PartitionHovered', $event) "
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { BContainer, BRow, BCol, } from 'bootstrap-vue'

export default {
  name: "ComponentTemplate",
  components: {
    BContainer,
    BRow,
    BCol,
    ComponentObject: () => import('./ComponentObject.vue'),
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
    TemplateClasses: function() {

      const vm = this
      const PartitionAddress = vm.GetPartitionAddress()
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
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get template index
      const TemplateID = vm.GetTemplateID(ObjectID)
      const TemplateIndex = vm.GetTemplateIndex(TemplateID)

      // Get template
      const Template = TemplateData[Context][TemplateIndex]

      // Return template
      return Template
    },
    GetTemplateID: function(ObjectID) {

      const vm = this
      const Context = vm.Context
      const ObjectData = vm.ObjectData[Context]
      let TemplateID = 0

      const ObjectIndex = ObjectData.findIndex((object) => object.id == ObjectID )

      if(ObjectIndex !== -1) {
        const Object = ObjectData[ObjectIndex]
        TemplateID = Object.template_id
      }

      return TemplateID

    },
    GetTemplateIndex: function(TemplateID) {

      // Initial variables
      const vm = this
      const TemplateData = vm.TemplateData
      const Context = vm.Context

      // Get object index
      const TemplateIndex = TemplateData[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    GetEnclosureInsertID: function(encIndex, encCols) {
      
      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID
      const InsertIndex = vm.ObjectData[Context].findIndex((object) => object.parent_id == ObjectID)
      let EnclosureInsertID = false
			
      if(InsertIndex !== -1) {
        const Insert = vm.ObjectData[Context][InsertIndex]
        const InsertParentEnclosureAddress = Insert.parent_enc_addr
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
    PartitionIsSelected: function(PartitionIndex) {
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressSelected = vm.PartitionAddressSelected[Context][TemplateFaceSelected]
      const PartitionAddress = vm.GetPartitionAddress(PartitionIndex)
      const TemplateIDSelected = vm.PartitionAddressSelected[Context].template_id
      const TemplateID = vm.GetTemplateID(vm.ObjectID)
      let PartitionIsSelected = false

      if(PartitionAddressSelected.length === PartitionAddress.length && PartitionAddressSelected.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsSelected = true
      }
      return PartitionIsSelected
    },
    PartitionIsHovered: function(PartitionIndex) {
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressHovered = vm.PartitionAddressHovered[Context][TemplateFaceSelected]
      const PartitionAddress = vm.GetPartitionAddress(PartitionIndex)
      const TemplateIDSelected = vm.PartitionAddressHovered[Context].template_id
      const TemplateID = vm.GetTemplateID(vm.ObjectID)
      let PartitionIsHovered = false

      if(PartitionAddressHovered.length === PartitionAddress.length && PartitionAddressHovered.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsHovered = true
      }
      return PartitionIsHovered
    },
    GetPartitionAddress: function(PartitionIndex) {

      // Store variables
      const vm = this
      return vm.InitialPartitionAddress.concat([PartitionIndex])
    },
    GetPartitionGrid: function(units) {

      let GridArray = []
      for(let i=0; i<units; i++) {
        GridArray.push('1fr')
      }

      return GridArray.join(' ')
    },
    GetPartitionAreas: function(rows, cols) {

      let AreasArray = []
      let AreaCounter = 0
      for(let r=0; r<rows; r++) {

        AreasArray.push([])
        for(let c=0; c<cols; c++) {
          
          AreasArray[r].push([])
          AreasArray[r][c] = 'area'+AreaCounter
          AreaCounter++
        }
      }
      
      const AreasString = "'" + AreasArray.map(arr => arr.join(' ')).join("' '") + "'";

      return AreasString
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
      const TemplateFaceSelected = vm.TemplateFaceSelected
      const Context = vm.Context
      const Template = vm.GetTemplate()
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
      let WorkingMax = vm.GetGlobalPartitionMax(Template, PartitionAddress)
      let WorkingPartition = JSON.parse(JSON.stringify(Template.blueprint[TemplateFaceSelected[Context]]))
      
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

      const vm = this;
      let PartitionFlexGrow
      const PartitionAddress = vm.GetPartitionAddress(PartitionIndex)
      const PartitionParentSize = vm.GetPartitionParentSize(PartitionAddress)

      PartitionFlexGrow = PartitionUnits / PartitionParentSize

      return PartitionFlexGrow
    },
    GetPartitionDirection: function(PartitionAddress) {

      const PartitionDirection = (PartitionAddress.length % 2) ? 'row' : 'col'
      
      return PartitionDirection
    },
  },
}
</script>
<template>

  <div
    class="pcm_cabinet_object_container pcm_template_partition_layered"
    :class="PartitionDirectionClass()"
  >
    
    <div
      v-for="(Partition, PartitionIndex) in GetPartitionCollection()"
      :key=" GetDepthCounter(PartitionIndex).join('-') "
      class=" pcm_template_partition_box "
      :class="{
        pcm_template_partition_selected: PartitionIsSelected(PartitionIndex),
        pcm_template_partition_hovered: PartitionIsHovered(PartitionIndex),
      }"
      :style="{ 'flex-grow': GetPartitionFlexGrow(Partition.units, PartitionIndex) }"
      :DepthCounter=" GetDepthCounter(PartitionIndex) "
      @click.stop=" $emit('PartitionClicked', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetDepthCounter(PartitionIndex)}) "
      @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetDepthCounter(PartitionIndex), 'HoverState': true}) "
      @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'TemplateID': GetTemplateID(ObjectID), 'PartitionAddress': GetDepthCounter(PartitionIndex), 'HoverState': false}) "
    >
      <!-- Generic partition -->
      <ComponentTemplate
        v-if=" Partition.type == 'generic' "
        :ObjectData="ObjectData"
        :TemplateData="TemplateData"
        :TemplateRUSize="TemplateRUSize"
        :InitialDepthCounter="GetDepthCounter(PartitionIndex)"
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
        class=" pcm_template_connectable_container "
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
        class=" pcm_template_enclosure_container "
        :style="{
          'grid-template-rows': GetPartitionGrid(Partition.enc_layout.rows),
          'grid-template-columns': GetPartitionGrid(Partition.enc_layout.cols),
          'grid-template-areas': GetPartitionAreas(Partition.enc_layout.rows, Partition.enc_layout.cols),
        }"
      >
        <div
          v-for=" encIndex in (Partition.enc_layout.rows * Partition.enc_layout.cols) "
          :key=" encIndex "
          class=" pcm_template_enclosure_area "
          :style="{ 'grid-area': 'area'+(encIndex-1) }"
        >
          <div
            v-if="GetEnclosureInsert(encIndex-1, Partition.enc_layout.cols)"
          >
            <ComponentObject
              :ObjectData="ObjectData"
              :TemplateData="TemplateData"
              :CategoryData="CategoryData"
              :TemplateRUSize="TemplateRUSize"
              :InitialDepthCounter=" InitialDepthCounter "
              :Context="Context"
              :ObjectID=2
              :TemplateFaceSelected="TemplateFaceSelected"
              :PartitionAddressSelected="PartitionAddressSelected"
              :PartitionAddressHovered="PartitionAddressHovered"
              @PartitionClicked=" $emit('PartitionClicked', $event) "
              @PartitionHovered=" $emit('PartitionHovered', $event) "
            />
          </div>
          {{ GetEnclosureAddress(encIndex-1, Partition.enc_layout.cols)[0] }}, {{ GetEnclosureAddress(encIndex-1, Partition.enc_layout.cols)[1] }}
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
  },
  props: {
    ObjectData: {type: Object},
    TemplateData: {type: Object},
    CategoryData: {type: Array},
    TemplateRUSize: {type: Number},
    InitialDepthCounter: {type: Array},
    Context: {type: String},
    ObjectID: {type: Number},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  methods: {
    GetPartitionCollection: function() {

      // Initial variables
      const vm = this
      const Template = vm.GetTemplate()
      const TemplateFaceSelected = vm.TemplateFaceSelected
      const Context = vm.Context
      const PartitionAddress = vm.InitialDepthCounter
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
    GetEnclosureInsert: function(encIndex, encCols) {
      
      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID
      const InsertIndex = vm.ObjectData[Context].findIndex((object) => object.parent_id == ObjectID)
      let InsertFound = false
      if(InsertIndex !== -1) {
        const Insert = vm.ObjectData[Context][InsertIndex]
        const InsertParentEnclosureAddress = Insert.parent_enc_addr
        const EnclosureAddress = vm.GetEnclosureAddress(encIndex, encCols)

        if(InsertParentEnclosureAddress[0] == EnclosureAddress[0] && InsertParentEnclosureAddress[1] == EnclosureAddress[1]) {
          InsertFound = true
        }
      }

      return InsertFound
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
      const PartitionAddress = vm.GetDepthCounter(PartitionIndex)
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
      const PartitionAddress = vm.GetDepthCounter(PartitionIndex)
      const TemplateIDSelected = vm.PartitionAddressHovered[Context].template_id
      const TemplateID = vm.GetTemplateID(vm.ObjectID)
      let PartitionIsHovered = false

      if(PartitionAddressHovered.length === PartitionAddress.length && PartitionAddressHovered.every(function(value, index) { return value === PartitionAddress[index]}) && TemplateIDSelected == TemplateID) {
        PartitionIsHovered = true
      }
      return PartitionIsHovered
    },
    GetDepthCounter: function(PartitionIndex) {

      // Store variables
      const vm = this;
      return vm.InitialDepthCounter.concat([PartitionIndex])
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
    GetPartitionParentSize: function(PartitionAddress) {

      // Store variables
      const vm = this
      const TemplateFaceSelected = vm.TemplateFaceSelected
      const Context = vm.Context
      const PartitionDirection = (PartitionAddress.length % 2) ? 'column' : 'row'
      let WorkingMax = (PartitionDirection == 'column') ? 24 : vm.TemplateRUSize * 2

      let WorkingPartition = JSON.parse(JSON.stringify(vm.GetTemplate().blueprint[TemplateFaceSelected[Context]]))
      
      PartitionAddress.pop()
      PartitionAddress.forEach(function(PartitionAddressIndex, Depth){
          let WorkingPartitionDirection = ((Depth + 1) % 2) ? 'column' : 'row'
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
      const PartitionAddress = vm.GetDepthCounter(PartitionIndex)
      const PartitionParentSize = vm.GetPartitionParentSize(PartitionAddress)

      PartitionFlexGrow = PartitionUnits / PartitionParentSize

      return PartitionFlexGrow
    },
    GetSelectedPartitionDirection: function() {

      const vm = this;
      const PartitionAddress = vm.InitialDepthCounter
      let PartitionDirection

      PartitionDirection = (PartitionAddress.length % 2) ? 'column' : 'row'
      
      return PartitionDirection
    },
    PartitionDirectionClass: function() {

      const vm = this;
      const PartitionAddress = vm.InitialDepthCounter
      
      return (PartitionAddress.length % 2) ? 'pcm_template_partition_horizontal' : 'pcm_template_partition_vertical'
    },
  },
}
</script>
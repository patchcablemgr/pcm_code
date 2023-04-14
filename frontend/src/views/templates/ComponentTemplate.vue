<template>

  <div
    :class="TemplateClasses"
  >
    <div
      v-for="(Partition, PartitionIndex) in GetPartitionCollection()"
      :key=" GetPartitionAddress(PartitionIndex).join('-') "
      class=" pcm_template_partition_box "
      :class="{
        pcm_template_partition_selected: IsPartitionSelected(GetPartitionAddress(PartitionIndex)),
        pcm_template_partition_hovered: IsPartitionHovered(GetPartitionAddress(PartitionIndex)),
      }"
      :style="{ 'flex-grow': GetPartitionFlexGrow(Partition.units, PartitionIndex) }"
      @click.stop=" PartitionClicked({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': GetPartitionAddress(PartitionIndex), 'PortID': null}) "
      @mouseover.stop="PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': GetPartitionAddress(PartitionIndex), 'PortID': null, 'HoverState': true}) "
      @mouseleave.stop="PartitionHovered({'Context': Context, 'ObjectID': ObjectID, 'ObjectFace': ObjectFace, 'PartitionAddress': GetPartitionAddress(PartitionIndex), 'PortID': null, 'HoverState': false}) "
    >
      <!-- Generic partition -->
      <ComponentTemplate
        v-if=" Partition.type == 'generic' "
        :InitialPartitionAddress="GetPartitionAddress(PartitionIndex)"
        :Context="Context"
        :ObjectID="ObjectID"
        :CabinetFace="CabinetFace"
        :ObjectFace="ObjectFace"
        :ObjectsAreDraggable="ObjectsAreDraggable"
        :SelectedPortInfo="SelectedPortInfo"
        :HoveredPortInfo="HoveredPortInfo"
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

        <component-port-r
          v-for=" portIndex in (Partition.port_layout.rows * Partition.port_layout.cols) "
          :key=" portIndex "
          :style="{ 'grid-area': 'area'+(portIndex-1)}"
          :Context="Context"
          :ObjectID="ObjectID"
          :ObjectFace="ObjectFace"
          :PartitionAddress="GetPartitionAddress(PartitionIndex)"
          :PortID="(portIndex-1)"
          :PortSelected="IsPortSelected(GetPartitionAddress(PartitionIndex), (portIndex-1))"
          :PortHovered="IsPortHovered(GetPartitionAddress(PartitionIndex), (portIndex-1))"
        >
        </component-port-r>
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
            v-if="GetEnclosureInsertID(GetPartitionAddress(PartitionIndex), encIndex-1, Partition.enc_layout.cols)"
            :InitialPartitionAddress=[]
            :Context="Context"
            :ObjectID="GetEnclosureInsertID(GetPartitionAddress(PartitionIndex), encIndex-1, Partition.enc_layout.cols)"
            :CabinetFace="CabinetFace"
            :ObjectsAreDraggable="ObjectsAreDraggable"
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
import ComponentPortR from '@/views/templates/ComponentPortR.vue'
import { PCM } from '../../mixins/PCM.js'

export default {
  name: "ComponentTemplate",
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,
    ComponentPortR,
    ComponentObject: () => import('./ComponentObject.vue'),
  },
  props: {
    InitialPartitionAddress: {type: Array},
    Context: {type: String},
    ObjectID: {},
    CabinetFace: {type: String},
    ObjectFace: {type: String},
    ObjectsAreDraggable: {type: Boolean},
    SelectedPortInfo: {},
    HoveredPortInfo: {},
  },
  data() {
    return {
    }
  },
  computed: {
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    IsPseudo: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID
      const TemplateID = String(vm.GetTemplateID(ObjectID, Context))

      return TemplateID.includes('pseudo')

    },
    IsPseudoParent: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID
      const ChildObjectIndex = vm.Objects[Context].findIndex((object) => object.parent_id == ObjectID )
      
      return (vm.IsPseudo && ChildObjectIndex !== -1) ? true : false

    },
    TemplateClasses: function() {

      const vm = this
      const Context = vm.Context
      const PartitionAddress = vm.GetPartitionAddress(0)
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
      let ClassLayered
      let ClassBorder
      
      if(Context == 'workspace') {
        ClassLayered = true
      } else {
        ClassLayered = false
      }

      if(Context == 'workspace') {
        ClassBorder = true
      } else {
        if(vm.InitialPartitionAddress.length == 0 && !vm.IsPseudo) {
          ClassBorder = true
        } else {
          ClassBorder = false
        }
      }

      return {
        "pcm_cabinet_object_container": true,
        "pcm_template_partition_layered": ClassLayered,
        "pcm_template_partition_border": ClassBorder,
        "pcm_template_partition_row": PartitionDirection == 'row',
        "pcm_template_partition_col": PartitionDirection == 'col',
      }
    },
    TemplateEnclosureClasses: function() {

      const vm = this
      const Context = vm.Context
      let ClassBackground

      if(Context == 'workspace') {
        ClassBackground = true
      } else {
        if(vm.IsPseudo) {
          ClassBackground = false
        } else {
          ClassBackground = true
        }
      }

      return {
        "pcm_template_enclosure_container": true,
        "pcm_template_enclosure_container_background": ClassBackground
      }
    },
    TemplateEnclosureAreaClasses: function() {

      const vm = this
      const Context = vm.Context
      let ClassBorder

      if(Context == 'workspace') {
        ClassBorder = true
      } else {
        if(vm.IsPseudo) {
          ClassBorder = false
        } else {
          ClassBorder = true
        }
      }

      return {
        "pcm_template_enclosure_area": true,
        "pcm_template_enclosure_area_border": ClassBorder
      }
    },
  },
  methods: {
    GetPartitionCollection: function() {

      // Initial variables
      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID

      // Get Template
      const Template = vm.GetTemplate({ObjectID, Context})

      // Get Object Face
      const ObjectFace = vm.ObjectFace

      // Get Partition Collection
      const PartitionAddress = vm.InitialPartitionAddress
      let PartitionCollection = Template.blueprint[ObjectFace]
      PartitionAddress.forEach(function(PartitionAddressIndex, Depth){
        PartitionCollection = PartitionCollection[PartitionAddressIndex].children
      })

      return PartitionCollection

    },
    GetEnclosureInsertID: function(partitionAddress, encIndex, encCols) {
      
      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID
      const EnclosureAddress = vm.GetEnclosureAddress(encIndex, encCols)
      const InsertIndex = vm.Objects[Context].findIndex(function(object) {
        return object.parent_id == ObjectID && JSON.stringify(object.parent_partition_address) == JSON.stringify(partitionAddress) && JSON.stringify(object.parent_enclosure_address) == JSON.stringify(EnclosureAddress)
      })
      let EnclosureInsertID = false
			
      if(InsertIndex !== -1) {

        const Insert = vm.Objects[Context][InsertIndex]
        EnclosureInsertID = Insert.id
      }

      return EnclosureInsertID
    },
    GetEnclosureAddress: function(encIndex, encCols) {
      const row = Math.floor(encIndex / encCols)
      const col = encIndex - (row * encCols)

      return [row, col]
    },
    HandleDrop: function(ParentID, ParentFace, ParentPartitionAddress, ParentEnclosureAddress, event) {
      
      // Store data
      
      const vm = this
      const Context = event.dataTransfer.getData('context')
      let InsertObjectID = event.dataTransfer.getData('object_id')
      let TemplateID = event.dataTransfer.getData('template_id')
      const TemplateFace = event.dataTransfer.getData('template_face')

      // getData() returns string which needs to be converted back to integer if it is a number
      InsertObjectID = (vm.is_Numeric(InsertObjectID)) ? parseInt(InsertObjectID) : InsertObjectID
      TemplateID = (vm.is_Numeric(TemplateID)) ? parseInt(TemplateID) : TemplateID

      // Validate dropped object template type
      const Template = vm.GetTemplate({TemplateID, Context})
      const TemplateType = Template.type

      if(TemplateType == 'insert') {

        let data = {}
        let url

        data.parent_id = ParentID
        data.parent_face = ParentFace
        data.parent_partition_address = ParentPartitionAddress
        data.parent_enclosure_address = ParentEnclosureAddress

        // POST new object
        if(Context == 'template') {

          data.template_id = TemplateID
          data.template_face = TemplateFace

          url = '/api/objects/insert'

          // POST to objects
          vm.$http.post(url, data).then(function(response){

            vm.$store.commit('pcmObjects/ADD_Object', {pcmContext:'actual', data:response.data})

          }).catch(error => {vm.DisplayError(error)})
        } else {

          data.object_id = InsertObjectID

          url = '/api/objects/'+InsertObjectID

          // POST to objects
          vm.$http.patch(url, data).then(function(response){

            vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:'actual', data:response.data})

          }).catch(error => {vm.DisplayError(error)})
        }
      }
    },
    GetGlobalPartitionMax: function(Template, PartitionAddress) {

      const vm = this
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)

      // Get working max
      let WorkingMax = (PartitionDirection == 'row') ? 24 : Template.ru_size*2
      if (Template.insert_constraints !== null) {
        const InsertConstraintPartitionLayout = Template.insert_constraints[Template.insert_constraints.length-1].part_layout
        WorkingMax = (PartitionDirection == 'row') ? InsertConstraintPartitionLayout.width : InsertConstraintPartitionLayout.height
      }

      return WorkingMax

    },
    GetPartitionParentSize: function(PartitionAddress) {

      // Store variables
      const vm = this
      const Context = vm.Context
      const ObjectID = vm.ObjectID
      const Face = vm.ObjectFace
      const Template = vm.GetTemplate({ObjectID, Context})
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
    IsPartitionSelected: function(PartitionAddress) {

      const vm = this
      const SelectedPortInfo = vm.SelectedPortInfo
      const ObjectFace = vm.ObjectFace

      if(SelectedPortInfo) {
        if(JSON.stringify(PartitionAddress) == JSON.stringify(SelectedPortInfo['partition'][ObjectFace])) {
          return true
        } else {
          return false
        }
      } else {
        return false
      }

    },
    IsPartitionHovered: function(PartitionAddress) {

      const vm = this
      const HoveredPortInfo = vm.HoveredPortInfo
      const ObjectFace = vm.ObjectFace

      if(HoveredPortInfo) {
        if(JSON.stringify(PartitionAddress) == JSON.stringify(HoveredPortInfo['partition'][ObjectFace])) {
          return true
        } else {
          return false
        }
      } else {
        return false
      }

    },
    IsPortSelected: function(PartitionAddress, PortID) {

      const vm = this
      const SelectedPortInfo = vm.SelectedPortInfo
      const ObjectFace = vm.ObjectFace

      if(SelectedPortInfo) {
        if(JSON.stringify(PartitionAddress) == JSON.stringify(SelectedPortInfo['partition'][ObjectFace]) && PortID == SelectedPortInfo['port_id'][ObjectFace]) {
          return true
        } else {
          return false
        }
      } else {
        return false
      }

    },
    IsPortHovered: function(PartitionAddress, PortID) {

      const vm = this
      const HoveredPortInfo = vm.HoveredPortInfo
      const ObjectFace = vm.ObjectFace

      if(HoveredPortInfo) {
        if(JSON.stringify(PartitionAddress) == JSON.stringify(HoveredPortInfo['partition'][ObjectFace]) && PortID == HoveredPortInfo['port_id'][ObjectFace]) {
          return true
        } else {
          return false
        }
      } else {
        return false
      }

    }
  },
}
</script>
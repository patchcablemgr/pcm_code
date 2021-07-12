<template>
  <div>
    <b-container class="bv-example-row" fluid="xs">
      <b-row>
        <b-col>
          <b-card
            title="Properties"
          >
            <b-card-body>
              <templates-form
                :CategoryData="CategoryData"
                :PortConnectorData="PortConnectorData"
                :PortOrientationData="PortOrientationData"
                :MediaData="MediaData"
                :TemplateData="TemplateData"
                :CabinetFace="CabinetFace"
                :SelectedPartitionAddress="SelectedPartitionAddress"
                :AddChildPartitionDisabled="AddChildPartitionDisabled"
                :AddSiblingPartitionDisabled="AddSiblingPartitionDisabled"
                :RemovePartitionDisabled="RemovePartitionDisabled"
                :PartitionTypeDisabled="PartitionTypeDisabled"
                @TemplateNameUpdated="TemplateNameUpdated($event)"
                @TemplateCategoriesUpdated="TemplateCategoriesUpdated($event)"
                @TemplateCategoryUpdated="TemplateCategoryUpdated($event)"
                @TemplateTypeUpdated="TemplateTypeUpdated($event)"
                @TemplateRUSizeUpdated="TemplateRUSizeUpdated($event)"
                @TemplateMountConfigUpdated="TemplateMountConfigUpdated($event)"
                @TemplatePartitionTypeUpdated="TemplatePartitionTypeUpdated($event)"
                @TemplatePartitionAdd="TemplatePartitionAdd($event)"
                @TemplatePartitionRemove="TemplatePartitionRemove()"
                @TemplatePartitionSizeUpdated="TemplatePartitionSizeUpdated($event)"
                @TemplatePartitionPortLayoutColsUpdated="TemplatePartitionPortLayoutColsUpdated($event)"
                @TemplatePartitionPortLayoutRowsUpdated="TemplatePartitionPortLayoutRowsUpdated($event)"
                @TemplatePartitionMediaUpdated="TemplatePartitionMediaUpdated($event)"
                @TemplatePartitionPortConnectorUpdated="TemplatePartitionPortConnectorUpdated($event)"
                @TemplatePartitionPortOrientationUpdated="TemplatePartitionPortOrientationUpdated($event)"
                @TemplatePartitionEncLayoutColsUpdated="TemplatePartitionEncLayoutColsUpdated($event)"
                @TemplatePartitionEncLayoutRowsUpdated="TemplatePartitionEncLayoutRowsUpdated($event)"
                @FormReset="FormReset($event)"
              />
            </b-card-body>
          </b-card>
        </b-col>
        <b-col>
          <b-card
            title="Preview"
          >
            <b-card-body>
              <b-form-radio
                v-model="CabinetFace"
                plain
                value="front"
                :disabled="CabinetFaceToggleIsDisabled"
              >Front
              </b-form-radio>
              <b-form-radio
                v-model="CabinetFace"
                plain
                value="rear"
                :disabled="CabinetFaceToggleIsDisabled"
              >
                Rear
              </b-form-radio>
              <component-cabinet
                :CabinetData="CabinetData"
                :CategoryData="CategoryData"
                :ObjectData="ObjectData"
                :TemplateData="TemplateData"
                :CabinetFace="CabinetFace"
                :SelectedPartitionAddress="SelectedPartitionAddress"
                :HoveredPartitionAddress="HoveredPartitionAddress"
                @PartitionClicked=" PartitionClicked($event) "
                @PartitionHovered=" PartitionHovered($event) "
              />
            </b-card-body>
          </b-card>
        </b-col>
        <b-col>
          <b-card
            title="Details"
          >
            <b-card-text>Bear claw sesame snaps gummies chocolate.</b-card-text>
          </b-card>
        </b-col>
      </b-row>
    </b-container>
    <toast-general/>
  </div>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BCardBody, BCardText, BFormRadio, } from 'bootstrap-vue'
import TemplatesForm from './templates/TemplatesForm.vue'
import ToastGeneral from './templates/ToastGeneral.vue'
import ComponentCabinet from './templates/ComponentCabinet.vue'

const CategoryData = [
  {
    "id": 0,
    "color": "#FFFFFFFF",
  }
]
const PortConnectorData = [
  {
    "value": 1,
    "name": "RJ45",
    "category_type_id": 1,
    "default": 1,
  },
]
const PortOrientationData = [
  {
    "value": 1,
    "name": "Top-Left to Right",
    "default": 1,
  },
]
const MediaData = [
  {
    "value": 1,
    "name": "placeholder",
    "category_id": 1,
    "type_id": 1,
    "display": 1,
    "default": 1,
  }
]
const CabinetFace = "front"
const SelectedPartitionAddress = {
  "front": [],
  "rear": []
}
const HoveredPartitionAddress = {
  "front": false,
  "rear": false
}
const CabinetData = {
  "id": 1,
  "size": 25,
  "name": "Cabinet",
}
const ObjectData = [
  {
    "id": 0,
    "location_id": 1,
    "cabinet_ru": 1,
    "cabinet_face": "front",
    "template_id": 0,
    "name": "Object",
  }
]
const TemplateData = [
  {
    "id": 0,
    "name": "New_Template",
    "category_id": 0,
    "type": "standard",
    "ru_size": 2,
    "function": "endpoint",
    "mount_config": "4-post",
    "blueprint": {
      "front": {
        "type": "generic",
        "children": [
          {
            "type": "generic",
            "units": 1,
            "children": [],
          },
          {
            "type": "generic",
            "units": 1,
            "children": [],
          },
          {
            "type": "generic",
            "units": 1,
            "children": [],
          },
          {
            "type": "generic",
            "units": 1,
            "children": [],
          },
          {
            "type": "generic",
            "units": 1,
            "children": [],
          },
        ],
      },
      "rear": {
        "children": [
          {
            "type": "generic",
            "units": 1,
            "children": [],
          },
        ],
      },
    }
  }
]

export default {
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardText,
    BFormRadio,

    TemplatesForm,
    ToastGeneral,
    ComponentCabinet,
  },
  data() {
    return {
      CategoryData,
      MediaData,
      PortConnectorData,
      PortOrientationData,
      CabinetData,
      ObjectData,
      TemplateData,
      TemplateDataOriginal: JSON.parse(JSON.stringify(TemplateData)),
      CabinetFace,
      SelectedPartitionAddress,
      HoveredPartitionAddress,
    }
  },
  computed: {
    CabinetFaceToggleIsDisabled: function() {
      return this.TemplateData[0].mount_config === '2-post'
    },
    AddChildPartitionDisabled: function() {
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const Partition = vm.GetPartition(PartitionAddress)

      return (!vm.GetPartitionUnitsAvailable() || Partition.type != 'generic')

      //return !vm.TemplateSelectedPartitionRoomAvailable()
    },
    AddSiblingPartitionDisabled: function() {
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)

      return (!vm.GetPartitionUnitsAvailable(PartitionParentAddress) || PartitionAddress.length == 0)

      //return !vm.TemplateParentPartitionRoomAvailable()
    },
    RemovePartitionDisabled: function() {
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]

      return !SelectedPartitionAddress.length
    },
    PartitionTypeDisabled: function() {
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]

      return !SelectedPartitionAddress.length
    },
  },
  methods: {
    PartitionClicked: function(PartitionAddress) {

      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace

      vm.SelectedPartitionAddress[CabinetFace] = PartitionAddress

      console.log('Debug (selectedPartition): '+JSON.stringify(vm.GetPartition()))
    },
    PartitionHovered: function(HoverData) {

      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = HoverData.PartitionAddress
      const HoverState = HoverData.HoverState

      vm.HoveredPartitionAddress[CabinetFace] = (HoverState) ? PartitionAddress : false
    },
    TemplateNameUpdated: function(newValue) {
      this.TemplateData[0].name = newValue
    },
    TemplateCategoriesUpdated: function() {
      const vm = this;

      vm.categoryGET();
    },
    TemplateCategoryUpdated: function(newValue) {
      this.TemplateData[0].category_id = newValue
    },
    TemplateTypeUpdated: function(newValue) {
      this.TemplateData[0].type = newValue
    },
    TemplateRUSizeUpdated: function(newValue) {
      this.TemplateData[0].ru_size = newValue
    },
    TemplateMountConfigUpdated: function(newValue) {
      this.TemplateData[0].mount_config = newValue
      this.CabinetFace = this.TemplateData[0].mount_config == '2-post' ? 'front' : this.CabinetFace
    },
    TemplatePartitionTypeUpdated: function(newValue) {

      // Store variables
      const vm = this
      let SelectedPartition = vm.GetPartition()
      SelectedPartition.type = newValue

      // Define port_layout if it doesn't exist
      if(newValue == 'connectable') {

        // Port layout
        SelectedPartition.port_layout = {'cols': 1, 'rows': 1}

        // Port media type
        const defaultMediaIndex = vm.MediaData.findIndex((media) => media.default);
        const defaultMediaValue = vm.MediaData[defaultMediaIndex].value
        SelectedPartition.media = defaultMediaValue

        // Port connector
        const defaultPortConnectorIndex = vm.PortConnectorData.findIndex((PortConnector) => PortConnector.default);
        const defaultPortConnectorValue = vm.PortConnectorData[defaultPortConnectorIndex].value
        SelectedPartition.port_connector = defaultPortConnectorValue

        // Port orientation
        const defaultPortOrientationIndex = vm.PortOrientationData.findIndex((PortOrientation) => PortOrientation.default);
        const defaultPortOrientationValue = vm.PortOrientationData[defaultPortOrientationIndex].value
        SelectedPartition.port_orientation = defaultPortOrientationValue

      } else if(newValue == 'enclosure') {
        
        SelectedPartition.enc_layout = ('enc_layout' in SelectedPartition) ? SelectedPartition.enc_layout : {'cols': 1, 'rows': 1}

      }
    },
    TemplatePartitionAdd: function(InsertPosition) {

      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      let PartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const PartitionParentAddress = (PartitionAddress.length > 0) ? PartitionAddress.slice(0, PartitionAddress.length - 1) : PartitionAddress
      const PartitionIndex = PartitionAddress[PartitionAddress.length - 1]
      let SelectedPartition = vm.GetPartition()
      let SelectedPartitionParent = vm.GetPartition(PartitionParentAddress)
      let PartitionBlank = {
        "type": "generic",
        "units": 1,
        "children": [],
      }

      if(InsertPosition == 'after' || InsertPosition == 'before') {

        // Determine if partition has space available
        if(vm.GetPartitionUnitsAvailable(PartitionParentAddress)) {
          if(InsertPosition == 'after') {

            // Insert new partition after selected partition
            SelectedPartitionParent.children.splice(PartitionIndex + 1, 0, PartitionBlank)
          } else if (InsertPosition == 'before') {

            // Insert new partition before selected partition
            SelectedPartitionParent.children.splice(PartitionIndex, 0, PartitionBlank)

            // Update SelectedPartitionAddress as it is shifted right
            PartitionAddress[PartitionAddress.length - 1] = PartitionIndex + 1
          }
        }
      } else if (InsertPosition == 'inside') {

        // Determine if partition has space available
        if(vm.GetPartitionUnitsAvailable()) {
          SelectedPartition.children.push(PartitionBlank)
        }
      }
    },
    TemplatePartitionRemove: function() {
      
      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      const SelectedPartitionAddressCopy = JSON.parse(JSON.stringify(SelectedPartitionAddress))
      let SelectedPartitionParent = TemplateData.blueprint[CabinetFace]
      const SelectedPartitionIndex = SelectedPartitionAddressCopy.pop()
      
      // Traverse blueprint until selected partition is reached
      if(SelectedPartitionIndex !== 'undefined') {

        // Repoint selected partition address to next available
        if(SelectedPartitionIndex > 0) {
          SelectedPartitionAddress[SelectedPartitionAddress.length - 1] = SelectedPartitionIndex - 1
        } else {
          SelectedPartitionAddress.pop()
        }

        // Identify selected partition parent
        SelectedPartitionAddressCopy.some(function(AddressIndex) {
          SelectedPartitionParent = SelectedPartitionParent.children[AddressIndex]
        })
      }

      // Remove selected partition from parent
      vm.$delete(SelectedPartitionParent.children, SelectedPartitionIndex)
    },
    TemplatePartitionSizeUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.units = newValue
    },
    TemplatePartitionPortLayoutColsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.port_layout.cols = newValue
    },
    TemplatePartitionPortLayoutRowsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.port_layout.rows = newValue
    },
    TemplatePartitionMediaUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.media = newValue
    },
    TemplatePartitionPortConnectorUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.port_connector = newValue
    },
    TemplatePartitionPortOrientationUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.port_orientation = newValue
    },
    TemplatePartitionEncLayoutColsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.enc_layout.cols = newValue
    },
    TemplatePartitionEncLayoutRowsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetPartition()
      SelectedPartition.enc_layout.rows = newValue
    },
    FormReset: function() {
      const vm = this
      for (const [key] of Object.entries(vm.TemplateData[0])) {
        vm.TemplateData[0][key] = vm.TemplateDataOriginal[0][key]
      }
    },
    GetPartitionDirection: function(PartAddr = false) {
      
      // Store variables
      const vm = this
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = (PartAddr) ? PartAddr : vm.SelectedPartitionAddress[CabinetFace]
      const PartitionDirection = (PartitionAddress.length % 2) ? 'column' : 'row'

      return PartitionDirection
    },
    GetPartitionUnitsAvailable: function(PartAddr = false) {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = (PartAddr) ? PartAddr : vm.SelectedPartitionAddress[CabinetFace]
      const Partition = vm.GetPartition(PartitionAddress)
      const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
      let UnitsAvailable = (PartitionDirection == 'row') ? 24 : TemplateData.ru_size * 2

      if(PartitionAddress.length > 1) {
        const PartitionParentAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
        const PartitionParent = vm.GetPartition(PartitionParentAddress)
        UnitsAvailable = PartitionParent.units
      }

      Partition.children.forEach(function(PartitionChild) {
        UnitsAvailable = UnitsAvailable - PartitionChild.units
      })

      return UnitsAvailable
    },
    GetPartition: function(PartAddr = false) {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const PartitionAddress = (PartAddr) ? PartAddr : vm.SelectedPartitionAddress[CabinetFace]
      let WorkingPartition = TemplateData.blueprint[CabinetFace]

      // Traverse blueprint until selected partition is reached
      PartitionAddress.forEach(function(AddressIndex, Index) {
        WorkingPartition = WorkingPartition.children[AddressIndex]
      })

      // Return selected partition
      return WorkingPartition
    },
    categoryGET: function(SetCategoryToDefault = false) {

      const vm = this;

      this.$http.get('/api/category').then(function(response){
        vm.CategoryData = response.data;

        // Apply default category to template preview
        if(SetCategoryToDefault) {
          const defaultCategoryIndex = vm.CategoryData.findIndex((category) => category.default);
          const defaultCategoryID = vm.CategoryData[defaultCategoryIndex].id
          vm.TemplateData[0].category_id = defaultCategoryID
        }
      });
    },
    mediumGET: function() {

      const vm = this;

      this.$http.get('/api/medium').then(function(response){
        vm.MediaData = response.data;
      });
    },
    portOrientationGET: function() {

      const vm = this;

      this.$http.get('/api/port-orientation').then(function(response){
        vm.PortOrientationData = response.data;
      });
    },
  },
  mounted() {

    const vm = this;
    const SetCategoryToDefault = true

    vm.categoryGET(SetCategoryToDefault)
    vm.mediumGET()
    vm.portOrientationGet()
  },
}
</script>

<style>

</style>

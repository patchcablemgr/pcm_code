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
                :TemplateData="TemplateData"
                :CabinetFace="CabinetFace"
                :SelectedPartitionAddress="SelectedPartitionAddress"
                :AddPartitionDisabled="AddPartitionDisabled"
                :RemovePartitionDisabled="RemovePartitionDisabled"
                :PartitionTypeDisabled="PartitionTypeDisabled"
                @TemplateNameUpdated="TemplateNameUpdated($event)"
                @TemplateCategoriesUpdated="TemplateCategoriesUpdated($event)"
                @TemplateCategoryUpdated="TemplateCategoryUpdated($event)"
                @TemplateTypeUpdated="TemplateTypeUpdated($event)"
                @TemplateRUSizeUpdated="TemplateRUSizeUpdated($event)"
                @TemplateMountConfigUpdated="TemplateMountConfigUpdated($event)"
                @TemplatePartitionTypeUpdated="TemplatePartitionTypeUpdated($event)"
                @TemplatePartitionAdd="TemplatePartitionAdd()"
                @TemplatePartitionRemove="TemplatePartitionRemove()"
                @TemplatePartitionSizeUpdated="TemplatePartitionSizeUpdated($event)"
                @TemplatePartitionPortLayoutColsUpdated="TemplatePartitionPortLayoutColsUpdated($event)"
                @TemplatePartitionPortLayoutRowsUpdated="TemplatePartitionPortLayoutRowsUpdated($event)"
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
                @PartitionClicked=" PartitionClicked($event) "
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
    "color": "#FFFFFFFF"
  }
]
const CabinetFace = "front"
const SelectedPartitionAddress = {
  "front": [],
  "rear": []
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
            "units": 12,
            "children": [
              {
                "type": "generic",
                "units": 3,
                "children": [
                  {
                    "type": "generic",
                    "units": 10,
                    "children": [],
                  },
                  {
                    "type": "generic",
                    "units": 1,
                    "children": [],
                  }
                ],
              },
            ],
          },
        ],
      },
      "rear": {
        "children": [
          {
            "type": "generic",
            "units": 2,
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
      CabinetData,
      ObjectData,
      TemplateData,
      TemplateDataOriginal: JSON.parse(JSON.stringify(TemplateData)),
      CabinetFace,
      SelectedPartitionAddress,
    }
  },
  computed: {
    CabinetFaceToggleIsDisabled: function() {
      return this.TemplateData[0].mount_config === '2-post'
    },
    AddPartitionDisabled: function() {
      // Store variables
      const vm = this

      return !vm.TemplatePartitionRoomAvailable()
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

      vm.SelectedPartitionAddress[this.CabinetFace] = PartitionAddress
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
      let SelectedPartition = this.GetSelectedPartition()
      SelectedPartition.type = newValue

      // Define port_layout if it doesn't exist
      if(newValue == 'connectable') {
        
        if(!('port_layout' in SelectedPartition)) {
          
          SelectedPartition.port_layout = {
            'cols': 1,
            'rows': 1,
          }
        }
      }
    },
    TemplatePartitionAdd: function() {

      // Store variables
      const vm = this
      let SelectedPartition = vm.GetSelectedPartition()
      let PartitionBlank = {
        "type": "generic",
        "units": 1,
        "children": [],
      }

      if(vm.TemplatePartitionRoomAvailable()) {
        SelectedPartition.children.push(PartitionBlank)
      }

      console.log('Debug (AddPartitionDisabled): '+vm.AddPartitionDisabled)
    },
    TemplatePartitionRoomAvailable: function() {
      
      // Store variables
      const vm = this
      let SelectedPartition = vm.GetSelectedPartition()
      const PartitionParentSize = vm.GetSelectedPartitionParentSize()

      // Sum of child partition sizes
      let PartitionChildUnits = 0
      SelectedPartition.children.forEach(function(PartitionChild){
        PartitionChildUnits = PartitionChildUnits + PartitionChild.units
      })

      // Return room availability
      if(PartitionChildUnits < PartitionParentSize) {
        return true
      } else {
        return false
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
      let SelectedPartition = this.GetSelectedPartition()
      SelectedPartition.units = newValue
    },
    TemplatePartitionPortLayoutColsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetSelectedPartition()
      SelectedPartition.port_layout.cols = newValue
    },
    TemplatePartitionPortLayoutRowsUpdated: function(newValue) {

      // Store variables
      let SelectedPartition = this.GetSelectedPartition()
      SelectedPartition.port_layout.rows = newValue
    },
    FormReset: function() {
      const vm = this
      for (const [key] of Object.entries(vm.TemplateData[0])) {
        vm.TemplateData[0][key] = vm.TemplateDataOriginal[0][key]
      }
    },
    GetSelectedPartition: function() {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = vm.SelectedPartitionAddress[CabinetFace]
      let SelectedPartition = TemplateData.blueprint[CabinetFace]

      // Traverse blueprint until selected partition is reached
      SelectedPartitionAddress.forEach(function(AddressIndex, Index) {
        SelectedPartition = SelectedPartition.children[AddressIndex]
      })

      // Return selected partition
      return SelectedPartition
    },
    GetSelectedPartitionParentSize: function() {

      // Store variables
      const vm = this
      const TemplateData = vm.TemplateData[0]
      const CabinetFace = vm.CabinetFace
      const SelectedPartitionAddress = JSON.parse(JSON.stringify(vm.SelectedPartitionAddress[CabinetFace]))
      const SelectedPartitionDepth = SelectedPartitionAddress.length
      const SelectedPartitionDirection = (SelectedPartitionDepth % 2) ? 'column' : 'row'
      let WorkingMax = (SelectedPartitionDirection == 'row') ? 24 : TemplateData.ru_size * 2
      let WorkingPartition = JSON.parse(JSON.stringify(TemplateData.blueprint[CabinetFace]))

      // Loop through partition address array
      SelectedPartitionAddress.some(function(PartitionAddressIndex, Depth){

        // If working partition is selected partition parent, set WorkingMax to unit size and break out of loop
        if((Depth + 1) == (SelectedPartitionAddress.length - 1)) {
          WorkingMax = WorkingPartition.children[PartitionAddressIndex].units
          return true
        }

        // Move to next partition
        WorkingPartition = WorkingPartition.children[PartitionAddressIndex]
      })
      
      // Return selected partition parent size
      return WorkingMax
    },
    categoryGET: function(SetCategoryToDefault = false) {

      const vm = this;

      this.$http.get('/api/category').then(function(response){
        vm.CategoryData = response.data;
        if(SetCategoryToDefault) {
          const defaultCategoryIndex = vm.CategoryData.findIndex((category) => category.default);
          const defaultCategoryID = vm.CategoryData[defaultCategoryIndex].id
          vm.TemplateData[0].category_id = defaultCategoryID
        }
      });
    },
  },
  mounted() {

    const vm = this;
    const SetCategoryToDefault = true

    vm.categoryGET(SetCategoryToDefault);
  },
}
</script>

<style>

</style>

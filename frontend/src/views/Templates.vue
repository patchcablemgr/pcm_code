<template>
  <div>
    <b-container class="bv-example-row">
      <b-row>
        <b-col>
          <b-card
            title="Properties"
          >
            <b-card-body>
              <templates-form
                :TemplateData="TemplateData"
                :CabinetFace="CabinetFace"
                :SelectedPartitionAddress="SelectedPartitionAddress"
                @TemplateNameUpdated="TemplateNameUpdated($event)"
                @TemplateCategoryUpdated="TemplateCategoryUpdated($event)"
                @TemplateTypeUpdated="TemplateTypeUpdated($event)"
                @PartitionTypeUpdated="PartitionTypeUpdated($event)"
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
              >Front
              </b-form-radio>
              <b-form-radio
                v-model="CabinetFace"
                plain
                value="rear"
              >
                Rear
              </b-form-radio>
              <component-cabinet
                :CabinetData="CabinetData"
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

const CabinetFace = "front"
const SelectedPartitionAddress = {
  "front": "0",
  "rear": "0"
}
const CabinetData = {
  "id": 1,
  "size": 10,
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
      "front": [
        {
          "type": "generic",
          "units": 12,
          "children": [
            {
              "type": "connectable",
              "units": 1,
              "children": [],
            }
          ],
        },{
          "type": "generic",
          "units": 6,
          "children": [
            {
              "type": "enclosure",
              "units": 2,
              "children": [],
            }
          ],
        }
      ],
      "rear": [
        {
          "type": "generic",
          "units": 2,
          "children": [],
        }
      ],
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
      CabinetData,
      ObjectData,
      TemplateData,
      TemplateDataOriginal: JSON.parse(JSON.stringify(TemplateData)),
      CabinetFace,
      SelectedPartitionAddress,
    }
  },
  methods: {
    PartitionClicked: function(PartitionAddress) {
      this.SelectedPartitionAddress[this.CabinetFace] = PartitionAddress
    },
    TemplateNameUpdated: function(newValue) {
      this.TemplateData[0].name = newValue
    },
    TemplateCategoryUpdated: function(newValue) {
      this.TemplateData[0].category_id = newValue
    },
    TemplateTypeUpdated: function(newValue) {
      this.TemplateData[0].type = newValue
    },
    PartitionTypeUpdated: function(newValue) {

      // Store variables
      const vm = this

      let SelectedPartition = vm.GetSelectedPartition()
      SelectedPartition.type = newValue
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
      const SelectedPartitionAddressArray = SelectedPartitionAddress.split('-')
      let SelectedPartition = TemplateData.blueprint[CabinetFace]

      // Traverse blueprint until selected partition is reached
      SelectedPartitionAddressArray.forEach(function(AddressIndex, Index) {
        if(Index) {
          SelectedPartition = SelectedPartition.children[AddressIndex]
        } else {
          SelectedPartition = SelectedPartition[AddressIndex]
        }
      })

      // Return selected partition
      return SelectedPartition
    },
  },
}
</script>

<style>

</style>

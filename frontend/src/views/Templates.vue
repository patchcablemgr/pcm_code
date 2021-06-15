<template>
  <div>
    <b-container class="bv-example-row">
      <b-row>
        <b-col>
          <b-card
            title="Properties"
          >
            <b-card-body>
              <templates-form/>
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
const CabinetData = {
  "id": 1,
  "size": 10,
  "name": "Cabinet",
}
const ObjectData = [
  {
    "id": 1,
    "location_id": 1,
    "cabinet_ru": 1,
    "cabinet_face": "front",
    "template_id": 1,
    "name": "Object",
  }
]
const TemplateData = [
  {
    "id": 1,
    "mount_config": "4-post",
    "ru_size": "2",
    "blueprint": {
      "front": [
        {
          "type": "generic1",
          "children": [
            {
              "type": "child1",
              "children": [],
            }
          ],
        },{
          "type": "generic2",
          "children": [
            {
              "type": "child2",
              "children": [],
            }
          ],
        }
      ],
      "rear": [
        {
          "type": "generic1",
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
      CabinetFace,
    }
  },
}
</script>

<style>

</style>

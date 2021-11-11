<template>
  <div>
    <b-card>
      <b-card-title>
        <div class="d-flex flex-wrap justify-content-between">
          <div class="demo-inline-spacing">
            Floorplan
          </div>
          <div class="demo-inline-spacing">

            <div
              v-for="FloorplanTemplate in FloorplanTemplateData"
              :key="FloorplanTemplate.id"
              style="display: flex;"
            >
              <div
                draggable="true"
                @dragstart.stop="StartDrag({ 'context': 'template', 'floorplan_object_type': FloorplanTemplate.type }, $event)"
                class="pcm_floorplan_object mr-1"
              >
                <feather-icon
                  :icon="FloorplanTemplate.icon"
                  size="16"
                />
              </div>
              <div>{{ FloorplanTemplate.name }}</div>
            </div>

            <!-- Button -->
            <b-button
              title="Create a new field before the one selected"
              v-ripple.400="'rgba(113, 102, 240, 0.15)'"
              variant="primary"
              class="btn-icon"
              @click=" UploadFloorplanImageClicked() "
            >
              <feather-icon icon="ImageIcon" />
              <span class="align-middle">Upload Image</span>
            </b-button>
          </div>
        </div>
      </b-card-title>
      <pinch-zoom
        disableZoomControl="disable"
        limit-zoom=9
      >
        <div
          @drop="HandleDrop($event)"
          @dragover.prevent
          @dragenter.prevent
          style="position:relative;"
        >
          <div
            v-for="FloorplanObject in FloorplanObjects"
            :key="FloorplanObject.id"
            draggable="true"
            class="pcm_floorplan_object"
            :class="{
              pcm_template_partition_selected: FloorplanIsSelected(FloorplanObject.id),
              pcm_template_partition_hovered: FloorplanIsHovered(FloorplanObject.id),
            }"
            @dragstart.stop="StartDrag({ 'context': 'floorplan', 'floorplan_object_id': FloorplanObject.id }, $event)"
            @click.stop=" $emit('FloorplanClicked', {'object_id': FloorplanObject.id}) "
            @mouseover.stop=" $emit('FloorplanHovered', {'object_id': FloorplanObject.id, 'hover_state': true}) "
            @mouseleave.stop=" $emit('FloorplanHovered', {'object_id': FloorplanObject.id, 'hover_state': false}) "
            :style="{position: 'absolute', left: FloorplanObject.floorplan_address[0]+'px', top: FloorplanObject.floorplan_address[1]+'px'}"
          >
            <feather-icon
              :icon="GetFloorplanIcon(FloorplanObject.id)"
              size="16"
            />
          </div>
          <img
            @dragstart.prevent
            :src="FloorplanImage"
          />
        </div>
      </pinch-zoom>
    </b-card>

    <!-- File Upload Modal -->
    <modal-file-upload
      :File="File"
      @FileSelected="$emit('FileSelected', $event)"
      @FileSubmitted="$emit('FileSubmitted')"
    />
  </div>
</template>

<script>
import {
  BCard,
  BCardTitle,
  BContainer,
  BRow,
  BCol,
  BButton,
} from 'bootstrap-vue'
import PinchZoom from 'vue-pinch-zoom'
import ModalFileUpload from './ModalFileUpload.vue'
import { PCM } from '../../mixins/PCM.js'
import Ripple from 'vue-ripple-directive'

export default {
  mixins: [PCM],
  components: {
    BCard,
    BCardTitle,
    BContainer,
    BRow,
    BCol,
    BButton,

    PinchZoom,
    ModalFileUpload,
  },
  props: {
    FloorplanImage: {type: String},
    File: {type: File},
    CabinetData: {type: Object},
    ObjectData: {type: Object},
    Context: {type: String},
    FloorplanTemplateData: {type: Array},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  directives: {
		Ripple,
	},
  data() {
    return {
    }
  },
  computed: {
    FloorplanObjects: function() {

      const vm = this
      const LocationID = vm.CabinetData.id
      const ObjectData = vm.ObjectData.preview
      const FloorplanObjects = ObjectData.filter((object) => object.location_id == LocationID )

      return FloorplanObjects
    }
  },
  methods: {
    GetFloorplanIcon: function(FloorplanObjectID) {

      const vm = this
      const ObjectData = vm.ObjectData.preview
      const FloorplanObject = ObjectData.find((object) => object.id == FloorplanObjectID )
      const FloorplanObjectType = FloorplanObject.floorplan_object_type
      const FloorplanTemplate = vm.FloorplanTemplateData.find((template) => template.type == FloorplanObjectType )
      const FloorplanTemplateIcon = FloorplanTemplate.icon

      return FloorplanTemplateIcon

    },
    StartDrag: function(TransferData, e) {

      const MouseX = e.pageX
      const ElemX = e.target.getBoundingClientRect().left
      const MouseY = e.pageY
      const ElemY = e.target.getBoundingClientRect().top

      const CursorOffsetX = MouseX-ElemX
      const CursorOffsetY = MouseY-ElemY
      
      const Context = TransferData.context
      e.dataTransfer.setData('context', TransferData.context)
      e.dataTransfer.setData('cursor_offset_x', CursorOffsetX)
      e.dataTransfer.setData('cursor_offset_y', CursorOffsetY)

      if(Context == 'template') {

        e.dataTransfer.setData('floorplan_object_type', TransferData.floorplan_object_type)

      } else if(Context == 'floorplan') {

        e.dataTransfer.setData('floorplan_object_id', TransferData.floorplan_object_id)

      }

    },
    UploadFloorplanImageClicked: function() {

      const vm = this
      vm.$bvModal.show('modal-file-upload')

    },
    HandleDrop: function(event) {

      // Store data
      const vm = this
      const Context = event.dataTransfer.getData('context')
      const CursorOffsetX = event.dataTransfer.getData('cursor_offset_x')
      const CursorOffsetY = event.dataTransfer.getData('cursor_offset_y')
      const ElemOffsetX = event.offsetX
      const ElemOffsetY = event.offsetY
      const PosX = parseInt(ElemOffsetX) - parseInt(CursorOffsetX)
      const PosY = parseInt(ElemOffsetY) - parseInt(CursorOffsetY)
      const FloorplanAddress = [PosX, PosY]
      
      let data = {
        "context": Context,
        "floorplan_address": FloorplanAddress,
      }

      if(Context == 'template') {

        const LocationID = vm.CabinetData.id
        const FloorplanObjectType = event.dataTransfer.getData('floorplan_object_type')

        data.location_id = LocationID
        data.floorplan_object_type = FloorplanObjectType

      } else if(Context == 'floorplan') {

        const FloorplanObjectID = event.dataTransfer.getData('floorplan_object_id')

        data.floorplan_object_id = FloorplanObjectID

      }

      vm.$emit('FloorplanObjectDropped', data )
    },
  }
}
</script>
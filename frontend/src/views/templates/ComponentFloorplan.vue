<template>
  <div>
    <b-card>
      <b-card-title>
        <div class="d-flex flex-wrap justify-content-between">
          <div class="demo-inline-spacing">
            Floorplan
          </div>
          <div class="demo-inline-spacing">

            <span class="mr-1">
              <div
                draggable="true"
                @dragstart.stop="StartDrag({ 'floorplan_object_type': 'device' }, $event)"
                class="pcm_floorplan_object"
              >
                <feather-icon
                  icon="MonitorIcon"
                  size="16"
                />
              </div>
            </span>
            <span>Device</span>

            <span class="mr-1">
              <div
                draggable="true"
                class="pcm_floorplan_object"
              >
                <feather-icon
                  icon="VideoIcon"
                  size="16"
                />
              </div>
            </span>
            <span>Camera</span>

            <span class="mr-1">
              <div
                draggable="true"
                class="pcm_floorplan_object"
              >
                <feather-icon
                  icon="WifiIcon"
                  size="16"
                />
              </div>
            </span>
            <span>WAP</span>

            <span class="mr-1">
              <div
                draggable="true"
                class="pcm_floorplan_object"
              >
                <feather-icon
                  icon="GridIcon"
                  size="16"
                />
              </div>
            </span>
            <span>Walljack</span>

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
        >
          <img
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
  },
  directives: {
		Ripple,
	},
  data() {
    return {
    }
  },
  computed: {
  },
  methods: {
    StartDrag: function(TransferData, e) {
      e.dataTransfer.setData('floorplan_object_type', TransferData.floorplan_object_type)
    },
    UploadFloorplanImageClicked: function() {

      const vm = this
      vm.$bvModal.show('modal-file-upload')

    },
    HandleDrop: function(event) {

      // Store data
      const vm = this
      const LocationID = vm.CabinetData.id
      const OffsetX = event.offsetX
      const OffsetY = event.offsetY
      const FloorplanAddress = [OffsetX, OffsetY]
      const FloorplanObjectType = event.dataTransfer.getData('floorplan_object_type')

      const data = {
        "location_id": LocationID,
        "floorplan_address": FloorplanAddress,
        "floorplan_object_type": FloorplanObjectType,
      }

      vm.$emit('FloorplanObjectDropped', data )
    },
  }
}
</script>
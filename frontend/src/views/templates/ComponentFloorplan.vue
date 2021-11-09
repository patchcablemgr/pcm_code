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
      @FileSelected="FileSelected($event)"
      @FileSubmitted="FileSubmitted()"
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
    NodeIDSelected: {type: Number},
  },
  data() {
    return {
    }
  },
  computed: {
    FloorplanImage: function() {
      
      const vm = this
      const NodeID = vm.NodeIDSelected
      let NodeFloorplanImage = null

      if(NodeID) {

        const Criteria = {
          "id": NodeID.toString()
        }
        const SelectedNode = vm.$refs.LiquorTree.find(Criteria)[0]
        const NodeType = SelectedNode.data.type
        NodeFloorplanImage = SelectedNode.data.img
      }

      return NodeFloorplanImage
    },
  },
  methods: {
    UploadFloorplanImageClicked: function() {

      const vm = this
      vm.$bvModal.show('modal-file-upload')

    },
    HandleDrop: function(event) {

      // Store data
      const vm = this
      const data = {
        "event": event,
      }

      vm.$emit('FloorplanObjectDropped', data )
    },
  }
}
</script>
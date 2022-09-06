<template>
  <div>
    <b-card>
      <b-card-title>
        <div class="d-flex flex-wrap justify-content-between">
          <div class="demo-inline-spacing">
            Floorplan
          </div>
          <div class="demo-inline-spacing"
            v-if="ObjectsAreDraggable"
          >

            <div
              v-for="FloorplanTemplate in FloorplanTemplates"
              :key="FloorplanTemplate.id"
              style="display: flex;"
            >
              <div
                draggable="true"
                @dragstart.stop="StartDrag({ 'context': 'template', 'floorplan_object_type': FloorplanTemplate.type }, $event)"
                class="pcm_floorplan_object mr-1"
                style="cursor:grab;"
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
            :draggable="ObjectsAreDraggable"
            class="pcm_floorplan_object"
            :class="{
              pcm_template_partition_selected: FloorplanIsSelected(FloorplanObject.id),
              pcm_template_partition_hovered: FloorplanIsHovered(FloorplanObject.id),
            }"
            @dragstart.stop="StartDrag({ 'context': 'actual', 'floorplan_object_id': FloorplanObject.id }, $event)"
            @click.stop=" FloorplanClicked({'ObjectID': FloorplanObject.id, Context}) "
            @mouseover.stop=" FloorplanHovered({'ObjectID': FloorplanObject.id, 'HoverState': true, Context}) "
            @mouseleave.stop=" FloorplanHovered({'ObjectID': FloorplanObject.id, 'HoverState': false, Context}) "
            :style="{position: 'absolute', left: FloorplanObject.floorplan_address[0]+'px', top: FloorplanObject.floorplan_address[1]+'px'}"
            style="cursor:grab;"
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
      Title="Floorplan Image"
      UploadType="floorplanImg"
      :Context="Context"
      :TemplateFaceSelected="TemplateFaceSelected"
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
import { PCM } from '@/mixins/PCM.js'
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
    Context: {type: String},
    FloorplanImage: {type: String},
    File: {type: File},
    TemplateFaceSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
    ObjectsAreDraggable: {type: Boolean},
  },
  directives: {
		Ripple,
	},
  data() {
    return {
    }
  },
  computed: {
    FloorplanTemplates() {
      return this.$store.state.pcmFloorplanTemplates.FloorplanTemplates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    StateSelected() {
      return this.$store.state.pcmState.Selected
    },
    FloorplanObjects: function() {

      const vm = this
      const Context = vm.Context
      const LocationID = vm.StateSelected[Context].location_id
      const FloorplanObjects = vm.Objects[Context].filter((object) => object.location_id == LocationID )

      return FloorplanObjects
    }
  },
  methods: {
    GetFloorplanIcon: function(FloorplanObjectID) {

      const vm = this
      const Context = vm.Context

      const ObjectIndex = vm.GetObjectIndex(FloorplanObjectID, Context)
      const Object = vm.Objects[Context][ObjectIndex]
      const FloorplanObjectType = Object.floorplan_object_type
      const FloorplanTemplateIndex = vm.FloorplanTemplates.findIndex((floorplanTemplate) => floorplanTemplate.type == FloorplanObjectType)
      const FloorplanTemplate = vm.FloorplanTemplates[FloorplanTemplateIndex]
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

      } else if(Context == 'actual') {

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
      const Context = vm.Context
      const TemplateContext = event.dataTransfer.getData('context')
      const CursorOffsetX = event.dataTransfer.getData('cursor_offset_x')
      const CursorOffsetY = event.dataTransfer.getData('cursor_offset_y')
      const ElemOffsetX = event.offsetX
      const ElemOffsetY = event.offsetY
      const PosX = parseInt(ElemOffsetX) - parseInt(CursorOffsetX)
      const PosY = parseInt(ElemOffsetY) - parseInt(CursorOffsetY)
      const FloorplanAddress = [PosX, PosY]
      
      let url
      let data = {
        "floorplan_address": FloorplanAddress,
      }

      if(TemplateContext == 'template') {

        const LocationID = vm.StateSelected[Context].location_id
        const FloorplanObjectType = event.dataTransfer.getData('floorplan_object_type')

        data.location_id = LocationID
        data.floorplan_object_type = FloorplanObjectType

        url = '/api/objects/floorplan'

        // POST to objects
        vm.$http.post(url, data).then(function(response){
          
          // Add floorplan object
          vm.$store.commit('pcmObjects/ADD_Object', {pcmContext:Context, data:response.data})

        }).catch(error => { vm.DisplayError(error) })

      } else if(TemplateContext == 'actual') {

        const FloorplanObjectID = event.dataTransfer.getData('floorplan_object_id')

        data.floorplan_object_id = FloorplanObjectID

        url = '/api/objects/'+FloorplanObjectID

        // PATCH to objects
        vm.$http.patch(url, data).then(function(response){
          
          // Update floorplan object
          vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:Context, data:response.data})

        }).catch(error => { vm.DisplayError(error) })

      }
    },
  }
}
</script>
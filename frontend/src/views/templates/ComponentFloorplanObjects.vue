<!-- Templates -->

<template>
  <b-card
    title="Floorplan Objects"
  >
    <b-card-body>

      <!-- Filter -->
      <label for="template-filter">Filter:</label>
      <b-form-tags
        v-model="FilterArray"
        input-id="template-filter"
        class="mb-2"
      />

      <!-- Templates -->
      <app-collapse>
        <app-collapse-item
          v-for="FloorplanTemplate in FloorplanTemplates"
          :key="FloorplanTemplate.id"
          :title="FloorplanTemplate.name+' ('+FloorplanObjectsCount(FloorplanTemplate.floorplan_object_type)+')'"
        >
          <b-list-group>
            <b-list-group-item
              v-for="FloorplanObject in FilteredFloorplanObjects(FloorplanTemplate.floorplan_object_type)"
              :key="FloorplanObject.id"
              @click.stop=" FloorplanClicked({'ObjectID': FloorplanObject.id, Context}) "
              @mouseover.stop=" FloorplanHovered({'ObjectID': FloorplanObject.id, 'HoverState': true, Context}) "
              @mouseleave.stop=" FloorplanHovered({'ObjectID': FloorplanObject.id, 'HoverState': false, Context}) "
              :variant="(SelectedFloorplanObjectID == FloorplanObject.id) ? 'primary' : null "
              style="cursor:pointer;"
            >
              {{ FloorplanObject.name }}
            </b-list-group-item>
          </b-list-group>
        </app-collapse-item>
      </app-collapse>

    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BFormTags, BFormRadio, BListGroup, BListGroupItem } from 'bootstrap-vue'
import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import { PCM } from '@/mixins/PCM.js'

const FilterArray = []

export default {
  mixins: [PCM],
  components: {
    BCard,
    BCardBody,
    BFormTags,
    BFormRadio,
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
  },
  props: {
    Context: {type: String},
    PartitionAddressHovered: {type: Object},
  },
  data() {
    return {
      FilterArray,
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
    },
    SelectedFloorplanObjectID: function() {

      const vm = this
      const Context = vm.Context
      return vm.StateSelected[Context].object_id
    }
  },
  methods: {
    FloorplanObjectsCount: function(FloorplanTemplateType) {

      // Store data
      const vm = this

      const FilteredFloorplanObjects = vm.FloorplanObjects.filter(object => object.floorplan_object_type == FloorplanTemplateType)

      return FilteredFloorplanObjects.length

    },
    FilteredFloorplanObjects: function(FloorplanTemplateType){

      const vm = this
      const Context = vm.Context
      const FilteredFloorplanObjects = vm.FloorplanObjects.filter(function(object) {

        let match = false

				// Include 'standard' templates only.  'Insert' templates will be rendered by pseudo parent objects
				if(object.floorplan_object_type == FloorplanTemplateType) {

          // Include if no filter tags
          if(vm.FilterArray.length == 0) {
            match = true
          } else {

            // Compare each filter tag
            vm.FilterArray.forEach(function(tag){

              // Include if template name contains filter tag
              if(object.name.toLowerCase().includes(tag.toLowerCase())) {
                match = true
              }
            })
          }
				}

        return match
      })

      return FilteredFloorplanObjects
    },
  }
}
</script>
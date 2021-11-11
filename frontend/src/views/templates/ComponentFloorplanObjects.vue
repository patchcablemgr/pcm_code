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
          v-for="FloorplanTemplate in FloorplanTemplateData"
          :key="FloorplanTemplate.id"
          :title="FloorplanTemplate.name+' ('+FloorplanObjectsCount(FloorplanTemplate.type)+')'"
        >
          <div
            v-for="FloorplanObject in FilteredFloorplanObjects(FloorplanTemplate.type)"
            :key="FloorplanObject.id"
            @click.stop=" $emit('FloorplanClicked', {'object_id': FloorplanObject.id}) "
            @mouseover.stop=" $emit('FloorplanHovered', {'object_id': FloorplanObject.id, 'hover_state': true}) "
            @mouseleave.stop=" $emit('FloorplanHovered', {'object_id': FloorplanObject.id, 'hover_state': false}) "
          >
            {{ FloorplanObject.name }}
          </div>
        </app-collapse-item>
      </app-collapse>

    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BFormTags, BFormRadio } from 'bootstrap-vue'
import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'

const FilterArray = []

export default {
  components: {
    BCard,
    BCardBody,
    BFormTags,
    BFormRadio,
    AppCollapse,
    AppCollapseItem,
  },
  props: {
    FloorplanTemplateData: {type: Array},
    ObjectData: {type: Object},
  },
  data() {
    return {
      FilterArray,
    }
  },
  computed: {
  },
  methods: {
    FloorplanObjectsCount: function(FloorplanTemplateType) {

      // Store data
      const vm = this
      const Context = 'preview'

      const FilteredFloorplanObjects = vm.ObjectData[Context].filter(object => object.floorplan_object_type == FloorplanTemplateType)

      return FilteredFloorplanObjects.length

    },
    FilteredFloorplanObjects: function(FloorplanTemplateType){

      const vm = this
      const Context = 'preview'
      const FilteredFloorplanObjects = vm.ObjectData[Context].filter(function(object) {

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
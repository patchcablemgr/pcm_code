<!-- Templates -->

<template>
  <div>

    <!-- TemplateFace -->
    <b-form-radio
      v-model="ComputedTemplateFace"
      plain
      value="front"
    >Front
    </b-form-radio>
    <b-form-radio
      v-model="ComputedTemplateFace"
      plain
      value="rear"
    >
      Rear
    </b-form-radio>

    <!-- Filter -->
    <label for="template-filter">Filter:</label>
    <b-form-tags
      v-model="TemplateFilter"
      input-id="template-filter"
      class="mb-2"
    />

    <!-- Templates -->
    <app-collapse>
      <app-collapse-item
        v-for="Category in CategoryData"
        :key="Category.id"
        :title="Category.name+' ('+CategoryTemplateCount(Category.id)+')'"
      >
        <div
          v-for="Template in FilteredCategoryTemplates(Category.id)"
          :key="Template.id"
        >
          {{ Template.name }}
          <table
            style="width:100%;"
          >
            <tr
              class="pcm_cabinet_row"
              v-for="RU in Template.ru_size"
              :key="RU"
            >
              <td
                class="pcm_cabinet_ru"
                :rowspan=" (RU == 1) ? 0 : '' "
              >
                <div
                  :class="{
                    pcm_template_partition_selected: PartitionAddressSelected[Context][TemplateFaceSelected[Context]].length === 0,
                    pcm_template_partition_hovered: PartitionAddressHovered[Context][TemplateFaceSelected[Context]].length === 0,
                  }"
                  :style="{
                    'background-color': ObjectCategoryData(Category.id).color,
                    'height': '100%',
                  }"
                  @mouseover.stop=" $emit('PartitionHovered', {'Context': Context, 'PartitionAddress': InitialDepthCounter, 'HoverState': true}) "
                  @mouseleave.stop=" $emit('PartitionHovered', {'Context': Context, 'PartitionAddress': InitialDepthCounter, 'HoverState': false}) "
                >
                  <component-object
                    v-if=" RU == 1 "
                    :TemplateBlueprint=" Template.blueprint[TemplateFaceSelected[Context]] "
                    :TemplateBlueprintOriginal=" Template.blueprint[TemplateFaceSelected[Context]] "
                    :TemplateRUSize=" Template.ru_size "
                    :InitialDepthCounter=" [] "
                    :Context="Context"
                    :TemplateFaceSelected="TemplateFaceSelected"
                    :PartitionAddressSelected="PartitionAddressSelected"
                    :PartitionAddressHovered="PartitionAddressHovered"
                    :TemplateID="Template.id"
                    @PartitionClicked=" $emit('PartitionClicked', $event) "
                    @PartitionHovered=" $emit('PartitionHovered', $event) "
                  />
                </div>
              </td>
            </tr>
          </table>
        </div>
      </app-collapse-item>
    </app-collapse>

  </div>
</template>

<script>
import { BFormTags, BFormRadio } from 'bootstrap-vue'
import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import ComponentObject from './ComponentObject.vue'

const InitialDepthCounter = []
const TemplateFilter = []

export default {
  components: {
    BFormTags,
    BFormRadio,
    AppCollapse,
    AppCollapseItem,
    ComponentObject,
  },
  props: {
    CategoryData: {type: Array},
    TemplatesData: {type: Array},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  data() {
    return {
      InitialDepthCounter,
      TemplateFilter,
    }
  },
  computed: {
    ComputedTemplateFace: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
        return vm.TemplateFaceSelected

      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        vm.$emit('TemplateFaceChanged', {'Context': Context, 'TemplateFace': newValue})

      }
    }
  },
  methods: {
    CategoryTemplateCount: function(CategoryID) {

      // Store data
      const vm = this;
      const TemplatesDataFiltered = vm.TemplatesData.filter(template => template.category_id == CategoryID)

      return TemplatesDataFiltered.length

    },
    ObjectCategoryData: function(CategoryID) {

      // Initial variables
      const vm = this;

      // Get category index
      const ObjectCategoryIndex = vm.CategoryData.findIndex((category) => category.id == CategoryID);

      // Get category
      const ObjectCategoryData = vm.CategoryData[ObjectCategoryIndex]

      // Return category
      return ObjectCategoryData
    },
    FilteredCategoryTemplates: function(CategoryID){

      const vm = this
      const FilteredCategoryTemplates = vm.TemplatesData.filter(function(template) {

        let match = false

        // Include if category ID matches
        if(template.category_id == CategoryID) {

          // Include if no filter tags
          if(vm.TemplateFilter.length == 0) {
            match = true
          } else {

            // Compare each filter tag
            vm.TemplateFilter.forEach(function(tag){

              // Include if template name contains filter tag
              if(template.name.toLowerCase().includes(tag.toLowerCase())) {
                match = true
              }
            })
          }
        }

        return match
      })

      return FilteredCategoryTemplates
    },
  }
}
</script>
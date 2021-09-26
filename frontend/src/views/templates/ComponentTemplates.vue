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
                <component-object
                  v-if=" RU == 1 && GetObjectID(Template.id)"
                  :TemplateData="TemplateData"
                  :ObjectData="ObjectData"
                  :CategoryData="CategoryData"
                  :TemplateRUSize=" Template.ru_size "
                  :InitialPartitionAddress=[]
                  :Context="Context"
                  :ObjectID="GetObjectID(Template.id)"
                  :TemplateFaceSelected="TemplateFaceSelected"
                  :PartitionAddressSelected="PartitionAddressSelected"
                  :PartitionAddressHovered="PartitionAddressHovered"
                  @PartitionClicked=" $emit('PartitionClicked', $event) "
                  @PartitionHovered=" $emit('PartitionHovered', $event) "
                />
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
    TemplateData: {type: Object},
    CategoryData: {type: Array},
    ObjectData: {type: Object},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
  },
  data() {
    return {
      TemplateFilter,
    }
  },
  computed: {
    ComputedTemplateFace: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
        return TemplateFaceSelected

      },
      set(newValue) {

        const vm = this
        const Context = vm.Context
        vm.$emit('TemplateFaceChanged', {'Context': Context, 'TemplateFace': newValue})

      }
    }
  },
  methods: {
    GetObjectIndex: function(TemplateID) {

      const vm = this
      const Context = vm.Context

      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.template_id == TemplateID)

      return ObjectIndex

    },
    GetObjectID: function(TemplateID) {

      const vm = this
      const Context = vm.Context

      const ObjectIndex = vm.GetObjectIndex(TemplateID)
      const ObjectID = vm.ObjectData[Context][ObjectIndex].id
      
      return ObjectID

    },
    PartitionIsSelected: function(TemplateID) {
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressSelected = vm.PartitionAddressSelected[Context][TemplateFaceSelected]
      const TemplateIDSelected = vm.PartitionAddressSelected[Context].template_id
      let PartitionIsSelected = false

      if(PartitionAddressSelected.length === 0 && TemplateIDSelected == TemplateID) {
        PartitionIsSelected = true
      }
      return PartitionIsSelected
    },
    PartitionIsHovered: function(TemplateID) {
      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const PartitionAddressHovered = vm.PartitionAddressHovered[Context][TemplateFaceSelected]
      const TemplateIDSelected = vm.PartitionAddressHovered[Context].template_id
      let PartitionIsHovered = false

      if(PartitionAddressHovered.length === 0 && TemplateIDSelected == TemplateID) {
        PartitionIsHovered = true
      }
      return PartitionIsHovered
    },
    CategoryTemplateCount: function(CategoryID) {

      // Store data
      const vm = this
      const Context = vm.Context
      const TemplateData = vm.TemplateData[Context]

      const TemplateDataFiltered = TemplateData.filter(template => template.category_id == CategoryID && template.type == 'standard')

      return TemplateDataFiltered.length

    },
    ObjectCategoryData: function(CategoryID) {

      // Initial variables
      const vm = this;

      // Get category index
      const ObjectCategoryIndex = vm.CategoryData.findIndex((category) => category.id == CategoryID)

      // Get category
      const ObjectCategoryData = vm.CategoryData[ObjectCategoryIndex]

      // Return category
      return ObjectCategoryData
    },
    FilteredCategoryTemplates: function(CategoryID){

      const vm = this
      const Context = vm.Context
      const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
      const FilteredCategoryTemplates = vm.TemplateData[Context].filter(function(template) {

        let match = false

				// Include 'standard' templates only.  'Insert' templates will be rendered by pseudo parent objects
				if(template.type == 'standard') {

          if(TemplateFaceSelected == 'front' || template.mount_config == '4-post') {

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
          }
				}

        return match
      })

      return FilteredCategoryTemplates
    },
  }
}
</script>
<!-- Templates -->

<template>
  <div>

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
        :title="Category.name"
      >
        <div
          v-for="Template in FilteredCategoryTemplates(Category.id)"
          :key="Template.id"
        >
          {{ Template.name }}
        </div>
      </app-collapse-item>
    </app-collapse>

  </div>
</template>

<script>
import { BFormTags } from 'bootstrap-vue'
import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'

export default {
  components: {
    BFormTags,
    AppCollapse,
    AppCollapseItem,
  },
  props: {
    CategoryData: {type: Array},
    TemplatesData: {type: Array},
  },
  data() {
    return {
      TemplateFilter: [],
    }
  },
  computed: {
  },
  methods: {
    FilteredCategoryTemplates: function(CategoryID){

      const vm = this
      const FilteredCategoryTemplates = vm.TemplatesData.filter(template => template.category_id == CategoryID)

      return FilteredCategoryTemplates
    }
  }
}
</script>
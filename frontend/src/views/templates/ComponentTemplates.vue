<!-- Templates -->

<template>
  <div>
  <b-card>
    <b-card-title class="mb-0">
      <div class="d-flex flex-wrap justify-content-between">
        <div class="demo-inline-spacing">
          Templates
        </div>
        <div
          v-if="Context != 'catalog'"
          class="demo-inline-spacing"
        >
          <b-dropdown
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            right
            size="sm"
            text="Actions"
            variant="primary"
          >
            <b-dropdown-item
              v-b-modal.modal-template-catalog
            >
              Import
            </b-dropdown-item>

          </b-dropdown>
        </div>
      </div>
    </b-card-title>
    <b-card-body>

      <!-- TemplateFace -->
      <div class="demo-inline-spacing mb-1">
        <b-form-radio
          v-model="ComputedTemplateFace"
          value="front"
        >Front
        </b-form-radio>
        <b-form-radio
          v-model="ComputedTemplateFace"
          value="rear"
        >Rear
        </b-form-radio>
      </div>

      <!-- Filter -->
      <b-form-tags
        v-model="TemplateFilter"
        placeholder="Add filter tags..."
        input-id="template-filter"
        class="mb-2"
      />

      <!-- Templates -->
      <app-collapse>
        <app-collapse-item
          v-for="Category in Categories[Context]"
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
                    :TemplateRUSize=" Template.ru_size "
                    :InitialPartitionAddress=[]
                    :Context="Context"
                    :ObjectID="GetObjectID(Template.id)"
                    :CabinetFace="TemplateFaceSelected[Context]"
                    :PartitionAddressHovered="PartitionAddressHovered"
                    :ObjectsAreDraggable="ObjectsAreDraggable"
                  />
                </td>
              </tr>
            </table>
          </div>
        </app-collapse-item>
      </app-collapse>

    </b-card-body>
  </b-card>

  <!-- Modal Template Catalog -->
  <modal-template-catalog
    ModalID="modal-template-catalog"
    Context="catalog"
    :TemplateFaceSelected="TemplateFaceSelected"
    :PartitionAddressHovered="PartitionAddressHovered"
    @SetTemplateFaceSelected=" $emit('SetTemplateFaceSelected', $event) "
  />

  </div>
</template>

<script>
import { BCard, BCardTitle, BCardBody, BDropdown, BDropdownItem, BFormTags, BFormRadio } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import ComponentObject from '@/views/templates/ComponentObject.vue'
import ModalTemplateCatalog from '@/views/templates/ModalTemplateCatalog.vue'

const TemplateFilter = []

export default {
  components: {
    BCard,
    BCardTitle,
    BCardBody,
    BDropdown,
    BDropdownItem,
    BFormTags,
    BFormRadio,
    AppCollapse,
    AppCollapseItem,
    
    ComponentObject,
    ModalTemplateCatalog,
  },
  directives: {
    Ripple,
  },
  props: {
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressHovered: {type: Object},
    ObjectsAreDraggable: {type: Boolean},
  },
  data() {
    return {
      TemplateFilter,
    }
  },
  computed: {
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
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
        vm.$emit('SetTemplateFaceSelected', {'Context': Context, 'Face': newValue})

      }
    }
  },
  methods: {
    GetObjectIndex: function(TemplateID) {

      const vm = this
      const Context = vm.Context
      
      const ObjectIndex = vm.Objects[Context].findIndex((object) => object.template_id == TemplateID)

      return ObjectIndex

    },
    GetObjectID: function(TemplateID) {

      const vm = this
      const Context = vm.Context

      const ObjectIndex = vm.GetObjectIndex(TemplateID)
      
      const ObjectID = vm.Objects[Context][ObjectIndex].id
      
      return ObjectID

    },
    CategoryTemplateCount: function(CategoryID) {

      // Store data
      const vm = this
      const Context = vm.Context

      const TemplatesFiltered = vm.Templates[Context].filter(template => template.category_id == CategoryID && template.type == 'standard')

      return TemplatesFiltered.length

    },
    FilteredCategoryTemplates: function(CategoryID){

      const vm = this
      const Context = vm.Context
      const Face = vm.TemplateFaceSelected[Context]
      const FilteredCategoryTemplates = vm.Templates[Context].filter(function(template) {

        let match = false

				// Include 'standard' templates only.  'Insert' templates will be rendered by pseudo parent objects
				if(template.type == 'standard') {

          if(Face == 'front' || template.mount_config == '4-post') {

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
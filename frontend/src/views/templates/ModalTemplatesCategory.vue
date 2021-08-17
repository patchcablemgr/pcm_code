<template>
    <!-- Template categories modal -->
    <b-modal
      id="modal-templates-category"
      title="Template Categories"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>

          <b-card
            title="Create/Edit"
            class="position-static"
          >
            <b-form @submit.prevent=" $emit('TemplateCategorySubmitted', LocalCategoryData) ">

              <!-- Name -->
              <dl class="row">
                <dt class="col-sm-4">
                  Name
                </dt>
                <dd class="col-sm-8">
                  <b-form-input
                    v-model="ComputedCategoryName"
                    placeholder="New_Category"
                  />
                </dd>
              </dl>

              <!-- Default -->
              <dl class="row">
                <dt class="col-sm-4">
                  Default
                </dt>
                <dd class="col-sm-8">
                  <b-form-checkbox
                    v-model="ComputedCategoryDefault"
                  />
                </dd>
              </dl>

              <!-- Color -->
              <dl class="row">
                <dt class="col-sm-4">
                  Color
                </dt>
                <dd class="col-sm-8">
                  <sketch v-model="ComputedCategoryColor" />
                </dd>
              </dl>

              <!-- Submit/Update -->
              <div offset-md="4">
                <b-button
                  v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                  type="submit"
                  :variant="SelectedCategoryID ? 'secondary' : 'primary'"
                  class="mr-1"
                >
                  {{ (SelectedCategoryID) ? "Update" : "Create" }}
                </b-button>

                <!-- Reset -->
                <b-button
                  v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                  type="button"
                  variant="warning"
                  class="mr-1"
                  @click="Reset()"
                >
                  Reset
                </b-button>

                <!-- Delete -->
                <b-button
                  v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                  type="button"
                  variant="danger"
                  class="mr-1"
                  @click=" $emit('TemplateCategoryDeleted', {'CategoryID': SelectedCategoryID} ) "
                  :disabled="!SelectedCategoryID"
                >
                  Delete
                </b-button>
              </div>
            </b-form>
          </b-card>
          
        </b-col>
        <b-col>
          <b-card
            title="Current"
            class="position-static"
          >
            <b-card-text>
              <div
                v-for="(Category) in CategoryData"
                v-bind:key="Category.id"
                class="pcm_box"
                :class="{
                  pcm_template_partition_selected: Category.id == SelectedCategoryID,
                  pcm_template_partition_hovered: Category.id == HoveredCategoryID,
                }"
                :style="{ background: Category.color}"
                @click="CategorySelected(Category.id)"
                @mouseover.stop=" CategoryMouseOver(Category.id) "
                @mouseleave.stop=" CategoryMouseOver(null) "
              >
                <span
                  v-if="Category.default"
                >
                *
                </span>
                {{ Category.name }} ({{ CategoryTemplateCount(Category.id) }})
              </div>
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>
    </b-modal>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BForm, BButton, BFormInput, BFormCheckbox, BCardText, } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { Sketch } from 'vue-color'

const DefaultCategoryName = ''
const DefaultCategoryDefault = false
const DefaultCategoryColor = '#194D33FF'
const HoveredCategoryID = null
const LocalCategoryData = {"name": DefaultCategoryName, "default": DefaultCategoryDefault, "color": DefaultCategoryColor}

export default {
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BForm,
    BButton,
    BFormInput,
    BFormCheckbox,
    BCardText,
    Sketch,
  },
  directives: {
    Ripple,
  },
  props: {
    TemplateData: {type: Object},
    CategoryData: {type: Array},
    SelectedCategoryID: {type: Number},
    Context: {type: String},
  },
  data () {
    return {
      DefaultCategoryName,
      DefaultCategoryDefault,
      DefaultCategoryColor,
      HoveredCategoryID,
      LocalCategoryData,
    }
  },
  computed: {
    ComputedCategoryName: {
      get() {

        // Store variables
        const vm = this
        const SelectedCategoryID = vm.SelectedCategoryID

        if(SelectedCategoryID) {

          const CategoryIndex = vm.CategoryData.findIndex((category) => category.id == SelectedCategoryID);
          return vm.CategoryData[CategoryIndex].name
        } else {
          return vm.DefaultCategoryName
        }
        
      },
      set(newValue) {

        // Store variables
        const vm = this
        vm.LocalCategoryData.name = newValue

        return
      }
    },
    ComputedCategoryDefault: {
      get() {

        // Store variables
        const vm = this
        const SelectedCategoryID = vm.SelectedCategoryID

        if(SelectedCategoryID) {

          const CategoryIndex = vm.CategoryData.findIndex((category) => category.id == SelectedCategoryID);
          return !!vm.CategoryData[CategoryIndex].default
        } else {
          return vm.DefaultCategoryDefault
        }
        
      },
      set(newValue) {

        // Store variables
        const vm = this
        vm.LocalCategoryData.default = newValue
        
        return
      }
    },
    ComputedCategoryColor: {
      get() {

        // Store variables
        const vm = this
        const SelectedCategoryID = vm.SelectedCategoryID

        if(SelectedCategoryID) {

          const CategoryIndex = vm.CategoryData.findIndex((category) => category.id == SelectedCategoryID);
          return vm.CategoryData[CategoryIndex].color
        } else {
          return vm.DefaultCategoryColor
        }
        
      },
      set(newValue) {

        // Store variables
        const vm = this
        vm.LocalCategoryData.color = newValue.hex8
        
        return
      }
    },
  },
  methods: {
    CategoryTemplateCount: function(CategoryID) {

      // Store data
      const vm = this
      const Context = vm.Context
      const TemplateData = vm.TemplateData[Context]
      const TemplateDataFiltered = TemplateData.filter(template => template.category_id == CategoryID)

      return TemplateDataFiltered.length

    },
    CategoryMouseOver: function(CategoryID) {

      // Store data
      const vm = this;

      vm.HoveredCategoryID = CategoryID
    },
    CategorySelected: function(CategoryID) {

      const vm = this
      const SelectedCategoryIndex = vm.CategoryData.findIndex((category) => category.id == CategoryID);
      const SelectedCategory = vm.CategoryData[SelectedCategoryIndex]

      // Set local category data to currently selected category data
      vm.LocalCategoryData.name = SelectedCategory.name
      vm.LocalCategoryData.default = SelectedCategory.default
      vm.LocalCategoryData.color = SelectedCategory.color

      vm.$emit('TemplateCategorySelected', {'CategoryID': CategoryID} )

    },
    Reset: function() {

      // Reset category form
      const vm = this
      vm.$emit('TemplateCategorySelected', {'CategoryID': null} )
      vm.LocalCategoryData.name = vm.DefaultCategoryName
      vm.LocalCategoryData.default = vm.DefaultCategoryDefault
      vm.LocalCategoryData.color = vm.DefaultCategoryColor

    },
  },
}
</script>
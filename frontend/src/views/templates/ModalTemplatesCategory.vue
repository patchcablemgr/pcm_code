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
            <b-form>

              <!-- Name -->
              <dl class="row">
                <dt class="col-sm-4">
                  Name
                </dt>
                <dd class="col-sm-8">
                  <b-form-input
                    v-model="LocalCategoryData.name"
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
                    v-model="LocalCategoryData.default"
                  />
                </dd>
              </dl>

              <!-- Color -->
              <dl class="row">
                <dt class="col-sm-4">
                  Color
                </dt>
                <dd class="col-sm-8">
                  <sketch v-model="LocalCategoryData.color" />
                </dd>
              </dl>

              <!-- Submit/Update -->
              <div offset-md="4">
                <b-button
                  v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                  type="button"
                  :variant="SelectedCategoryID ? 'secondary' : 'primary'"
                  class="mr-1"
                  @click="Submit()"
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
                  @click="Delete()"
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
                v-for="(Category) in Categories"
                v-bind:key="Category.id"
                class="pcm_box"
                :class="{
                  pcm_template_partition_selected: Category.id == SelectedCategoryID,
                  pcm_template_partition_hovered: Category.id == HoveredCategoryID,
                }"
                :style="{ background: Category.color}"
                @click="Clicked(Category.id)"
                @mouseover.stop="HoveredCategoryID = Category.id"
                @mouseleave.stop="HoveredCategoryID = null"
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
const SelectedCategoryID = null
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
  props: {},
  data () {
    return {
      DefaultCategoryName,
      DefaultCategoryDefault,
      DefaultCategoryColor,
      HoveredCategoryID,
      SelectedCategoryID,
      LocalCategoryData,
    }
  },
  computed: {
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
  },
  methods: {
    Clicked: function(CategoryID) {

      const vm = this
      const SelectedCategoryID = vm.SelectedCategoryID

      if(SelectedCategoryID == CategoryID) {

        vm.Reset()
      } else {

        vm.SelectedCategoryID = CategoryID
        const CategoryIndex = vm.Categories.findIndex((category) => category.id == CategoryID)
        const Category = vm.Categories[CategoryIndex]
        vm.LocalCategoryData.name = Category.name
        vm.LocalCategoryData.default = Category.default
        vm.LocalCategoryData.color = Category.color
      }
    },
    CategoryTemplateCount: function(CategoryID) {

      // Store data
      const vm = this
      const TemplatesFiltered = vm.Templates.template.filter((template) => template.category_id == CategoryID)

      return TemplatesFiltered.length

    },
    Submit: function() {

      const vm = this
      const SelectedCategoryID = vm.SelectedCategoryID
      let data = {
        'vm': vm,
        'data': this.LocalCategoryData
      }
      
      if(SelectedCategoryID) {
        data.data.id = SelectedCategoryID
        this.$store.dispatch('pcmCategories/PATCH_Categories', data)
      } else {
        this.$store.dispatch('pcmCategories/POST_Categories', data)
      }
    },
    Delete: function() {

      const vm = this
      const data = {
        'vm': vm,
        'data': {
          'id': vm.SelectedCategoryID
        }
      }

      vm.SelectedCategoryID = null
      this.$store.dispatch('pcmCategories/DELETE_Categories', data)
    },
    Reset: function() {

      // Reset category form
      const vm = this
      vm.SelectedCategoryID = null
      vm.LocalCategoryData.name = vm.DefaultCategoryName
      vm.LocalCategoryData.default = vm.DefaultCategoryDefault
      vm.LocalCategoryData.color = vm.DefaultCategoryColor

    },
  },
}
</script>
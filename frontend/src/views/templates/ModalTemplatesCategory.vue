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
            <b-form @submit.prevent="(CategorySelected) ? categoryPUT() : categoryPOST()">

              <!-- Name -->
              <dl class="row">
                <dt class="col-sm-4">
                  Name
                </dt>
                <dd class="col-sm-8">
                  <b-form-input
                    v-model="CategoryName"
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
                    v-model="CategoryDefault"
                  />
                </dd>
              </dl>

              <!-- Color -->
              <dl class="row">
                <dt class="col-sm-4">
                  Color
                </dt>
                <dd class="col-sm-8">
                  <sketch v-model="CategoryColor" />
                </dd>
              </dl>

              <!-- Submit/Update -->
              <div offset-md="4">
                <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    type="submit"
                    :variant="CategorySelected ? 'secondary' : 'primary'"
                    class="mr-1"
                >
                  {{ (CategorySelected) ? "Update" : "Create" }}
                </b-button>

                <!-- Reset -->
                <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    type="button"
                    variant="warning"
                    class="mr-1"
                    @click="formReset"
                >
                  Reset
                </b-button>

                <!-- Delete -->
                <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    type="button"
                    variant="danger"
                    class="mr-1"
                    @click="categoryDELETE"
                    :disabled="!CategorySelected"
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
                v-for="(Category, CategoryIndex) in CategoryData"
                v-bind:key="Category.id"
                class="pcm_box"
                :class="Category.id == CategorySelected ? 'pcm_selected' : ''"
                :style="{ background: Category.color}"
                @click="categorySelected(CategoryIndex)"
              >
                <span
                  v-if="Category.default"
                >
                *
                </span>
                {{ Category.name }} ({{ CategoryIndex }})
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

const CategorySelected = null
const CategoryName = ""
const CategoryColor = {"hex8": "#194D33FF"}
const CategoryDefault = false
const Categories = []

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
    CategoryData: {type: Array},
  },
  data () {
    return {
      CategorySelected,
      CategoryName,
      CategoryColor,
      CategoryDefault,
      Categories,
    }
  },
  methods: {
    categorySelected: function(CategoryIndex) {

      // Set Categories selected index
      const selectedCategory = this.CategoryData[CategoryIndex]

      // Set category form to selected category values
      this.CategorySelected = selectedCategory.id
      this.CategoryName = selectedCategory.name
      this.CategoryColor = selectedCategory.color
      this.CategoryDefault = (selectedCategory.default) ? true : false
    },
    categoryPUT: function() {

      // Store data
      const vm = this;
      const categoryID = vm.CategorySelected
      const url = '/api/category/'+categoryID
      const data = {
        name: vm.CategoryName,
        color: vm.CategoryColor.hex8,
        default: vm.CategoryDefault,
      };

      // PUT category form data
      vm.$http.put(url, data).then(function(response){

        // Update categories
        vm.$emit('TemplateCategoriesUpdated')

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
    categoryPOST: function() {

      // Store data
      const vm = this;
      const url = '/api/category'
      const data = {
        name: vm.CategoryName,
        color: vm.CategoryColor.hex8,
        default: vm.CategoryDefault,
      };

      // POST category form data
      this.$http.post(url, data).then(function(response){

        // Update categories
        vm.$emit('TemplateCategoriesUpdated')

      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
    formReset: function() {

      // Reset category form
      this.CategorySelected = CategorySelected
      this.CategoryName = CategoryName
      this.CategoryColor = CategoryColor
      this.CategoryDefault = CategoryDefault

    },
    categoryDELETE: function() {

      // Store data
      const vm = this;
      const categoryID = vm.CategorySelected
      const url = '/api/category/'+categoryID

      // DELETE category form data
      vm.$http.delete(url).then(function(response){

        // Update categories variable
        const returnedCategoryID = response.data.id
        const categoryIndex = vm.Categories.findIndex((category) => category.id == returnedCategoryID);
        vm.Categories.splice(categoryIndex, 1)
        vm.$emit('categoriesUpdated', vm.Categories)

        // Reset form
        vm.formReset()
        
      }).catch(error => {

        // Display error to user via toast
        vm.$bvToast.toast(JSON.stringify(error.response.data), {
          title: 'Error',
          variant: 'danger',
        })

      });
    },
  },
  mounted(){

    const vm = this;

    //vm.categoryGET();
  },
}
</script>
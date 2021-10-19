<!-- Template/Object Details -->

<template>
	
  <b-card
		:title="CardTitle"
	>
		
			<div class="d-flex flex-wrap justify-content-between">
				<div class="demo-inline-spacing">
					<b-card-title/>
				</div>
				<div class="demo-inline-spacing">
					<b-dropdown
						v-ripple.400="'rgba(255, 255, 255, 0.15)'"
						right
						size="sm"
						text="Actions"
						variant="primary"
					>
						<b-dropdown-item
              @click=" $emit('TemplateObjectEditClicked') "
              :disabled="!TemplateSelected"
            >Edit
            </b-dropdown-item>

						<b-dropdown-item
              v-if="Context == 'template'"
              @click=" $emit('TemplateObjectCloneClicked') "
              :disabled="!TemplateSelected"
            >Clone
            </b-dropdown-item>

						<b-dropdown-item
              v-if="Context == 'template'"
              :disabled="!TemplateSelected"
            >Where Used</b-dropdown-item>

						<b-dropdown-divider />

						<b-dropdown-item
							variant="danger"
							@click=" $emit('TemplateObjectDeleteClicked') "
              :disabled="!TemplateSelected"
						>Delete</b-dropdown-item>

					</b-dropdown>
				</div>
			</div>
		<b-card-body>
		
    <div
      class="h5 font-weight-bolder m-0"
    >
			General:
    </div>
    <hr
      class="separator mt-0"
    >

    <!-- Object Name -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Object Name:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedObjectName}}
      </dd>
    </dl>

    <!-- Template Name -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Template Name:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedTemplateName}}
      </dd>
    </dl>

    <!-- Category -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Category:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedCategoryName}}
      </dd>
    </dl>

    <!-- Type -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Template Type:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedType}}
      </dd>
    </dl>

    <!-- Function -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Function:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedFunction}}
      </dd>
    </dl>

    <!-- RU Size -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        RU Size:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedRUSize}}
      </dd>
    </dl>

    <!-- Mount Config -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Mount Config:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedMountConfig}}
      </dd>
    </dl>

    <!-- Image -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Image:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedCategoryName}}
      </dd>
    </dl>

    <div
      class="h5 font-weight-bolder m-0"
    >
      Partition:
    </div>
    <hr
      class="separator mt-0"
    >

    <!-- Partition Type -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Partition Type:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedPartitionType}}
      </dd>
    </dl>

    <!-- Trunked To -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Trunked To:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedTrunkedTo}}
      </dd>
    </dl>

    <!-- Port Range -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Port Range:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{TemplatePartitionPortRange}}
      </dd>
    </dl>

    <!-- Port Orientation -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Port Orientation:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedPortOrientation}}
      </dd>
    </dl>

    <!-- Port Type -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Port Type:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedPortType}}
      </dd>
    </dl>

    <!-- Media Type -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Media Type:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedPortMediaType}}
      </dd>
    </dl>

    <!-- Enclosure Tolerance -->
    <dl class="row mb-0">
      <dt class="col-sm-4">
        Enclosure Tolerance:
      </dt>
      <dd class="col-sm-8 mb-0">
        {{ComputedCategoryName}}
      </dd>
    </dl>

  </b-card-body>
  
  </b-card>

</template>

<script>
import { BCard, BCardTitle, BCardBody, BDropdown, BDropdownItem, BDropdownDivider } from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'

export default {
  components: {
		BCard,
		BCardTitle,
		BCardBody,
		BDropdown,
		BDropdownItem,
		BDropdownDivider,
  },
	directives: {
		Ripple,
	},
  props: {
    CardTitle: {type: String},
    TemplateData: {type: Object},
    CategoryData: {type: Array},
    ObjectData: {type: Object},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    TemplatePartitionPortRange: {type: String},
    PortOrientationData: {type: Array},
  },
  data() {
    return {
    }
  },
  computed: {
    TemplateSelected: function(){

      const vm = this
      const Context = vm.Context
      const TemplateID = vm.PartitionAddressSelected[Context].template_id

      return (TemplateID === null) ? false : true
    },
    ComputedObjectName: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.PartitionAddressSelected[Context].object_id
        let ReturnString = '-'

        console.log('Debug (ComputedObjectName-Context): '+Context)

        if(Context == 'preview') {
          if(ObjectID) {
            const ObjectIndex = vm.GetObjectIndex(ObjectID)
            ReturnString = vm.ObjectData[Context][ObjectIndex].name
          }
        } else {
          ReturnString = 'N/A'
        }

        return ReturnString
      },
    },
    ComputedTemplateName: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'
        console.log('Debug (ComputedTemplateName-Context): '+Context)
        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          ReturnString = vm.TemplateData[Context][TemplateIndex].name
        }

        return ReturnString
      },
    },
    ComputedCategoryName: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          const CategoryID = vm.TemplateData[Context][TemplateIndex].category_id
          const CategoryIndex = vm.GetCategoryIndex(CategoryID)
          ReturnString = vm.CategoryData[CategoryIndex].name
        }

        return ReturnString
      },
    },
    ComputedTrunkedTo: {
      get() {

        const vm = this
        const Context = vm.Context
        const ObjectID = vm.PartitionAddressSelected[Context].object_id
        let ReturnString = '-'

        if(ObjectID) {
          ReturnString = 'Trunked To'
        }

        return ReturnString
      },
    },
    ComputedType: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          ReturnString = vm.TemplateData[Context][TemplateIndex].type
          ReturnString = ReturnString.charAt(0).toUpperCase() + ReturnString.slice(1)
        }

        return ReturnString
      },
    },
    ComputedFunction: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          ReturnString = vm.TemplateData[Context][TemplateIndex].function
          ReturnString = ReturnString.charAt(0).toUpperCase() + ReturnString.slice(1)
        }

        return ReturnString
      },
    },
    ComputedRUSize: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          ReturnString = vm.TemplateData[Context][TemplateIndex].ru_size
        }

        return ReturnString
      },
    },
    ComputedMountConfig: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          ReturnString = vm.TemplateData[Context][TemplateIndex].mount_config
        }

        return ReturnString
      },
    },
    ComputedPartitionType: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        let ReturnString = '-'

        if(TemplateID) {

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          const Template = vm.TemplateData[Context][TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          ReturnString = (Partition.type) ? Partition.type : 'generic'
          ReturnString = ReturnString.charAt(0).toUpperCase() + ReturnString.slice(1)
        }

        return ReturnString
      },
    },
    ComputedPortOrientation: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        let ReturnString = '-'

        if(TemplateID) {

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          const Template = vm.TemplateData[Context][TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          const PartitionType = Partition.type

          if(PartitionType == 'connectable') {
            const PortOrientationID = Partition.port_orientation
            const PortOrientationIndex = vm.GetPortOrientationIndex(PortOrientationID)
            const PortOrientationName = vm.PortOrientationData[PortOrientationIndex].name
            ReturnString = PortOrientationName
          }
        }

        return ReturnString
      },
    },
    ComputedPortType: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        let ReturnString = '-'

        if(TemplateID) {

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          const Template = vm.TemplateData[Context][TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          const PartitionType = Partition.type

          if(PartitionType == 'connectable') {
            ReturnString = Partition.port_connector
          }
        }

        return ReturnString
      },
    },
    ComputedPortMediaType: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        const TemplateFace = vm.TemplateFaceSelected[Context]
        let ReturnString = '-'

        if(TemplateID) {

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          const Template = vm.TemplateData[Context][TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          const PartitionType = Partition.type

          if(PartitionType == 'connectable') {
            ReturnString = Partition.media
          }
        }

        return ReturnString
      },
    },
  },
  methods: {
    GetPortOrientationIndex: function(PortOrientationID) {

      const vm = this
      const PortOrientationIndex = vm.PortOrientationData.findIndex((PortOrientation) => PortOrientation.value == PortOrientationID);
      
      return PortOrientationIndex
    },
    GetObjectIndex: function(ObjectID) {

      const vm = this
      const Context = vm.Context
      const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);
      
      return ObjectIndex
    },
    GetTemplateIndex: function(TemplateID) {

      const vm = this
      const Context = vm.Context
      const TemplateIndex = vm.TemplateData[Context].findIndex((template) => template.id == TemplateID);

      return TemplateIndex
    },
    GetCategoryIndex: function(CategoryID) {

      const vm = this;
      const CategoryIndex = vm.CategoryData.findIndex((category) => category.id == CategoryID);

      return CategoryIndex
    },
    GetPartition: function(Blueprint, PartitionAddress) {

      // Store variables
      let WorkingPartitionChildren = Blueprint
      let SelectedPartition = WorkingPartitionChildren

      // Traverse blueprint until selected partition is reached
      PartitionAddress.forEach(function(AddressIndex, Index) {
        SelectedPartition = WorkingPartitionChildren[AddressIndex]
        WorkingPartitionChildren = SelectedPartition.children
      })

      // Return selected partition
      return SelectedPartition
    },
  }
}
</script>
<!-- Template/Object Details -->

<template>
  <div>

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

  <!-- Trunked To -->
  <dl class="row mb-0">
    <dt class="col-sm-4">
      Trunked To:
    </dt>
    <dd class="col-sm-8 mb-0">
      {{ComputedTrunkedTo}}
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

  <!-- Partition Type -->
  <dl class="row mb-0">
    <dt class="col-sm-4">
      Partition Type:
    </dt>
    <dd class="col-sm-8 mb-0">
      {{ComputedPartitionType}}
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

  <!-- Image -->
  <dl class="row mb-0">
    <dt class="col-sm-4">
      Image:
    </dt>
    <dd class="col-sm-8 mb-0">
      {{ComputedCategoryName}}
    </dd>
  </dl>

  </div>
</template>

<script>
import { } from 'bootstrap-vue'

export default {
  components: {
  },
  props: {
    CategoryData: {type: Array},
    TemplateData: {type: Array},
    Context: {type: String},
    TemplateFaceSelected: {type: Object},
    PartitionAddressSelected: {type: Object},
    TemplatePartitionPortRange: {type: String},
  },
  data() {
    return {
    }
  },
  computed: {
    ComputedObjectName: {
      get() {

        return '-'
      },
    },
    ComputedTemplateName: {
      get() {

        const vm = this
        const Context = vm.Context
        const TemplateID = vm.PartitionAddressSelected[Context].template_id
        let ReturnString = '-'

        if(TemplateID) {
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          ReturnString = vm.TemplateData[TemplateIndex].name
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
          const CategoryID = vm.TemplateData[TemplateIndex].category_id
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
        const ObjectID = vm.PartitionAddressSelected.object.object_id
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
          ReturnString = vm.TemplateData[TemplateIndex].type
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
          ReturnString = vm.TemplateData[TemplateIndex].function
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
          ReturnString = vm.TemplateData[TemplateIndex].ru_size
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
          ReturnString = vm.TemplateData[TemplateIndex].mount_config
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

          ReturnString = 'generic'

          // Get template
          const TemplateIndex = vm.GetTemplateIndex(TemplateID)
          const Template = vm.TemplateData[TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          ReturnString = (Partition.type) ? Partition.type : 'generic'
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
          const Template = vm.TemplateData[TemplateIndex]

          // Get partition
          const Blueprint = Template.blueprint[TemplateFace]
          const PartitionAddress = vm.PartitionAddressSelected[Context][TemplateFace]
          const Partition = vm.GetPartition(Blueprint, PartitionAddress)

          // Get partition type
          const PartitionType = Partition.type

          if(PartitionType == 'connectable') {
            ReturnString = Partition.port_orientation
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
          const Template = vm.TemplateData[TemplateIndex]

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
          const Template = vm.TemplateData[TemplateIndex]

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
    GetTemplateIndex: function(TemplateID) {

      const vm = this;
      const TemplateIndex = vm.TemplateData.findIndex((template) => template.id == TemplateID);

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
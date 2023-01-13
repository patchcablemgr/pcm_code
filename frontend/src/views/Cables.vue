<template>
  <div
    v-if="DependenciesReady"
  >
    <b-container class="bv-example-row" fluid="xs">
      <b-row
        cols="1"
      >
        <b-col>

          <component-cables
            CardTitle="Cables"
					/>

        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import {
  BContainer,
  BRow,
  BCol,
  VBTooltip,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import ComponentCables from '@/views/templates/ComponentCables.vue'
import { PCM } from '@/mixins/PCM.js'

const CableFields = [
  {key: 'a_identifier', label: 'ID'},
  {key: 'a_connector', label: 'Connector'},
  {key: 'a_port', label: 'Port'},
  {key: 'b_identifier', label: 'ID'},
  {key: 'b_connector', label: 'Connector'},
  {key: 'b_port', label: 'Port'},
  {key: 'media_type', label: 'Media Type'},
  {key: 'length', label: 'Length'},
  {key: 'editable', label: 'Actions'},
]

export default {
  mixins: [PCM],
  components: {
    BContainer,
    BRow,
    BCol,

    ComponentCables,
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
	},
  data() {
    return {
      Context: 'actual',
      CableFields,
    }
  },
  computed: {
    DependenciesReady: function() {

      const vm = this
      const Dependencies = [
        vm.CablesReady,
        vm.CategoriesReady.actual,
        vm.ObjectsReady.actual,
        vm.ConnectionsReady,
        vm.TemplatesReady.actual,
        vm.LocationsReady.actual,
      ]
      
      const DependenciesReady = Dependencies.every(function(element){ return element == true })
      return DependenciesReady
    },
    Cables() {
      return this.$store.state.pcmCables.Cables
    },
    CablesReady: function() {
      return this.$store.state.pcmCables.CablesReady
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    ObjectsReady: function() {
      return this.$store.state.pcmObjects.ObjectsReady
    },
    Connections() {
      return this.$store.state.pcmConnections.Connections
    },
    ConnectionsReady: function() {
      return this.$store.state.pcmConnections.ConnectionsReady
    },
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    TemplatesReady: function() {
      return this.$store.state.pcmTemplates.TemplatesReady
    },
    Categories() {
      return this.$store.state.pcmCategories.Categories
    },
    CategoriesReady: function() {
      return this.$store.state.pcmCategories.CategoriesReady
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    LocationsReady: function() {
      return this.$store.state.pcmLocations.LocationsReady
    },
    PortUtilizationList() {

      const vm = this
      const Context = vm.Context
      const PortUtilizationList = []
      const FaceArray = ['front', 'rear']

      vm.Objects[Context].forEach(function(Object, index){

        const ConnectablePartitions = []
        let PortTotal = 0
        let PortsPopulated = 0

        // Collect object data
        const ObjectID = Object.id
        const TemplateID = Object.template_id
        const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
        const Template = vm.Templates[Context][TemplateIndex]

        if(Template && Template.type == 'standard') {

          PortsPopulated = PortsPopulated + vm.Connections.filter((connection) => connection.a_id == ObjectID || connection.b_id == ObjectID).length

          // Get object connectable partitions
          const Blueprint = Template.blueprint
          FaceArray.forEach(function(Face, index){
            vm.GetConnectablePartitions(Blueprint[Face], ConnectablePartitions)
          })

          // Get object inserts
          const ObjectInserts = vm.GetObjectInserts(ObjectID)
          ObjectInserts.forEach(function(Insert, index){
          
            // Collect insert data
            const InsertID = Insert.id
            const InsertTemplateID = Insert.template_id
            const InsertTemplateIndex = vm.GetTemplateIndex(InsertTemplateID, Context)
            const InsertTemplate = vm.Templates[Context][InsertTemplateIndex]

            PortsPopulated = PortsPopulated + vm.Connections.filter((connection) => connection.a_id == InsertID || connection.b_id == InsertID).length

            // Get insert connectable partitions
            const InsertBlueprint = InsertTemplate.blueprint
            FaceArray.forEach(function(Face, index){
                vm.GetConnectablePartitions(InsertBlueprint[Face], ConnectablePartitions)
            })
          })
        
          // Calculate object port total
          ConnectablePartitions.forEach(function(ConnectablePartition){
            const ConnectablePartitionPortTotal = ConnectablePartition.port_layout.cols * ConnectablePartition.port_layout.rows
            PortTotal = PortTotal + ConnectablePartitionPortTotal
          })

          const CategoryID = Template.category_id
          const CategoryIndex = vm.Categories[Context].findIndex((category) => category.id == CategoryID )
          const Category = vm.Categories[Context][CategoryIndex]
          const CategoryColor = Category.color
          const ObjectName = vm.GetObjectDN(Object.id)
          const PortUtilization = (PortTotal > 0) ? Math.round((PortsPopulated / PortTotal) * 100) : 0
          let PortUtilizationVariant = ""
          if(PortUtilization > 89) {
            PortUtilizationVariant = "danger"
          } else if(PortUtilization > 79) {
            PortUtilizationVariant = "warning"
          } else {
            PortUtilizationVariant = "success"
          }

          const ObjectData = {
            'category_color': CategoryColor,
            'name': ObjectName,
            'total': PortTotal,
            'populated': PortsPopulated,
            'utilization': PortUtilization,
            'utilization_variant': PortUtilizationVariant,
          }
          
          PortUtilizationList.push(ObjectData)
        }
      });
      return PortUtilizationList
    },
  },
  methods: {
    GetObjectDN(ObjectID) {

      const vm = this
      const Context = vm.Context
      const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
      const Object = vm.Objects[Context][ObjectIndex]
      let LocationID = Object.location_id
      let DNArray = [Object.name]

      while(LocationID !== 0) {

          // Retrieve location data
          const LocationIndex = vm.GetLocationIndex(LocationID, Context)
          const Location = vm.Locations[Context][LocationIndex]
          const LocationName = Location.name

          // Prepend DNArray with parent object
          DNArray.unshift(LocationName)

          // Updata location ID
          LocationID = Location.parent_id
      }

      return DNArray.join('.')
    },
    GetObjectInserts(ObjectID, ObjectInserts=[]) {

      const vm = this
      const Context = vm.Context
      const FilteredInserts = vm.Objects[Context].filter(object => object.parent_id == ObjectID)

      FilteredInserts.forEach(function(FilteredInsert) {

        const Insert = JSON.parse(JSON.stringify(FilteredInsert))
        ObjectInserts.push(Insert)
        vm.GetObjectInserts(FilteredInsert.id, ObjectInserts)

      })

      return ObjectInserts
    },
    GetConnectablePartitions(Blueprint, ConnectablePartitions=[]) {
    
      const vm = this

      Blueprint.forEach(function(Partition, index) {

        if(Partition.type == 'connectable') {

          const ConnectablePartition = JSON.parse(JSON.stringify(Partition))
          ConnectablePartitions.push(ConnectablePartition)

        } else if(Partition.type == 'generic') {
          vm.GetConnectablePartitions(Partition.children, ConnectablePartitions)
        }
      })

      return ConnectablePartitions
    },
  },
  mounted() {},
}
</script>

<style>

</style>

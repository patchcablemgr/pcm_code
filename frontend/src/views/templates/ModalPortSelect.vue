<template>
    <!-- Template port select modal -->
    <b-modal
      id="modal-port-select"
      title="Edit"
      size="lg"
      ok-only
      ok-title="OK"
    >
      <b-row>
        <b-col>
          <b-card
            :title="ModalTitle"
          >
            <b-card-body>
              <liquor-tree
                :ref="TreeRef"
                :data="[]"
                :options="LocationTreeOptions"
              >
                <span class="tree-text" slot-scope="{ node }" style="width:100%;">
                  <template>
                    <div>
                      <feather-icon :icon="node.data.icon" />
                      {{ node.text }}
                      
                    </div>
                  </template>
                </span>
              </liquor-tree>
            </b-card-body>
          </b-card>
        </b-col>
      </b-row>

    </b-modal>
</template>

<script>
import {
  BRow,
  BCol,
  BCard,
  BCardBody,
} from 'bootstrap-vue'
import { PCM } from '@/mixins/PCM.js'
import LiquorTree from 'liquor-tree'

const LocationTreeOptions = {
  "multiples": true,
}

export default {
  mixins: [PCM],
  components: {
    BRow,
    BCol,
    BCard,
    BCardBody,
    LiquorTree,
  },
  directives: {},
  props: {
    ModalTitle: {type: String},
    TreeRef: {type: String},
    Context: {type: String},
    PartitionAddressSelected: {type: Object},
  },
  data () {
    return {
      LocationTreeOptions
    }
  },
  computed: {
    Templates() {
      return this.$store.state.pcmTemplates.Templates
    },
    Objects() {
      return this.$store.state.pcmObjects.Objects
    },
    Locations() {
      return this.$store.state.pcmLocations.Locations
    },
    TemplateType: function() {

      const vm = this
      const Context = vm.Context
      const ObjectID = vm.PartitionAddressSelected[Context].object_id
      let TemplateType = null

      if(ObjectID) {
        const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
        const Object = vm.Objects[Context][ObjectIndex]
        TemplateType = Object.floorplan_object_type
      }

      return TemplateType
    },
    MultiSelect: function() {

      const vm = this
      if(vm.TemplateType == 'walljack') {
        return true
      } else {
        return false
      }
    },
  },
  methods: {
  },
  mounted() {

    const vm = this
    const Context = vm.Context
    const TreeRef = vm.TreeRef

    this.$root.$on('bv::modal::shown', (bvEvent, modalId) => {

      // Build tree
      // $nextTick is required to allow time for the $refs[TreeRef] to be rendered
      vm.$nextTick(function () {
        const Scope = 'port'
        vm.BuildLocationTree({Scope})

        // Set tree options
        vm.$refs[TreeRef].options.multiples = vm.MultiSelect

        // Prevent nodes from being selected
        vm.$refs[TreeRef].$on('node:selected', function(node){
          const AllowedNodeTypes = [
            'partition',
            'port'
          ]
          const NodeType = node.data.type
          if(!AllowedNodeTypes.includes(NodeType)) {
            node.unselect()
          }
        })
      })
    })
  },
}
</script>
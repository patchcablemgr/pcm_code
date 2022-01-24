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
                ref="PortSelectTree"
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
  "multiples": false,
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
  },
  methods: {
    GetLocationNode(NodeID, Tree=false) {

      const vm = this
      Tree = (Tree) ? Tree : vm.TreeData
      const Node = Tree.find(element => element.id == NodeID)

      if(Node === 'undefined') {
        Tree.forEach(function(element){
          if(element.hasOwnProperty('children')) {
            vm.GetLocationNode(NodeID, element.children)
          }
        })
      } else {
        return Node
      }
    },
  },
  mounted() {

    const vm = this
  },
}
</script>
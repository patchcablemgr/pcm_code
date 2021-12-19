import axios from '@axios'

const ConnectorsReady = false
const Connectors = []
const MediumReady = false
const Medium = []
const OrientationsReady = false
const Orientations = []
const WorkspaceStandardID = 1
const WorkspaceInsertID = 2
const GenericCabinet = {
  "id": null,
  "size": 25,
  "name": "Cabinet",
}
const GenericBlueprintGeneric = {
  "type": "generic",
  "units": 1,
  "children": [],
}
const GenericBlueprintConnectable = {
  "type": "connectable",
  "units": null,
  "port_layout": {
    "cols": null,
    "rows": null,
  },
  "port_format": []
}
const GenericBlueprintEnclosure = {
  "type": "enclosure",
  "units": null,
  "enc_layout": {
    "cols": null,
    "rows": null,
  },
}
const GenericTemplate = {
  "id": null,
  "name": null,
  "category_id": null,
  "type": null,
  "ru_size": null,
  "function": null,
  "mount_config": null,
  "insert_constraints": null,
  "blueprint": {
    "front": [
      {
        "type": "generic",
        "units": 24,
        "children": []
      }
    ],
    "rear": [
      {
        "type": "generic",
        "units": 24,
        "children": []
      }
    ]
  }
}
const GenericObject = {
  "id": null,
  "name": null,
  "template_id": null,
  "location_id": null,
  "cabinet_ru": null,
  "cabinet_front": null,
  "parent_id": null,
  "parent_face": null,
  "parent_partition_address": null,
  "parent_enclosure_address": null,
}

export default {
  namespaced: true,
  state: {
    ConnectorsReady,
    Connectors,
    MediumReady,
    Medium,
    OrientationsReady,
    Orientations,
    WorkspaceStandardID,
    WorkspaceInsertID,
    GenericCabinet,
    GenericBlueprintGeneric,
    GenericBlueprintConnectable,
    GenericBlueprintEnclosure,
    GenericTemplate,
    GenericObject,
  },
  mutations: {
    SET_Connectors(state, data) {

      state.Connectors = data
    },
    Connectors_Ready(state) {
      state.ConnectorsReady = true
    },
    SET_Medium(state, data) {

      state.Medium = data
    },
    Medium_Ready(state) {
      state.MediumReady = true
    },
    SET_Orientations(state, data) {

      state.Orientations = data
    },
    Orientations_Ready(state) {
      state.OrientationsReady = true
    },
  },
  actions: {
    GET_Connectors(context) {

      axios.get('/api/port-connectors')
      .then(response => {

        context.commit('SET_Connectors', response.data)
        context.commit('Connectors_Ready')
      })
      
    },
    GET_Medium(context) {

      axios.get('/api/medium')
      .then(response => {

        context.commit('SET_Medium', response.data)
        context.commit('Medium_Ready')
      })
      
    },
    GET_Orientations(context) {

      axios.get('/api/port-orientation')
      .then(response => {

        context.commit('SET_Orientations', response.data)
        context.commit('Orientations_Ready')
      })
      
    },
  }
}

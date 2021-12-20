import axios from '@axios'

const pcmPropsReady = {
  connectors: false,
  medium: false,
  orientations: false
}
const Connectors = []
const Medium = []
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
    pcmPropsReady,
    Connectors,
    Medium,
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
    SET_Medium(state, data) {
      state.Medium = data
    },
    SET_Orientations(state, data) {
      state.Orientations = data
    },
    SET_Ready(state, {Prop, ReadyState}) {
      state.pcmPropsReady[Prop] = ReadyState
    },
  },
  actions: {}
}

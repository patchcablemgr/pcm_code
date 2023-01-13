const Default = {
  'object_id': null,
  'object_face': null,
  'partition': {
    'front': null,
    'rear': null,
  },
  'port_id': {
    'front': null,
    'rear': null,
  },
  'location_id': null,
  'node_id': null,
  'location_face': 'front'
}

const Selected = {
  'actual': JSON.parse(JSON.stringify(Default)),
  'workspace': JSON.parse(JSON.stringify(Default), function(key, value) {
    if(key == 'object_id' || key == 'template_id') {
      return 1
    } else if(key == 'object_face' ) {
      return 'front'
    } else if(key == 'partition' ) {
      return {'front': [0], 'rear': [0]}
    } else if(key == 'location_id' ) {
      return 1
    } else {
      return value
    }
  }),
  'template': JSON.parse(JSON.stringify(Default)),
  'object': JSON.parse(JSON.stringify(Default)),
  'catalog': JSON.parse(JSON.stringify(Default))
}

const Hovered = {
  'actual': JSON.parse(JSON.stringify(Default)),
  'workspace': JSON.parse(JSON.stringify(Default)),
  'template': JSON.parse(JSON.stringify(Default)),
  'object': JSON.parse(JSON.stringify(Default)),
  'catalog': JSON.parse(JSON.stringify(Default))
}

export default {
  namespaced: true,
  state: {
    Selected,
    Hovered,
    DataSyncTimestamp: 0,
    DataSyncIntervalID: null,
  },
  mutations: {
    UPDATE_Selected(state, {pcmContext, data}) {

      Object.keys(data).forEach(key => {
        state.Selected[pcmContext][key] = data[key]
      })

    },
    DEFAULT_Selected_Object(state, {pcmContext}) {

      let workingObject = JSON.parse(JSON.stringify(state.Selected[pcmContext]))

      workingObject.object_id = null
      workingObject.object_face = null
      workingObject.partition.front = null
      workingObject.partition.rear = null
      workingObject.port_id.front = null
      workingObject.port_id.rear = null

      state.Selected[pcmContext] = workingObject
    },
    DEFAULT_Selected_Location(state, {pcmContext}) {

      let workingObject = JSON.parse(JSON.stringify(state.Selected[pcmContext]))
      
      workingObject.location_id = null
      workingObject.node_id = null
      workingObject.location_face = "front"

      state.Selected[pcmContext] = workingObject
    },
    DEFAULT_Selected_All(state, {pcmContext}) {
      state.Selected[pcmContext] = JSON.parse(JSON.stringify(Default))
    },
    UPDATE_Hovered(state, {pcmContext, data}) {

      state.Hovered[pcmContext] = data
    },
    UPDATE_DataSyncTimestamp(state, {timestamp}) {

      state.DataSyncTimestamp = timestamp
    },
    UPDATE_DataSyncIntervalID(state, {IntervalID}) {

      state.DataSyncIntervalID = IntervalID
    },
    DEFAULT_Hovered(state, {pcmContext}) {

      state.Hovered[pcmContext] = JSON.parse(JSON.stringify(Default))
    },
  },
  actions: {}
}

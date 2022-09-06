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
  'location_face': 'front',
}

const Selected = {
  'actual': Default,
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
  'template': Default,
  'object': Default,
  'catalog': Default
}

const Hovered = {
  'actual': Default,
  'workspace': Default,
  'template': Default,
  'object': Default,
  'catalog': Default
}

export default {
  namespaced: true,
  state: {
    Selected,
    Hovered,
  },
  mutations: {
    UPDATE_Selected(state, {pcmContext, data}) {

      Object.keys(data).forEach(key => {
        state.Selected[pcmContext][key] = data[key]
      })

    },
    DEFAULT_Selected(state, {pcmContext}) {

      state.Selected[pcmContext] = Default
    },
    UPDATE_Hovered(state, {pcmContext, data}) {

      state.Hovered[pcmContext] = data
    },
    DEFAULT_Hovered(state, {pcmContext}) {

      state.Hovered[pcmContext] = Default
    },
  },
  actions: {}
}

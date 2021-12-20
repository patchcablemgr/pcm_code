const LocationsReady = {
  'workspace': false,
  'actual': false,
  'template': false,
}
const Locations = {
  'workspace': [],
  'actual': [],
  'template': [],
}

export default {
  namespaced: true,
  state: {
    Locations,
    LocationsReady,
  },
  mutations: {
    SET_Locations(state, {pcmContext, data}) {
      state.Locations[pcmContext] = data
    },
    ADD_Location(state, {pcmContext, data}) {
      state.Locations[pcmContext].push(data)
    },
    UPDATE_Location(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.Locations[pcmContext].findIndex((item) => item.id == ID)
      state.Locations[pcmContext].splice(Index, 1, data)
    },
    REMOVE_Location(state, data) {

      const ID = data.id
      const Index = state.Locations.template.findIndex((item) => item.id == ID)
      state.Locations.template.splice(Index, 1)
    },
    SET_Ready(state, {pcmContext, ReadyState}) {
      state.LocationsReady[pcmContext] = ReadyState
    },
  },
  actions: {}
}

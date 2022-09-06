const CablePathsReady = {
  'workspace': false,
  'actual': false,
  'template': false,
}
const CablePaths = {
  'workspace': [],
  'actual': [],
  'template': [],
}

export default {
  namespaced: true,
  state: {
    CablePaths,
    CablePathsReady,
  },
  mutations: {
    SET_CablePaths(state, {pcmContext, data}) {
      state.CablePaths[pcmContext] = data
    },
    ADD_CablePath(state, {pcmContext, data}) {
      state.CablePaths[pcmContext].push(data)
    },
    UPDATE_CablePath(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.CablePaths[pcmContext].findIndex((item) => item.id == ID)
      state.CablePaths[pcmContext].splice(Index, 1, data)
    },
    REMOVE_CablePath(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.CablePaths[pcmContext].findIndex((item) => item.id == ID)
      state.CablePaths[pcmContext].splice(Index, 1)
    },
    SET_Ready(state, {pcmContext, ReadyState}) {
      state.CablePathsReady[pcmContext] = ReadyState
    },
  },
  actions: {}
}

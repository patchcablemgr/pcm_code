const FloorplanTemplatesReady = false
const FloorplanTemplates = []

export default {
  namespaced: true,
  state: {
    FloorplanTemplatesReady,
    FloorplanTemplates,
  },
  mutations: {
    SET_FloorplanTemplates(state, {data}) {
      state.FloorplanTemplates = data
    },
    SET_Ready(state, {ReadyState}) {
      state.FloorplanTemplatesReady = ReadyState
    },
  },
  actions: {}
}

const CablesReady = false
const Cables = []

export default {
  namespaced: true,
  state: {
    Cables,
    CablesReady,
  },
  mutations: {
    SET_Cables(state, {data}) {
      state.Cables = data
    },
    ADD_Cable(state, {data}) {
      state.Cables.push(data)
    },
    UPDATE_Cable(state, {data}) {

      const ID = data.id
      const Index = state.Cables.findIndex((item) => item.id == ID)
      state.Cables.splice(Index, 1, data)
    },
    REMOVE_Cable(state, {data}) {
      const ID = data.id
      const Index = state.Cables.findIndex((item) => item.id == ID)
      state.Cables.splice(Index, 1)
    },
    SET_Ready(state, {ReadyState}) {
      state.CablesReady = ReadyState
    },
  },
  actions: {}
}

const TrunksReady = false
const Trunks = []

export default {
  namespaced: true,
  state: {
    Trunks,
    TrunksReady,
  },
  mutations: {
    SET_Trunks(state, {data}) {
      state.Trunks = data
    },
    ADD_Trunk(state, {data}) {
      state.Trunks.push(data)
    },
    UPDATE_Trunk(state, {data}) {

      const ID = data.id
      const Index = state.Trunks.findIndex((item) => item.id == ID)
      state.Trunks.splice(Index, 1, data)
    },
    REMOVE_Trunk(state, {data}) {
      const ID = data.id
      const Index = state.Trunks.findIndex((item) => item.id == ID)
      state.Trunks.splice(Index, 1)
    },
    SET_Ready(state, {ReadyState}) {
      state.TrunksReady = ReadyState
    },
  },
  actions: {}
}

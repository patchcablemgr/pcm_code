const ConnectionsReady = false
const Connections = []

export default {
  namespaced: true,
  state: {
    Connections,
    ConnectionsReady,
  },
  mutations: {
    SET_Connections(state, {data}) {
      state.Connections = data
    },
    ADD_Connection(state, {data}) {
      console.log(data)
      state.Connections.push(data)
    },
    UPDATE_Connection(state, {data}) {

      const ID = data.id
      const Index = state.Connections.findIndex((item) => item.id == ID)
      state.Connections.splice(Index, 1, data)
    },
    REMOVE_Connection(state, {data}) {
      const ID = data.id
      const Index = state.Connections.findIndex((item) => item.id == ID)
      state.Connections.splice(Index, 1)
    },
    SET_Ready(state, {ReadyState}) {
      state.ConnectionsReady = ReadyState
    },
  },
  actions: {}
}

const PortsReady = false
const Ports = []

export default {
  namespaced: true,
  state: {
    Ports,
    PortsReady,
  },
  mutations: {
    SET_Ports(state, {data}) {
      state.Ports = data
    },
    ADD_Port(state, {data}) {
      state.Ports.push(data)
    },
    UPDATE_Port(state, {data}) {

      const ID = data.id
      const Index = state.Ports.findIndex((item) => item.id == ID)
      state.Ports.splice(Index, 1, data)
    },
    REMOVE_Port(state, {data}) {
      const ID = data.id
      const Index = state.Ports.findIndex((item) => item.id == ID)
      state.Ports.splice(Index, 1)
    },
    SET_Ready(state, {ReadyState}) {
      state.PortsReady = ReadyState
    },
  },
  actions: {}
}

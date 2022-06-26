const OrganizationReady = false
const Organization = []

export default {
  namespaced: true,
  state: {
    Organization,
    OrganizationReady,
  },
  mutations: {
    SET_Organization(state, {data}) {
      state.Organization = data
    },
    ADD_User(state, {data}) {
      state.Organization.push(data)
    },
    UPDATE_User(state, {data}) {

      const ID = data.id
      const Index = state.Organization.findIndex((item) => item.id == ID)
      state.Organization.splice(Index, 1, data)
    },
    REMOVE_User(state, {data}) {
      const ID = data.id
      const Index = state.Organization.findIndex((item) => item.id == ID)
      state.Organization.splice(Index, 1)
    },
    SET_Ready(state, {ReadyState}) {
      state.OrganizationReady = ReadyState
    },
  },
  actions: {}
}

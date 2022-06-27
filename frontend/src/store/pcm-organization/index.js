const OrganizationReady = false
const Organization = {}

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
    UPDATE_Organization(state, {data}) {
      state.Organization = data
    },
    SET_Ready(state, {ReadyState}) {
      state.OrganizationReady = ReadyState
    },
  },
  actions: {}
}

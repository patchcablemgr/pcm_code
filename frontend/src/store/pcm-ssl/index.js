const CSRReady = false
const CertReady = false
const CSRList = []
const CertList = []

export default {
  namespaced: true,
  state: {
    CSRList,
    CertList,
    CSRReady,
    CertReady,
  },
  mutations: {
    SET_CSR(state, {data}) {
      state.CSRList = data
    },
    ADD_CSR(state, {data}) {
      state.CSRList.push(data)
    },
    UPDATE_CSR(state, {data}) {

      const ID = data.id
      const Index = state.CSRList.findIndex((item) => item.id == ID)
      state.CSRList.splice(Index, 1, data)
    },
    REMOVE_CSR(state, {data}) {
      const ID = data.id
      const Index = state.CSRList.findIndex((item) => item.id == ID)
      state.CSRList.splice(Index, 1)
    },
    SET_CSR_Ready(state, {ReadyState}) {
      state.CSRReady = ReadyState
    },
    SET_Cert(state, {data}) {
      state.CertList = data
    },
    ADD_Cert(state, {data}) {
      state.CertList.push(data)
    },
    UPDATE_Cert(state, {data}) {

      const ID = data.id
      const Index = state.CertList.findIndex((item) => item.id == ID)
      state.CertList.splice(Index, 1, data)
    },
    REMOVE_Cert(state, {data}) {
      const ID = data.id
      const Index = state.CertList.findIndex((item) => item.id == ID)
      state.CertList.splice(Index, 1)
    },
    SET_Cert_Ready(state, {ReadyState}) {
      state.CertReady = ReadyState
    },
  },
  actions: {}
}

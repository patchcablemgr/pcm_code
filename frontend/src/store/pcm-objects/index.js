const ObjectsReady = {
  'workspace': false,
  'actual': false,
  'template': false,
}
const Objects = {
  'workspace': [],
  'actual': [],
  'template': [],
}

export default {
  namespaced: true,
  state: {
    ObjectsReady,
    Objects,
  },
  mutations: {
    SET_Objects(state, data) {
      state.Objects.actual = data
    },
    ADD_Object(state, {pcmContext, data}) {
      state.Objects[pcmContext].push(data)
    },
    UPDATE_Object(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.Objects[pcmContext].findIndex((item) => item.id == ID)
      state.Objects[pcmContext].splice(Index, 1, data)
    },
    REMOVE_Object(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.Objects[pcmContext].findIndex((item) => item.id == ID)
      state.Objects[pcmContext].splice(Index, 1)
    },
    SET_Ready(state, {pcmContext, ReadyState}) {
      state.ObjectsReady[pcmContext] = ReadyState
    },
  },
  actions: {}
}

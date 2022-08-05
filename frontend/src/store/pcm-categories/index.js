const CategoriesReady = {
  'workspace': false,
  'actual': false,
  'template': false,
  'catalog': false,
}
const Categories = {
  'workspace': [],
  'actual': [],
  'template': [],
  'catalog': [],
}

export default {
  namespaced: true,
  state: {
    CategoriesReady,
    Categories
  },
  mutations: {
    SET_Categories(state, {pcmContext, data}) {
      state.Categories[pcmContext] = data
    },
    ADD_Category(state, {pcmContext, data}) {
      state.Categories[pcmContext].push(data)
    },
    UPDATE_Category(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.Categories[pcmContext].findIndex((item) => item.id == ID)
      state.Categories[pcmContext].splice(Index, 1, data)
    },
    DELETE_Category(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.Categories[pcmContext].findIndex((item) => item.id == ID)
      state.Categories[pcmContext].splice(Index, 1)
    },
    SET_Ready(state, {pcmContext, ReadyState}) {
      state.CategoriesReady[pcmContext] = ReadyState
    },
  },
  actions: {}
}

export default {
  namespaced: true,
  state: {
    CategoriesReady:false,
    Categories: null
  },
  mutations: {
    SET_Categories(state, data) {
      state.Categories = data
    },
    ADD_Category(state, data) {
      state.Categories.push(data)
    },
    UPDATE_Category(state, data) {

      const ID = data.id
      const Index = state.Categories.findIndex((item) => item.id == ID)
      state.Categories.splice(Index, 1, data)
    },
    DELETE_Category(state, data) {

      const ID = data.id
      const Index = state.Categories.findIndex((item) => item.id == ID)
      state.Categories.splice(Index, 1)
    },
    SET_Ready(state, ReadyState) {
      state.CategoriesReady = ReadyState
    },
  },
  actions: {}
}

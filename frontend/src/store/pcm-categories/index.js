import axios from '@axios'

export default {
  namespaced: true,
  state: {
    CategoriesReady:false,
    Categories: null
  },
  mutations: {
    GET_Categories(state, data) {

      state.Categories = data
      state.CategoriesReady = true
    },
    POST_Categories(state, data) {

      state.Categories.push(data)
    },
    PATCH_Categories(state, data) {

      const ID = data.id
      const Index = state.Categories.findIndex((item) => item.id == ID)
      state.Categories.splice(Index, 1, data)
    },
    DELETE_Categories(state, data) {

      const ID = data.id
      const Index = state.Categories.findIndex((item) => item.id == ID)
      state.Categories.splice(Index, 1)
    },
  },
  actions: {
    GET_Categories(context) {

      axios.get('/api/categories')
      .then(response => {
        context.commit('GET_Categories', response.data)
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })
      
    },
    POST_Categories(context, {vm, data}) {

      axios.post('/api/categories', data)
      .then(response => {
        context.commit('POST_Categories', response.data)
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    PATCH_Categories(context, {vm, data}) {

      const ID = data.id

      axios.patch('/api/categories/'+ID, data)
      .then(response => {
        context.commit('PATCH_Categories', response.data)
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    DELETE_Categories(context, {vm, data}) {
      
      const ID = data.id

      axios.delete('/api/categories/'+ID)
      .then(response => {
        context.commit('DELETE_Categories', response.data)
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    ERROR({vm, data}) {
      
      // Display error to user via toast
      vm.$bvToast.toast(JSON.stringify(data), {
        title: 'Error',
        variant: 'danger',
      })
    }
  }
}

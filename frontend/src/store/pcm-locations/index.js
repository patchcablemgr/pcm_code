import axios from '@axios'

const LocationsReady = {
  'workspace': false,
  'actual': false,
  'template': false,
}
const Locations = {
  'workspace': [],
  'actual': [],
  'template': [],
}

export default {
  namespaced: true,
  state: {
    Locations,
    LocationsReady,
  },
  mutations: {
    SET_Locations(state, data) {

      state.Locations.template = data
      state.LocationsReady.template = true
    },
    ADD_Location(state, {pcmContext, data}) {

      state.Locations[pcmContext].push(data)
    },
    UPDATE_Location(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.Locations[pcmContext].findIndex((item) => item.id == ID)
      state.Locations[pcmContext].splice(Index, 1, data)
    },
    REMOVE_Location(state, data) {

      const ID = data.id
      const Index = state.Locations.template.findIndex((item) => item.id == ID)
      state.Locations.template.splice(Index, 1)
    },
  },
  actions: {
    GET_Locations(context) {

      const WorkspaceIDs = {
        standard: context.rootState.pcmProps.WorkspaceStandardID,
        insert: context.rootState.pcmProps.WorkspaceInsertID
      }
      let WorkspaceCabinet
      Object.entries(WorkspaceIDs).forEach(function([Type, ID]){
        WorkspaceCabinet = JSON.parse(JSON.stringify(context.rootState.pcmProps.GenericCabinet), function (Key, Value) {
          if(Key == 'id') {
            return ID
          } else {
            return Value
          }
        })
        context.commit('ADD_Location', {pcmContext:'workspace', data:WorkspaceCabinet})
      })

      axios.get('/api/locations')
      .then(response => {

        // Store template data
        context.commit('SET_Locations', response.data)
      })
      
    },
    POST_Location(context, {vm, data}) {

      axios.post('/api/locations', data)
      .then(response => {
        context.commit('ADD_Location', {pcmContext:'template', data:response.data})
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    PATCH_Location(context, {vm, data}) {

      const ID = data.id

      axios.patch('/api/locations/'+ID, data)
      .then(response => {
        context.commit('UPDATE_Location', {pcmContext:'template', data:response.data})
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    DELETE_Location(context, {vm, data}) {
      
      const ID = data.id

      axios.delete('/api/locations/'+ID)
      .then(response => {
        context.commit('REMOVE_Location', response.data)
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

import axios from '@axios'

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
  actions: {
    GET_Objects(context) {

      // Create workspace objects
      const WorkspaceIDs = {
        standard: context.rootState.pcmProps.WorkspaceStandardID,
        insert: context.rootState.pcmProps.WorkspaceInsertID
      }
      let WorkspaceObject
      Object.entries(WorkspaceIDs).forEach(function([Type, ID]){
        WorkspaceObject = JSON.parse(JSON.stringify(context.rootState.pcmProps.GenericObject), function (Key, Value) {
          if(Key == 'id') {
            return ID
          } else if(Key == 'name') {
            return "New_Object"
          } else if(Key == 'template_id') {
            return ID
          } else if(Key == 'location_id') {
            return ID
          } else if(Key == 'cabinet_ru') {
            return (Type == 'standard') ? 1 : Value
          } else if(Key == 'cabinet_front') {
            return (Type == 'standard') ? 'front' : Value
          } else {
            return Value
          }
        })
        context.commit('ADD_Object', {pcmContext:'workspace', data:WorkspaceObject})
      })
      context.commit('SET_Ready', {pcmContext:'workspace', ReadyState:true})

      axios.get('/api/objects')
      .then(response => {
        context.commit('SET_Objects', response.data)
        context.commit('SET_Ready', {pcmContext:'actual', ReadyState:true})
      })
      
    },
    POST_Object(context, {vm, data}) {

      axios.post('/api/objects', data)
      .then(response => {
        context.commit('ADD_Object', {pcmContext:'actual', data:response.data})
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    PATCH_Object(context, {vm, data}) {

      const ID = data.id

      axios.patch('/api/objects/'+ID, data)
      .then(response => {
        context.commit('PATCH_Objects', response.data)
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    DELETE_Object(context, {vm, data}) {
      
      const ID = data.id

      axios.delete('/api/objects/'+ID)
      .then(response => {
        context.commit('DELETE_Objects', response.data)
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

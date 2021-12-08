import axios from '@axios'

const TemplatesReady = {
  'workspace': false,
  'actual': false,
  'template': false,
}
const Templates = {
  'workspace': [],
  'actual': [],
  'template': []
}
const DefaultPortFormat = [
  {'type': 'static', 'value': 'Port', 'count': 0, 'order': 0},
  {'type': 'incremental', 'value': '1', 'count': 0, 'order': 2},
  {'type': 'series', 'value': 'A,B,C', 'count': 0, 'order': 1},
]
const DefaultPortLayout = {
  'cols': 1,
  'rows': 1
}
const DefaultEncLayout = {
  'cols': 1,
  'rows': 1
}

export default {
  namespaced: true,
  state: {
    TemplatesReady,
    Templates,
    DefaultPortFormat,
    DefaultPortLayout,
    DefaultEncLayout,
  },
  mutations: {
    SET_Templates(state, data) {

      state.Templates.actual = data
      state.Templates.template = data
    },
    ADD_Template(state, {pcmContext, data}) {

      state.Templates[pcmContext].push(data)
    },
    UPDATE_Template(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.Templates[pcmContext].findIndex((item) => item.id == ID)
      state.Templates[pcmContext].splice(Index, 1, data)
    },
    REMOVE_Template(state, {pcmContext, data}) {

      const ID = data.id
      const Index = state.Templates[pcmContext].findIndex((item) => item.id == ID)
      state.Templates[pcmContext].splice(Index, 1)
    },
    SET_Ready(state, {pcmContext, ReadyState}) {

      state.TemplatesReady[pcmContext] = ReadyState
    },
  },
  actions: {
    GET_Templates(context) {

      // Create workspace templates
      const WorkspaceIDs = {
        standard: context.rootState.pcmProps.WorkspaceStandardID,
        insert: context.rootState.pcmProps.WorkspaceInsertID
      }
      let WorkspaceTemplate
      Object.entries(WorkspaceIDs).forEach(function([Type, ID]){
        WorkspaceTemplate = JSON.parse(JSON.stringify(context.rootState.pcmProps.GenericTemplate), function (Key, Value) {
          if(Key == 'id') {
            return ID
          } else if(Key == 'name') {
            return "New_Template"
          } else if(Key == 'type') {
            if(Value === null) {
              return Type
            } else {
              return Value
            }
          } else if(Key == 'ru_size') {
            return (Type == 'standard') ? 1 : Value
          } else if(Key == 'function') {
            return 'endpoint'
          } else if(Key == 'mount_config') {
            return (Type == 'standard') ? '2-post' : Value
          } else if(Key == 'insert_constraints') {
            return (Type == 'standard') ? Value : {part_layout:{height:2,width:24},enc_layout:{cols:1,rows:1}}
          } else {
            return Value
          }
        })
        context.commit('ADD_Template', {pcmContext:'workspace', data:WorkspaceTemplate})
      })
      context.commit('SET_Ready', {pcmContext:'workspace', ReadyState:true})

      axios.get('/api/templates')
      .then(response => {

        // Store template data
        context.commit('SET_Templates', response.data)
        context.commit('SET_Ready', {pcmContext:'actual', ReadyState:true})
        context.commit('SET_Ready', {pcmContext:'template', ReadyState:true})
      })
      
    },
    POST_Template(context, {vm, data}) {

      axios.post('/api/templates', data)
      .then(response => {
        context.commit('ADD_Template', {pcmContext:'template', data:response.data})
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    PATCH_Template(context, {vm, data}) {

      const ID = data.id

      axios.patch('/api/templates/'+ID, data)
      .then(response => {
        context.commit('UPDATE_Template', {pcmContext:'template', data:response.data})
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    DELETE_Template(context, {vm, data}) {
      
      const ID = data.id

      axios.delete('/api/templates/'+ID)
      .then(response => {
        context.commit('REMOVE_Template', response.data)
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

import axios from '@axios'

const ObjectsReady = false
const StandardTemplateID = 1
const InsertTemplateID = 2
const Objects = {
  'workspace': [
    {
      "id": StandardTemplateID,
      "name": "Standard",
      "template_id": StandardTemplateID,
      "location_id": 1,
      "cabinet_ru": 1,
      "cabinet_front": "front",
    },
    {
      "id": InsertTemplateID,
      "name": "Insert",
      "template_id": InsertTemplateID,
      "location_id": null,
      "cabinet_ru": null,
      "cabinet_front": null,
      "parent_id": null,
      "parent_face": "front",
      "parent_part_addr": null,
      "parent_enclosure_address": [0,0],
    }
  ],
  'actual': [],
  'template': [],
}

export default {
  namespaced: true,
  state: {
    ObjectsReady,
    Objects
  },
  mutations: {
    GET_Objects(state, data) {

      state.Objects.actual = data
      state.ObjectsReady = true
    },
    ADD_Object(state, {pcmContext, data}) {
      
      state.Objects[pcmContext].push(data)
    },
    PATCH_Objects(state, data) {

      const ID = data.id
      const Index = state.Objects.actual.findIndex((item) => item.id == ID)
      state.Objects.actual.splice(Index, 1, data)
    },
    DELETE_Objects(state, data) {

      const ID = data.id
      const Index = state.Objects.actual.findIndex((item) => item.id == ID)
      state.Objects.actual.splice(Index, 1)
    },
  },
  actions: {
    GET_Objects(context) {

      axios.get('/api/objects')
      .then(response => {
        context.commit('GET_Objects', response.data)
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })
      
    },
    POST_Objects(context, {vm, data}) {

      axios.post('/api/objects', data)
      .then(response => {
        context.commit('ADD_Object', {pcmContext:'actual', data:response.data})
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    PATCH_Objects(context, {vm, data}) {

      const ID = data.id

      axios.patch('/api/objects/'+ID, data)
      .then(response => {
        context.commit('PATCH_Objects', response.data)
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    DELETE_Objects(context, {vm, data}) {
      
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

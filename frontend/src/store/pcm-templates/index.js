import axios from '@axios'

const TemplatesReady = false
const StandardTemplateID = 1
const InsertTemplateID = 2
const Templates = {
  'workspace': [
    {
      "id": StandardTemplateID,
      "name": "New_Template",
      "category_id": 0,
      "type": "standard",
      "ru_size": 1,
      "function": "endpoint",
      "mount_config": "2-post",
      "insert_constraints": null,
      "blueprint": {
        "front": [{
          "type": "generic",
          "units":24,
          "children": [],
        }],
        "rear": [{
          "type": "generic",
          "units":24,
          "children": [],
        }],
      }
    },
    {
      "id": InsertTemplateID,
      "name": "New_Template",
      "category_id": 0,
      "type": "insert",
      "function": "endpoint",
      "insert_constraints": null,
			"parent_template": null,
      "blueprint": {
        "front": [{
          "type": "generic",
          "units":24,
          "children": [],
        }],
        "rear": [],
      }
    }
  ],
  'actual': [],
  'template': []
}
const GenericTemplate = {
  "id": null,
  "pseudo": true,
  "name": "PseudoTemplate",
  "category_id": null,
  "type": null,
  "ru_size": null,
  "function": null,
  "mount_config": "2-post",
  "insert_constraints": null,
  "blueprint": {
    "front": [{
      "type": "generic",
      "units": null,
      "children": [{
        "type": "enclosure",
        "units": null,
        "children": [],
        "enc_layout": {
          "cols": null,
          "rows": null,
        },
      },
      ],
    },
    ],
    "rear": []
  },
}
const GenericObject = {
  "id": null,
  "pseudo": true,
  "name": null,
  "template_id": null,
  "location_id": null,
  "cabinet_ru": null,
  "cabinet_front": null,
  "parent_id": null,
  "parent_face": null,
  "parent_part_addr": null,
  "parent_enclosure_address": null,
}

export default {
  namespaced: true,
  state: {
    TemplatesReady,
    StandardTemplateID,
    InsertTemplateID,
    Templates,
  },
  mutations: {
    GET_Templates(state, data) {

      state.Templates.template = data
    },
    POST_Templates(state, {pcmContext, data}) {

      state.Templates[pcmContext].push(data)
    },
    UPDATE_Template(state, {pcmContext, data}) {

      const ID = data.id
      console.log(pcmContext)
      const Index = state.Templates[pcmContext].findIndex((item) => item.id == ID)
      state.Templates[pcmContext].splice(Index, 1, data)
    },
    DELETE_Templates(state, data) {

      const ID = data.id
      const Index = state.Templates.template.findIndex((item) => item.id == ID)
      state.Templates.template.splice(Index, 1)
    },
    Templates_Ready(state) {
      state.TemplatesReady = true
    },
  },
  actions: {
    TEST(context, data) {
      console.log(data)
    },
    GET_Templates(context) {

      axios.get('/api/templates')
      .then(response => {

        // Store template data
        context.commit('GET_Templates', response.data)

        // Generate pseudo data for templates
        response.data.forEach(function(element) {
          context.dispatch('GeneratePseudoData', {pcmContext:'template', Template:element})
        })

        // Flag template data as ready
        context.commit('Templates_Ready')
      })
      
    },
    POST_Templates(context, {vm, data}) {

      axios.post('/api/templates', data)
      .then(response => {
        context.commit('POST_Templates', {pcmContext:'template', data:response.data})
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    PATCH_Templates(context, {vm, data}) {

      const ID = data.id

      axios.patch('/api/templates/'+ID, data)
      .then(response => {
        context.commit('UPDATE_Template', {pcmContext:'template', data:response.data})
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    DELETE_Templates(context, {vm, data}) {
      
      const ID = data.id

      axios.delete('/api/templates/'+ID)
      .then(response => {
        context.commit('DELETE_Templates', response.data)
      }).catch(error => {
        context.dispatch('ERROR', {vm: vm, data: error.response.data})
      })

    },
    GeneratePseudoData(context, {pcmContext, Template}) {

      let WorkingObjectData = []
      let WorkingTemplateData = []
      let PseudoObjectParentID = null
      let PseudoObjectParentFace = null
      let PseudoObjectParentPartitionAddress = null
      let PseudoObjectParentEnclosureAddress = null
      const TemplateID = Template.id
      const TemplateType = Template.type

      if (TemplateType == 'insert') {
        
        PseudoObjectParentFace = 'front'
        PseudoObjectParentPartitionAddress = [0, 0]
        PseudoObjectParentEnclosureAddress = [0, 0]
        const InsertConstraints = Template.insert_constraints

        // Generate pseudo IDs
        const PseudoTemplateID = "pseudo-" + Templates[pcmContext].length
        const PseudoObjectID = "pseudo-" + Templates[pcmContext].length

        // Create pseudo object
        WorkingObjectData.push(JSON.parse(JSON.stringify(GenericObject), function (GenericObjectKey, GenericObjectValue) {
          if (GenericObjectKey == 'id') {
            return PseudoObjectID
          } else if (GenericObjectKey == 'cabinet_front') {
            return 'front'
          } else if (GenericObjectKey == 'location_id') {
            return (pcmContext == 'working') ? InsertTemplateID : null
          } else if (GenericObjectKey == 'cabinet_ru') {
            return 1
          } else if (GenericObjectKey == 'template_id') {
            return PseudoTemplateID
          } else if (GenericObjectKey == 'parent_id') {
            return PseudoObjectParentID
          } else {
            return GenericObjectValue
          }
        }))

        // Create pseudo template
        WorkingTemplateData.push(JSON.parse(JSON.stringify(GenericTemplate), function (GenericTemplateKey, GenericTemplateValue) {
          if (GenericTemplateKey == 'id') {
            return PseudoTemplateID
          } else if (GenericTemplateKey == 'name') {
            return Template.name
          } else if (GenericTemplateKey == 'category_id') {
            return Template.category_id
          } else if (GenericTemplateKey == 'type') {
            // Set pseudo template type, but avoid setting partition type
            if (GenericTemplateValue === null) {
                return 'standard'
            } else {
                return GenericTemplateValue
            }
          } else if (GenericTemplateKey == 'ru_size') {
            // Set pseudo template RU size if this is the insert constraint origin ('standard' template type)
            return Math.ceil(InsertConstraints.part_layout.height / 2)
          } else if (GenericTemplateKey == 'blueprint') {
            // Set pseudo template partition attributes
            GenericTemplateValue.front[0].units = InsertConstraints.part_layout.width
            GenericTemplateValue.front[0].children[0].units = InsertConstraints.part_layout.height
            GenericTemplateValue.front[0].children[0].enc_layout.cols = InsertConstraints.enc_layout.cols
            GenericTemplateValue.front[0].children[0].enc_layout.rows = InsertConstraints.enc_layout.rows
            return GenericTemplateValue

          } else {

              return GenericTemplateValue
          }
        }))

        PseudoObjectParentID = PseudoObjectID
      }

      // Create pseudo object for template
      const PseudoObjectID = "pseudo-" + (Templates[pcmContext].length + WorkingObjectData.length)
      WorkingObjectData.push(JSON.parse(JSON.stringify(GenericObject), function (GenericObjectKey, GenericObjectValue) {
        if (GenericObjectKey == 'id') {
            return PseudoObjectID
        } else if (GenericObjectKey == 'location_id') {
            return (pcmContext == 'working' && TemplateType == 'standard') ? InsertTemplateID : GenericObjectValue
        } else if (GenericObjectKey == 'cabinet_front') {
            return (pcmContext == 'working' && TemplateType == 'standard') ? 'front' : GenericObjectValue
        } else if (GenericObjectKey == 'cabinet_ru') {
            return (pcmContext == 'working' && TemplateType == 'standard') ? 1 : GenericObjectValue
        } else if (GenericObjectKey == 'parent_id') {
            return PseudoObjectParentID
        } else if (GenericObjectKey == 'parent_face') {
            return PseudoObjectParentFace
        } else if (GenericObjectKey == 'parent_part_addr') {
            return PseudoObjectParentPartitionAddress
        } else if (GenericObjectKey == 'parent_enclosure_address') {
            return PseudoObjectParentEnclosureAddress
        } else if (GenericObjectKey == 'template_id') {
            return TemplateID
        } else {
            return GenericObjectValue
        }
      }))

      WorkingTemplateData.forEach(function(element) {
        context.commit('POST_Templates', {pcmContext:'template', data:element})
      })
      
      WorkingObjectData.forEach(function(element) {
        context.commit('pcmObjects/ADD_Object', {pcmContext:'template', data:element}, {root:true})
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

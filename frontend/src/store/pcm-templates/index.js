const TemplatesReady = {
  'workspace': false,
  'actual': false,
  'template': false,
  'catalog': false,
  'floorplan': true,
}
const Templates = {
  'workspace': [],
  'actual': [],
  'template': [],
  'catalog': [],
  'floorplan': [
    {
      'id': 'walljack',
      'name': 'walljack',
      'category_id': 'walljack',
      'type': 'floorplan',
      'function': 'passive',
      'mount_config': 'N/A',
      'insert_constraints': null,
      'blueprint': {
        "front":[
          {
            "type":"connectable",
            "units":24,
            "children":[],
            "port_format":[
              {
                "type":"static",
                "value":"Port",
                "count":1,
                "order":0
              },{
                "type":"incremental",
                "value":"1",
                "count":48,
                "order":1
              }
            ],
            "port_layout":{
              "cols":24,
              "rows":1
            },
            "media":1,
            "port_connector":1,
            "port_orientation":1
          }
        ],
        "rear":[
          {
            "type":"generic",
            "units":24,
            "children":[]
          }
        ]
      }
    }
  ],
}
const DefaultStaticValue = 'Port'
const DefaultIncrementalValue = '1'
const DefaultSeriesValue = 'A,B,C'
const DefaultCount = 1
const DefaultPortFormat = [
  {'type': 'static', 'value': DefaultStaticValue, 'count': DefaultCount, 'order': 0},
  {'type': 'incremental', 'value': DefaultIncrementalValue, 'count': 48, 'order': 2},
  {'type': 'series', 'value': DefaultSeriesValue, 'count': DefaultCount, 'order': 1},
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
    DefaultStaticValue,
    DefaultIncrementalValue,
    DefaultSeriesValue,
    DefaultCount,
    DefaultPortFormat,
    DefaultPortLayout,
    DefaultEncLayout,
  },
  mutations: {
    SET_Templates(state, {pcmContext, data}) {
      state.Templates[pcmContext] = data
    },
    ADD_Template(state, {pcmContext, data}) {
      state.Templates[pcmContext].push(data)
    },
    UPDATE_Template(state, {pcmContext, data, src=false}) {

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
  actions: {}
}

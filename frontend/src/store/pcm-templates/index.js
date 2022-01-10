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

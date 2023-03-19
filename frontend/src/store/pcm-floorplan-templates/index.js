const Blueprint = {
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
      "type":
      "generic",
      "units":24,
      "children":[]
    }
  ]
}

const FloorplanTemplatesReady = false
const FloorplanTemplates = [
  {
    'id': 'device',
    'name': 'Device',
    'category_id': 'device',
    'icon': 'MonitorIcon',
    'function': 'endpoint',
    'floorplan_object_type': 'device',
    'blueprint': JSON.parse(JSON.stringify(Blueprint))
  },
  {
    'id': 'camera',
    'name': 'Camera',
    'category_id': 'camera',
    'icon': 'VideoIcon',
    'function': 'endpoint',
    'floorplan_object_type': 'camera',
    'blueprint': JSON.parse(JSON.stringify(Blueprint))
  },
  {
    'id': 'wap',
    'name': 'WAP',
    'category_id': 'wap',
    'icon': 'WifiIcon',
    'function': 'endpoint',
    'floorplan_object_type': 'wap',
    'blueprint': JSON.parse(JSON.stringify(Blueprint))
  },
  {
    'id': 'walljack',
    'name': 'Walljack',
    'category_id': 'walljack',
    'icon': 'GridIcon',
    'function': 'passive',
    'floorplan_object_type': 'walljack',
    'blueprint': JSON.parse(JSON.stringify(Blueprint))
  },
]

export default {
  namespaced: true,
  state: {
    FloorplanTemplatesReady,
    FloorplanTemplates,
  },
  mutations: {},
  actions: {}
}

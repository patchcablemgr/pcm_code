const FloorplanCategoriesReady = true
const FloorplanCategories = [
  {
    'id': 'device',
    'name': 'Device',
    'color': '#9B9B9BFF',
    'default': 0
  },
  {
    'id': 'walljack',
    'name': 'Walljack',
    'color': '#9B9B9BFF',
    'default': 0
  },
  {
    'id': 'wap',
    'name': 'WAP',
    'color': '#9B9B9BFF',
    'default': 0
  },
  {
    'id': 'Camera',
    'name': 'camera',
    'color': '#9B9B9BFF',
    'default': 0
  },
]

export default {
  namespaced: true,
  state: {
    FloorplanCategoriesReady,
    FloorplanCategories,
  },
  mutations: {},
  actions: {}
}

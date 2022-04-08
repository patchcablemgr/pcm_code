const UsersReady = false
const Users = []

export default {
  namespaced: true,
  state: {
    Users,
    UsersReady,
  },
  mutations: {
    SET_Users(state, {data}) {
      state.Users = data
    },
    ADD_User(state, {data}) {
      console.log(data)
      state.Users.push(data)
    },
    UPDATE_User(state, {data}) {

      const ID = data.id
      const Index = state.Users.findIndex((item) => item.id == ID)
      state.Users.splice(Index, 1, data)
    },
    REMOVE_User(state, {data}) {
      const ID = data.id
      const Index = state.Users.findIndex((item) => item.id == ID)
      state.Users.splice(Index, 1)
    },
    SET_Ready(state, {ReadyState}) {
      state.UsersReady = ReadyState
    },
  },
  actions: {}
}

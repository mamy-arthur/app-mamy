export default {
  state: () => ({
    user_permissions: [],
    is_redirection: false
  }),
  mutations: {
    set_user_permissions(state, permissions) {
      state.user_permissions = permissions
    },
    set_is_redirection(state, redirection) {
      state.is_redirection = redirection
    }
  },
}

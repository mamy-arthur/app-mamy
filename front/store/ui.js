export default {
  state: () => ({
    menu_folder_types: [],
  }),
  mutations: {
    set_folder_types(state, types) {
      state.menu_folder_types = types
    },
  },
}

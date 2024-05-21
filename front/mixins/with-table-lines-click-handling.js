export default {
  methods: {
    async handleRowClick(row, column, event) {
      if (
        this.row_click_action &&
        [('touch', 'mouse')].includes(event.pointerType) &&
        (!column.hasOwnProperty('className') ||
          column.className !== 'actions-column')
      ) {
        await this.row_click_action(row, column, event)
      } else if (!this.row_click_action) {
        console.warn('the "row_click_action" function has not been provided!')
      }
    },
  },
}

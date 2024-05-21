export default {
  data() {
    return {
      page: 1,
      items_per_page: 10,
      total_items: 10,
    }
  },
  methods: {
    async updatePagination(page) {
      this.page = page
      await this.$fetch()
    },
  },
}

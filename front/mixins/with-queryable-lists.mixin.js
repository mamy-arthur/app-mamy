export default {
  data() {
    return {
      queried_choices: {},
    }
  },
  methods: {
    queryChoices(field, choices) {
      return (query) => {
        if (query !== '' && choices && !field.loading) {
          field.loading = true

          this.queried_choices[field.name] = choices.filter((item, index) => {
            return (
              item[field.value_property ? field.value_property : index]
                .toLowerCase()
                .includes(query.toLowerCase()) ||
              item.label?.toLowerCase().includes(query.toLowerCase())
            )
          })

          field.loading = false
        }
      }
    },
  },
}

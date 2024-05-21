export default {
  data() {
    return {
      files_to_upload: [],
    }
  },
  methods: {
    async downloadFile(filename) {
      return this.$axios({
        url: `/storage-api/file/${filename}`,
        method: 'GET',
        responseType: 'blob',
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', filename)
        document.body.appendChild(link)
        link.click()
      })
    },
    async uploadFiles() {
      const remote_files_refs = []

      await Promise.all(
        this.files_to_upload.map(async (file) => {
          const form_data = new FormData()

          form_data.append('file', file)

          const { filename } = await this.$axios.$post(
            '/storage-api/file',
            form_data,
            {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            }
          )

          remote_files_refs.push(filename)
        })
      )

      this.files_to_upload.splice(0)

      return remote_files_refs
    },
    async openFileInNewTab(filename) {
      return this.$axios({
        url: `/storage-api/file/${filename}`,
        method: 'GET',
        responseType: 'arraybuffer',
      }).then((response) => {
        var file = new Blob([response.data], { type: 'application/pdf' })
        var file_url = window.URL.createObjectURL(file)
        window.open(file_url, '_blank')
      })
    },
  },
}

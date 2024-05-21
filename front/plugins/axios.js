export default function ({ $axios, $auth, redirect }) {
  $axios.onRequest((config) => {
    console.log('Making request to ' + config.url)
  })

  const redirect_to_home = async () => await redirect('/')

  $axios.onError(async (error) => {
    const code = parseInt(error.response && error.response.status)

    const payload = error.response || error

    switch (code) {
      case 401:
        console.warn(payload)
        console.log('Logging out...')
        await redirect('/login')
        await $auth.logout()
        break

      case 403:
        console.warn(payload)
        console.warn('API resource forbidden, redirecting to home page')
        await redirect_to_home()
        break

      case 404:
        console.error('API resource not found!', payload)
        break

      default:
        if (typeof payload === 'string') {
          throw new Error(payload)
        } else {
          throw payload
        }
    }
  })
}

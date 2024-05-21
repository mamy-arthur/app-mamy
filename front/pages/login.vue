<template>
  <div class="row align-items-center justify-content-center full-height">
    <div class="col-md-8">
      <h1>Base App Arthur</h1>
      <validation-observer v-slot="{ handleSubmit }">
        <form ref="login_form" @submit.prevent="handleSubmit(login)" novalidate>
          <div>
            <base-input
              label="Adresse email"
              name="email"
              type="email"
              required="required"
              v-model="credentials.username"
            ></base-input>
          </div>
          <div>
            <base-input
              label="Mot de passe"
              name="password"
              type="password"
              required="required"
              v-model="credentials.password"
            ></base-input>
          </div>
          <div class="row actions-zone">
            <div class="col-sm text-right">
              <span>
                <nuxt-link :to="email_sending_link"
                  >Mot de passe oublié ?</nuxt-link
                >
              </span>
            </div>
          </div>
          <div class="row actions-zone">
            <div class="col-sm">
              <base-button type="primary" native-type="submit" class="col-md-12"
                >Se connecter</base-button
              >
            </div>
          </div>
        </form>
      </validation-observer>
    </div>
  </div>
</template>

<script>
const emptyCredentials = {
  username: '',
  password: '',
}

export default {
  layout: 'login',
  head() {
    return {
      title: 'Login page',
    }
  },
  data() {
    return {
      credentials: { ...emptyCredentials },
      remember_me: false,
    }
  },
  methods: {
    async login() {
      try {
        const response = await this.$auth.loginWith('local', {
          data: this.credentials,
        })
        console.log(`${this.credentials.username} has been logged in`)
      } catch (err) {
        console.error(err)
        alert('Could not log you in!')
      }
    },
  },
  computed: {
    email_sending_link() {
      return `/pass-reset`
    },
  },
}
</script>

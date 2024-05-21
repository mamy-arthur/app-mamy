<template>
  <div class="row align-items-center justify-content-center full-height">
    <div class="col-md-8">
      <h1>Modification de mot de passe</h1>
      <validation-observer v-slot="{ handleSubmit }">
        <form ref="form" @submit.prevent="handleSubmit(makeSubmit)" novalidate>
          <div>
            <base-input
              label="Mot de passe"
              name="password"
              type="password"
              required="required"
              v-model="form_data.password"
            ></base-input>
          </div>
          <div>
            <base-input
              label="Confirmation du mot de passe"
              name="password confirmation"
              type="password"
              required="required"
              v-model="form_data.password_check"
              :rules="{ is: form_data.password }"
            ></base-input>
          </div>
          <div class="row actions-zone">
            <div class="col-sm"></div>
            <div class="col-sm">
              <base-button type="primary" native-type="submit" class="col-md-12"
                >Modifier</base-button
              >
            </div>
          </div>
        </form>
      </validation-observer>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'login',
  auth: false,
  head() {
    return {
      title: 'Password reset page',
    }
  },
  async asyncData({ params, $axios }) {
    const token = params.token

    await $axios.$get(`/auth-api/pass-reset/${token}`)

    return { token }
  },
  data() {
    return {
      form_data: {
        password: '',
        password_check: '',
      },
    }
  },
  methods: {
    async makeSubmit() {
      await this.$axios.$post(
        `/auth-api/pass-reset/${this.token}`,
        this.form_data
      )
      await this.$router.push('/login')
    },
  },
}
</script>

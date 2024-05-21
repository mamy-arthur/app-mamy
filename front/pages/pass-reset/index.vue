<template>
  <div class="row align-items-center justify-content-center full-height">
    <div class="col-md-8">
      <h1>Base App Arthur</h1>
      <validation-observer v-slot="{ handleSubmit }">
        <form ref="form" @submit.prevent="handleSubmit(makeSubmit)" novalidate>
          <div>
            <base-input
              label="Adresse email"
              name="email"
              type="email"
              required="required"
              v-model="form_data.email"
            ></base-input>
          </div>
          <div class="row actions-zone">
            <div class="col-sm text-center">
              <base-button type="primary" native-type="submit" class="col-md-6"
                >Envoyer</base-button
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
      title: 'Email sending page',
    }
  },
  data() {
    return {
      form_data: {
        email: '',
      },
    }
  },
  methods: {
    async makeSubmit() {
      try {
        const url_template = `${process.env.BASE_URL}/pass-reset/{{token}}`

        await this.$axios.$post(
          `/users-api/user/reset-password?username=${this.form_data.email}`,
          {
            message: `Une demande de récupération de mot de passe a été effectuée pour votre compte utilisateur.
            <br>
            <br>
            Si cette demande a été effectuée par vous, veuillez vous rendre <a href="${url_template}">ici</a> pour redéfinir votre mot de passe.
            <br>
            Ou alors suivre l'url suivante:
            <br>
            ${url_template}
            <br>
            <br>
            Si vous n'êtes pas à l'origine de de cette action, merci d'en informer votre administrateur.`,
          }
        )

        this.$notify({
          type: 'success',
          verticalAlign: 'bottom',
          horizontalAlign: 'right',
          message: `Vous recevrez bientôt un email avec les instructions de récupération de votre mot de passe`,
          timeout: 30 * 1000,
          title: `Mot de passe oublié`,
          closeOnClick: false,
        })
      } catch (exception) {
        this.$notify({
          type: 'error',
          verticalAlign: 'bottom',
          horizontalAlign: 'right',
          message: `Votre requete n'a pas pu etre traitée, veuillez réessayer ultérieurement`,
          timeout: 20 * 1000,
          title: `Action en échec!`,
          closeOnClick: false,
        })
      }
    },
  },
}
</script>

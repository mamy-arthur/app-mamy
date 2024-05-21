<template>
  <div class="container-fluid">
    <div class="content-subheader">
      <base-button outline type="secondary" class="subheader-main-action">
        <nuxt-link to="/users#users-list">Utilisateurs</nuxt-link>
      </base-button>
      <span class="subheader-sep"> >> </span>
      <span class="subheader-title"> {{ this.page_title }} </span>
    </div>
    <div class="container">
      <update-user-form
        :user="user_form_data"
        v-on:form:re-init="refreshUser"
        v-on:user:updated="refreshUser"
        class="content-section"
      ></update-user-form>
      <div class="container actions-zone">
        <base-button outline type="secondary" @click="getNewPasswordToken"
          >Modification de mot de passe</base-button
        >
        <span v-show="password_token"
          >Pour modifier le mot de passe de cet utilisateur, rendez-vous
          <nuxt-link :to="change_password_link">ici</nuxt-link></span
        >
      </div>
    </div>
  </div>
</template>

<script>
import UpdateUserForm from '@/components/users/UpdateUserForm'

export default {
  components: { UpdateUserForm },
  head() {
    return {
      title: this.page_title,
    }
  },
  async asyncData({ params, $axios }) {
    const user_id = params.user_id

    const user = await $axios.$get(`/users-api/user/${user_id}`)
    let password_token = await $axios.$get(
      `/auth-api/pass-reset?username=${user.email}`
    )

    return { user, password_token }
  },
  data() {
    return {
      password_token: '',
    }
  },
  methods: {
    async refreshUser() {
      this.user = await this.$axios.$get(`/users-api/user/${this.user.id}`)
    },
    async getNewPasswordToken() {
      this.password_token = await this.$axios.$post('/auth-api/pass-reset', {
        username: this.user.email,
      })
    },
  },
  computed: {
    user_full_name() {
      return [this.user.first_name, this.user.last_name].join(' ')
    },
    page_title() {
      return `Utilisateur - ${this.user_full_name}`
    },
    user_form_data() {
      let output

      if (typeof this.user.service === 'object') {
        output = { ...this.user, service: this.user.service.id }
      } else {
        output = { ...this.user }
      }

      return output
    },
    change_password_link() {
      return `/pass-reset/${this.password_token}`
    },
  },
}
</script>

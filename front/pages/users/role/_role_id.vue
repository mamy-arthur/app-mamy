<template>
  <div class="container-fluid">
    <div class="content-subheader">
      <base-button outline type="secondary" class="subheader-main-action">
        <nuxt-link to="/users#users-roles-list">Rôles</nuxt-link>
      </base-button>
      <span class="subheader-sep"> >> </span>
      <span class="subheader-title"> {{ this.page_title }} </span>
    </div>
    <div class="container">
      <update-user-role-form
        :role="this.role"
        v-on:form:re-init="refreshRole"
        v-on:user-role:updated="refreshRole"
      ></update-user-role-form>
    </div>
  </div>
</template>

<script>
import UpdateUserRoleForm from '@/components/users/roles/UpdateUserRoleForm'

const getRole = async (role_id, axios) => {
  return await axios.$get(`/auth-api/role/${role_id}`)
}

export default {
  components: { UpdateUserRoleForm },
  head() {
    return {
      title: this.page_title,
    }
  },
  async asyncData({ params, $axios }) {
    const role_id = params.role_id

    const role = await getRole(role_id, $axios)

    return { role }
  },
  methods: {
    async refreshRole() {
      this.role = await getRole(this.role.id, this.$axios)
    },
  },
  computed: {
    role_label() {
      return `${this.role.name} (${this.role.code})`
    },
    page_title() {
      return `Role - ${this.role_label}`
    },
  },
}
</script>

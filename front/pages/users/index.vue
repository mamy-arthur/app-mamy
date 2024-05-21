<template>
  <div class="container-fluid tabbed-content">
    <tabs
      tab-nav-wrapper-classes="tabbed-content-tabs-wrapper"
      ref="tabs"
      :active-tab="getActiveTab()"
    >
      <tab-pane
        title="Utilisateurs"
        id="users-list"
        icon="fa fa-users-cog"
        v-if="can('user', 'entity', 'view')"
      >
        <users-list-table ref="usersListTable"></users-list-table>
      </tab-pane>
      <tab-pane
        title="Créer utilisateur"
        id="create-user"
        icon="fa fa-user-plus"
        v-if="can('user', 'entity', 'create')"
      >
        <create-user-form
          ref="createUserForm"
          v-on:user:created="onUserCreated()"
        ></create-user-form>
      </tab-pane>
      <tab-pane
        title="Rôles"
        id="users-roles-list"
        icon="fa fa-user-tag"
        v-if="can('role', 'entity', 'view')"
      >
        <users-roles-list-table ref="rolesListTable"></users-roles-list-table>
      </tab-pane>
      <tab-pane
        title="Créer rôle"
        id="create-user-role"
        icon="fa fa-user-edit"
        v-if="can('role', 'entity', 'create')"
      >
        <create-user-role-form
          v-on:user-role:created="onUserRoleCreated()"
        ></create-user-role-form>
      </tab-pane>
    </tabs>
  </div>
</template>

<script>
import TabPane from '~/components/argon-core/Tabs/Tab'
import Tabs from '~/components/argon-core/Tabs/Tabs'
import CreateUserForm from '@/components/users/CreateUserForm'
import UsersListTable from '@/components/users/UsersListTable'
import UsersRolesListTable from '~/components/users/roles/UsersRolesListTable'
import CreateUserRoleForm from '~/components/users/roles/CreateUserRoleForm'

export default {
  components: {
    CreateUserRoleForm,
    UsersRolesListTable,
    UsersListTable,
    CreateUserForm,
    Tabs,
    TabPane,
  },
  head() {
    return {
      title: 'Gestion utilisateurs et roles',
    }
  },
  data() {
    return {
      tabs: [
        'users-list',
        'create-user',
        'users-roles-list',
        'create-user-role',
      ],
    }
  },
  methods: {
    getActiveTab() {
      const hash = this.$route.hash.substr(1)
      return this.tabs.includes(hash) ? hash : 'users-list'
    },
    activateTab(tabId) {
      this.$refs.tabs.findAndActivateTab(tabId)
    },
    refreshRolesList() {
      this.$refs.rolesListTable.$fetch()
      this.$refs.createUserForm.$fetch()
    },
    refreshUsersList() {
      this.$refs.usersListTable.$fetch()
    },
    onUserRoleCreated() {
      this.activateTab('users-roles-list')
      this.refreshRolesList()
    },
    onUserCreated() {
      this.activateTab('users-list')
      this.refreshUsersList()
    },
    onTabClicked(tab) {
      switch (tab.id) {
        case 'users-list':
          this.refreshUsersList()
          break

        case 'users-roles-list':
          this.refreshRolesList()
          break
      }
    },
  },
  mounted() {
    this.$refs.tabs.handleClick = this.onTabClicked
  },
}
</script>

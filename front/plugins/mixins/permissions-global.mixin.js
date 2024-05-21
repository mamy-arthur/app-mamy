import Vue from 'vue'
Vue.mixin({
  data() {
    return {
      userPermissions: [],
    }
  },
  methods: {
    can(resource, resourceType, action) {
      return this.userPermissions.find((permission) => {
        return (
          permission.actions.includes(action) &&
          permission.permission_type.resource == resource &&
          permission.permission_type.resource_type == resourceType
        )
      })
    },
    translatePermissionAction(value) {
      let output = value
      switch (value) {
        case 'view':
          output = 'Voir'
          break
        case 'create':
          output = 'Créer'
          break
        case 'delete':
          output = 'Supprimer'
          break
        case 'update':
          output = 'Editer'
          break
      }

      return output
    },
  },
  async created() {
    this.userPermissions = this.$store?.state.permissions.user_permissions || []
  },
})

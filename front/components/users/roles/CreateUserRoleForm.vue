<template>
  <div class="container">
    <validation-observer ref="formObserver" v-slot="{ handleSubmit }">
      <form
        @submit.prevent="handleSubmit(makeCreate)"
        :key="formKey"
        novalidate
      >
        <div class="row content-section">
          <div class="col-md-6">
            <div>
              <base-input
                label="Nom du rôle"
                name="Nom du rôle"
                required="required"
                v-model="role.name"
              ></base-input>
            </div>
            <div>
              <base-input
                label="Code"
                name="Code"
                required="required"
                v-model="role.code"
              ></base-input>
            </div>
            <div>
              <base-input
                label="Ressources"
                name="Ressources"
                id="resource_type"
              >
                <el-select
                  filterable
                  multiple
                  placeholder="..."
                  v-model="selected_permission_types"
                >
                  <el-option
                    v-for="permission_type in availables_permissions_types"
                    :value="permission_type.id"
                    :label="getPermissionLabel(permission_type)"
                    :key="permission_type.id"
                  ></el-option>
                </el-select>
              </base-input>
            </div>
          </div>
          <div class="col-md-6">
            <h3>Actions</h3>
            <div class="fields-block">
              <base-input
                :label="getPermissionLabel(getPermission(permission_type_id))"
                :name="getPermissionLabel(getPermission(permission_type_id))"
                v-for="(permission_type_id, index) in selected_permission_types"
              >
                <base-input
                  v-for="action in getPermissionActions(permission_type_id)"
                  class="actions"
                >
                  <input
                    type="checkbox"
                    :id="`${action}.${permission_type_id}`"
                    :true-value="action"
                    :value="action"
                    v-model="role.permissions[index].actions"
                  />
                  <label :for="`${action}.${permission_type_id}`">{{
                    translatePermissionAction(action)
                  }}</label>
                </base-input>
              </base-input>
            </div>
          </div>
        </div>
        <div class="row actions-zone">
          <div class="col-sm">
            <base-button type="secondary" native-type="reset" class="col-md-12"
              >Annuler</base-button
            >
          </div>
          <div class="col-sm">
            <base-button type="primary" native-type="submit" class="col-md-12"
              >Créer</base-button
            >
          </div>
        </div>
      </form>
    </validation-observer>
  </div>
</template>

<script>
import { getRandomString } from '@/utils/string'
import { Option, Select } from 'element-ui'
import { PERMISSIONS_LABELS } from '@/utils/permissions'

const emptyRole = {
  name: '',
  code: '',
  permissions: [],
}

export default {
  name: 'CreateUserRoleForm',
  components: { [Select.name]: Select, [Option.name]: Option },
  async fetch() {
    const permission_types = await this.$axios.$get(
      `/auth-api/permissions-types`
    )
    this.availables_permissions_types = permission_types.sort((item1, item2) =>
      item1 === 'front-page' ? 1 : -1
    )
  },

  data: () => ({
    role: { ...emptyRole },
    formKey: getRandomString(15),
    availables_permissions_types: [],
    selected_permission_types: [],
  }),
  methods: {
    async makeCreate() {
      try {
        await this.$axios.$post('/auth-api/role', this.role)
      } catch (exception) {
        console.error(exception)
        if (exception.status == 400 && exception.data.errors) {
          this.$notify({
            type: 'warning',
            verticalAlign: 'bottom',
            horizontalAlign: 'left',
            message: `Certaines des données transmises ont été rejétées, veuillez les vérifier et réessayer.`,
            timeout: 7000,
          })
        }
        return
      }
      this.$emit('user-role:created')
      this.role = { ...emptyRole }
      this.formKey = getRandomString(15)
      this.selected_permission_types = []
    },

    getPermissionLabel(permission_type) {
      return PERMISSIONS_LABELS[this.getPermissionReference(permission_type)]
    },

    getPermissionReference(permission_type) {
      return permission_type.resource_type + ':' + permission_type.resource
    },

    getPermissionActions(permission_type_id) {
      return this.getPermission(permission_type_id).possibles_actions
    },

    getPermission(filter) {
      return this.availables_permissions_types.find((type) => {
        let check = false

        switch (true) {
          case typeof filter === 'number':
            check = type.id === filter
            break
          case typeof filter === 'string':
            const key_parts = filter.split(':')

            if (key_parts.length === 2) {
              check =
                type.resource_type === key_parts[0] &&
                type.resource === key_parts[1]
            }
            break
        }

        return check
      })
    },
  },

  watch: {
    selected_permission_types: function (selected_permission_types) {
      let actions = []
      let existing_permission = ''
      let previous_permissions = this.role.permissions
      this.role.permissions = []
      selected_permission_types.forEach((permission_type, index) => {
        existing_permission = previous_permissions.find(
          (permission) => permission.permission_type === permission_type
        )

        actions = existing_permission ? existing_permission.actions : []

        this.$set(this.role.permissions, index, {
          permission_type: permission_type,
          actions: actions,
        })

        const whole_permission = this.getPermission(permission_type)

        if (whole_permission?.related_perm_types?.length) {
          const { related_perm_types } = whole_permission

          related_perm_types.forEach((perm) => {
            const whole_sub_perm = this.getPermission(perm)
            if (
              whole_sub_perm?.id &&
              !this.selected_permission_types.includes(whole_sub_perm.id)
            ) {
              this.selected_permission_types.push(whole_sub_perm.id)
            }
          })
        }
      })
    },
  },
}
</script>

<style lang="scss" scoped>
@import 'assets/sass/app/role';
</style>

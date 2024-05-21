import {
  PERMISSION_PARTS_SEPARATOR,
  PERMISSIONS_PAGES,
} from '@/utils/permissions'

export default async function ({ $axios, $auth, redirect, route, store }) {
  const user_permissions = $auth.loggedIn
    ? await $axios.$get(`/auth-api/user-permissions`)
    : []
  store.commit('permissions/set_user_permissions', user_permissions)

  const permission_page = PERMISSIONS_PAGES[route.name]

  let user_has_page_permission = true

  if (permission_page) {
    const permission_parts = permission_page.split(PERMISSION_PARTS_SEPARATOR)

    user_has_page_permission = user_permissions.find((permission) => {
      return (
        permission.actions.includes(permission_parts[2]) &&
        permission.permission_type.resource == permission_parts[1] &&
        permission.permission_type.resource_type == permission_parts[0]
      )
    })
  }

  if (
    !user_has_page_permission &&
    route.name != 'login' &&
    route.name != 'pass-reset'
  ) {
    store.commit('permissions/set_is_redirection', true)
    return redirect(403, '/')
  }
}

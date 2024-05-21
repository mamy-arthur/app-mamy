<template>
  <div class="body-wrapper">
    <notifications></notifications>
    <dashboard-navbar type="light"></dashboard-navbar>
    <side-bar>
      <template slot-scope="props" slot="links">
        <sidebar-item
          v-if="can('dashboard', 'front-page', 'view')"
          :link="{
            icon: 'fa fa-desktop',
            name: 'Dashboard',
            path: '/dashboard',
          }"
        ></sidebar-item>
        <sidebar-item
          v-if="can('users-roles-management', 'front-page', 'view')"
          :link="{
            icon: 'fa fa-user-cog',
            name: 'Utilisateurs',
            path: '/users',
          }"
        ></sidebar-item>
      </template>
    </side-bar>
    <div class="main-content" style="top: 40px">
      <nuxt></nuxt>
    </div>
  </div>
</template>
<script>
/* eslint-disable no-new */
import PerfectScrollbar from 'perfect-scrollbar'
import 'perfect-scrollbar/css/perfect-scrollbar.css'
import DashboardNavbar from '~/components/layouts/argon/DashboardNavbar.vue'
import DashboardContent from '~/components/layouts/argon/Content.vue'

function hasElement(className) {
  return document.getElementsByClassName(className).length > 0
}

function initScrollbar(className) {
  if (hasElement(className)) {
    new PerfectScrollbar(`.${className}`)
  } else {
    // try to init it later in case this component is loaded async
    setTimeout(() => {
      initScrollbar(className)
    }, 100)
  }
}

export default {
  components: {
    DashboardNavbar,
    DashboardContent,
  },
  methods: {
    initScrollbar() {
      let isWindows = navigator.platform.startsWith('Win')
      if (isWindows) {
        initScrollbar('scrollbar-inner')
      }
    },
    getFoldersTypes() {
      return this.$store.state.ui.menu_folder_types
    },
  },
  mounted() {
    this.initScrollbar()
  },
}
</script>

<style>
html,
body,
div#__nuxt,
div#__layout,
.full-height {
  height: 100%;
}
.body-wrapper {
  padding-top: 77px;
}
</style>

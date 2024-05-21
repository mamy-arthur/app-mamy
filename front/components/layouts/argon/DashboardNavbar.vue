<template>
  <base-nav
    container-classes="container-fluid"
    class="navbar-top border-bottom navbar-expand"
    type="white"
  >
    <!-- Navbar links -->
    <ul class="navbar-nav align-items-start">
      <nuxt-link to="/">
        <img
          src="~/assets/images/app_logo.png"
          style="max-height: 80px; object-fit: contain; border-radius: 50%;"
      /></nuxt-link>
    </ul>
    <ul class="navbar-nav align-items-center ml-md-auto">
      <!-- <div class="navbar-search">
        <div class="form-group">
          <input
            type="text"
            class="form-control"
            v-model="searchString"
            v-on:keyup.enter="searchData"
            placeholder="Search.."
          />
          <base-button
            class="btn btn-md btn-primary ml-2 mr-2"
            native-type="search"
            type="primary"
            v-on:click="searchData"
            ><i class="fas fa-search"></i
          ></base-button>
        </div>
      </div> -->
      <li class="nav-item d-xl-none">
        <!-- Sidenav toggler -->
        <!-- todo: fix rendering when visible -->
        <div
          class="pr-3 sidenav-toggler"
          :class="{
            active: $sidebar.showSidebar,
            'sidenav-toggler-dark': type === 'default',
            'sidenav-toggler-light': type === 'light',
          }"
          @click="toggleSidebar"
        >
          <div class="sidenav-toggler-inner">
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
          </div>
        </div>
      </li>
      <base-dropdown
        class="nav-item"
        tag="li"
        title-classes="nav-link"
        title-tag="a"
        icon="fa fa-bell"
        menu-classes="dropdown-menu-xl dropdown-menu-right py-0 overflow-hidden"
      >
      </base-dropdown>
    </ul>
    <ul class="navbar-nav align-items-center ml-auto ml-md-0">
      <base-dropdown
        menu-on-right
        class="nav-item"
        tag="li"
        title-tag="a"
        title-classes="nav-link pr-0"
      >
        <a href="#" class="nav-link pr-0" @click.prevent slot="title-container">
          <div class="media align-items-center">
            <span class="avatar avatar-sm rounded-circle">
              <img
                alt="Image placeholder"
                src="~/assets/images/profile-pic.png"
              />
            </span>
            <div class="media-body ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm font-weight-bold">{{
                userDisplayName
              }}</span>
            </div>
          </div>
        </a>
        <template>
          <a href="/dashboard" class="dropdown-item">
            <i class="fa fa-desktop"></i>
            <span>Dashboard</span>
          </a>
          <a href="/settings/pczsar" class="dropdown-item">
            <i class="fa fa-cogs"></i>
            <span>Paramétrage</span>
          </a>
          <a href="#!" class="dropdown-item" @click="logout">
            <i class="fa fa-sign-out-alt"></i>
            <span>Se déconnecter</span>
          </a>
        </template>
      </base-dropdown>
    </ul>
  </base-nav>
</template>
<script>
import { CollapseTransition } from 'vue2-transitions'
import BaseNav from '@/components/argon-core/Navbar/BaseNav.vue'
import Modal from '@/components/argon-core/Modal.vue'

export default {
  components: {
    CollapseTransition,
    BaseNav,
    Modal,
  },
  props: {
    type: {
      type: String,
      default: 'default', // default|light
      description:
        'Look of the dashboard navbar. Default (Green) or light (gray)',
    },
  },
  computed: {
    routeName() {
      const { name } = this.$route
      return this.capitalizeFirstLetter(name)
    },
    userDisplayName() {
      return [this.$auth.user?.first_name, this.$auth.user?.last_name]
        .filter((name) => !!name)
        .join(' ')
    },
  },
  data() {
    return {
      activeNotifications: false,
      showMenu: false,
      searchModalVisible: false,
      searchQuery: '',
      searchString: '',
    }
  },
  methods: {
    searchData() {
      this.$router.push({
        name: 'search',
        query: {
          query: this.searchString,
        },
      })
    },
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    },
    toggleNotificationDropDown() {
      this.activeNotifications = !this.activeNotifications
    },
    closeDropDown() {
      this.activeNotifications = false
    },
    toggleSidebar() {
      this.$sidebar.displaySidebar(!this.$sidebar.showSidebar)
    },
    hideSidebar() {
      this.$sidebar.displaySidebar(false)
    },
    async logout() {
      await this.$auth.logout()
    },
  },
  watch: {
    '$route.query'() {
      this.searchString = this.$route.query.query
      this.$nuxt.$emit('searchEvent', { searchString: this.searchString })
    },
  },
}
</script>
<style>
.navbar-search .form-group {
  display: flex;
  padding: 5px 15px;
  margin-bottom: 0 !important;
}

.navbar-search .form-group .form-control {
  width: auto;
  min-width: 400px;
  border: 1px solid #6f6f6e;
}
</style>

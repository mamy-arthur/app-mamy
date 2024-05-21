<template>
  <div
    class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white"
    style="top: 113px"
    @mouseenter="$sidebar.onMouseEnter()"
    @mouseleave="$sidebar.onMouseLeave()"
    :data="backgroundColor"
  >
    <div class="scrollbar-inner" ref="sidebarScrollArea">
      <slot></slot>
      <div
        class="pr-3 sidenav-toggler"
        :class="{ 'rotate-left': $sidebar.isMinimized }"
        @click="toggleSidebar"
      >
        <div class="sidenav-toggler-inner">
          <i class="fa fa-angle-left"></i>
        </div>
      </div>
      <div class="navbar-inner">
        <ul class="navbar-nav">
          <slot name="links">
            <sidebar-item
              v-for="(link, index) in sidebarLinks"
              :key="link.name + index"
              :link="link"
            >
              <sidebar-item
                v-for="(subLink, index) in link.children"
                :key="subLink.name + index"
                :link="subLink"
              >
              </sidebar-item>
            </sidebar-item>
          </slot>
        </ul>
        <slot name="links-after"></slot>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'sidebar',
  props: {
    title: {
      type: String,
      default: 'Creative Tim',
      description: 'Sidebar title',
    },
    shortTitle: {
      type: String,
      default: 'CT',
      description: 'Sidebar short title',
    },
    logo: {
      type: String,
      default:
        'https://demos.creative-tim.com/nuxt-argon-dashboard-pro/img/brand/green.png',
      description: 'Sidebar app logo',
    },
    backgroundColor: {
      type: String,
      default: 'vue',
      validator: (value) => {
        let acceptedValues = [
          '',
          'vue',
          'blue',
          'green',
          'orange',
          'red',
          'primary',
        ]
        return acceptedValues.indexOf(value) !== -1
      },
      description:
        'Sidebar background color (vue|blue|green|orange|red|primary)',
    },
    sidebarLinks: {
      type: Array,
      default: () => [],
      description:
        "List of sidebar links as an array if you don't want to use components for these.",
    },
    autoClose: {
      type: Boolean,
      default: true,
      description:
        'Whether sidebar should autoclose on mobile when clicking an item',
    },
  },
  provide() {
    return {
      autoClose: this.autoClose,
    }
  },
  methods: {
    toggleSidebar() {
      if (this.$sidebar) {
        this.$sidebar.toggleMinimize()
        if (
          this.$sidebar.isMinimized &&
          this.$sidebar.breakpoint > window.innerWidth
        ) {
          this.$sidebar.displaySidebar(false)
        }
      }
    },
    onWindowResize() {
      if (
        this.$sidebar.breakpoint > window.innerWidth &&
        !this.$sidebar.isMinimized
      ) {
        this.$sidebar.toggleMinimize()
      }
    },
  },
  mounted() {
    window.addEventListener('resize', this.onWindowResize)
    this.$sidebar.isMinimized = this.$sidebar.breakpoint > window.innerWidth
    this.$sidebar.displaySidebar(this.$sidebar.isMinimized)
  },
  beforeDestroy() {
    if (this.$sidebar.showSidebar) {
      this.$sidebar.showSidebar = false
    }
  },
  destroyed() {
    window.removeEventListener('resize', this.onWindowResize)
  },
}
</script>

<style scoped>
.sidenav-toggler {
  position: absolute;
  top: 0;
  right: 0;
}

.sidenav-toggler-inner {
  font-size: 2em;
}

.rotate-left {
  -ms-transform: rotate(180deg);
  -moz-transform: rotate(180deg);
  -webkit-transform: rotate(180deg);
  transform: rotate(180deg);
}
</style>

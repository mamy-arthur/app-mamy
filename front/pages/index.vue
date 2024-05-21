<template></template>

<script>
export default {
  head() {},
  beforeCreate() {
    this.$router.push({ name: 'dashboard' })
  },
}
</script>

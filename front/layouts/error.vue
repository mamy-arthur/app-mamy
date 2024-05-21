<template>
  <div>
    <h1 v-if="error.statusCode === 404">
      La page demandée est introuvable - 404
    </h1>
    <h1 v-else>Une erreur est survenue - {{ error.statusCode }}</h1>
    <h3>... Aller à la <NuxtLink to="/">page d'acueil</NuxtLink></h3>
  </div>
</template>

<script>
export default {
  props: ['error'],
  layout: 'empty', // you can set a custom layout for the error page
}
</script>

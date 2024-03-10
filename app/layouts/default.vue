<template>
  <div>
    <v-app class="font-all">
      <LayoutElementsNavbar />
      <v-main>
        <slot />
      </v-main>
      <LayoutElementsFooter />
    </v-app>

    <NuxtLoadingIndicator />
  </div>
</template>
<style lang="scss" scoped>
@use '~/assets/scss/base/typhography';
</style>

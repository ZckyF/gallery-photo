<!-- <template>
  <div>
    <NuxtWelcome />
  </div>
</template> -->
<template>
  <NuxtLayout>
    <NuxtPage />

    <NuxtLoadingIndicator />
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  titleTemplate: (titleChunk: any) => {
    return titleChunk ? `Aesphot | ${titleChunk}` : 'Aesphot';
  },
});
</script>

<style lang="scss" scoped>
@use '~/assets/scss/base/typhography';
// @use '~/assets/scss/main.scss';
</style>

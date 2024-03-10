<template>
  <div class="link-bottom-image">
    <v-row class="justify-center mt-7">
      <NuxtLink to="/"> Home</NuxtLink>
      <!-- <NuxtLink to="/leaderboard"> Leaderboard</NuxtLink> -->
      <NuxtLink v-if="cookie" to="/following"> Following</NuxtLink>
    </v-row>
  </div>
</template>

<script lang="ts" setup>
const cookie = useCookie('auth_token');
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
.link-bottom-image {
  .v-row {
    display: flex;
    align-items: center;
    gap: 20px;
    a {
      font-size: 1.1rem;
      font-weight: 500;
      color: variables.$secondary-black;
      text-decoration: none;
      @include mixins.hoover-link(variables.$primary-black);
      &.router-link-active {
        @include mixins.active-link;
      }
    }
  }
}
</style>

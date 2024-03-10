<template>
  <v-navigation-drawer floating permanent class="sidebar" elevation="5">
    <v-list>
      <v-list-item prepend-icon="mdi-cog-outline">
        <h1 class="text-item title-settings">Settings</h1>
      </v-list-item>
    </v-list>
    <v-list density="compact" nav>
      <v-list-item active-class="active" prepend-icon="mdi-account-outline" to="/settings/profile">
        <h1 class="text-item">Profile</h1>
      </v-list-item>
      <v-list-item active-class="active" prepend-icon="mdi-lock-outline" to="/settings/reset-password">
        <h1 class="text-item">Reset Password</h1>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script lang="ts" setup></script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/components/buttons';
.sidebar {
  .title-settings {
    color: variables.$primary-black;
    font-weight: 600;
    font-size: 1.3rem;
  }
  h1 {
    color: variables.$primary-black;
    font-weight: 500;
    font-size: 1rem;
  }
  .active {
    border-radius: 10px 100px 100px 10px;
  }
}
</style>

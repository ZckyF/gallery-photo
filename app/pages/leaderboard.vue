<template>
  <div>
    <LayoutElementsLinkBottomImage />

    <header class="leaderboard-title ml-10 mt-10">
      <h1>Top 50 User</h1>
      <p>Users with the most followers in All time</p>
    </header>
    <div class="select px-10 mt-5">
      <div class="selectItems">
        <v-select clearable v-model="select" :items="selectItems" variant="outlined"></v-select>
      </div>
    </div>

    <!-- <div class="px-12 mt-10">
      <v-row>
        <v-col cols="12" sm="4" md="3">
          <v-skeleton-loader style="background-color: transparent" max-width="700" class="rounded-lg" type="avatar,sentences"></v-skeleton-loader>
        </v-col>
        <v-col cols="12" md="3">
          <v-skeleton-loader style="background-color: transparent" class="rounded-lg" type="image"></v-skeleton-loader>
        </v-col>
        <v-col cols="12" md="3">
          <v-skeleton-loader style="background-color: transparent" class="rounded-lg" type="image"></v-skeleton-loader>
        </v-col>
        <v-col cols="12" md="3">
          <v-skeleton-loader style="background-color: transparent" class="rounded-lg" type="image"></v-skeleton-loader>
        </v-col>
      </v-row>
    </div> -->
    <CollectionUserLists />
  </div>
</template>

<script lang="ts" setup>
const selectItems = ref<[string, string, string]>(['Followers', 'Likes', 'Most Photos']);
const select = ref<string>('Followers');
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
.leaderboard-title {
  h1 {
    color: variables.$primary-black;
    font-weight: 600;
    font-size: 3rem;
  }
  p {
    color: variables.$secondary-black;
    font-weight: 500;
    font-size: 1.2rem;
  }
}
.select {
  display: flex;
  justify-content: end;
  align-items: end;
  .selectItems {
    width: 300px;
  }
}
</style>

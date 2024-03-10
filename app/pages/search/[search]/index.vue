<template>
  <div class="page-search mx-16">
    <header class="title-search">
      <h1>Photo "{{ search }}"</h1>
    </header>
    <div class="link-bottom-image">
      <v-row class="mt-9">
        <NuxtLink :to="`/search/${search}`">
          Photo <span class="ml-1" style="color: rgb(48, 48, 48); font-size: 15px">{{ totalData }}</span></NuxtLink
        >

        <NuxtLink :to="`/search/${search}/folders`"> Folders</NuxtLink>
        <NuxtLink :to="`/search/${search}/users`"> Users</NuxtLink>
      </v-row>
    </div>

    <div class="mt-12">
      <v-row v-if="skeletonLoaders">
        <v-col v-for="index in totalData" :key="index" cols="12" sm="4" md="4">
          <v-skeleton-loader max-width="700" class="rounded-lg" type="image,image"></v-skeleton-loader>
        </v-col>
      </v-row>
      <CollectionImages :images="searchData" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const { search } = useRoute().params;
const url = useRuntimeConfig().public.URL_API;

let searchData = ref([]);
let skeletonLoaders = ref<boolean>(true);
let totalData = ref(0);

try {
  const { data } = await useFetch(`${url}/search/${search}/photos`);
  searchData.value = data.value.data;
  totalData.value = searchData.value.length;
} catch (error) {
  console.error('Error fetching images:', error);
} finally {
  setTimeout(() => {
    skeletonLoaders.value = false;
  }, 500);
}
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
.page-search {
  .title-search {
    margin-top: 100px;
    h1 {
      color: variables.$primary-black;
      font-weight: 600;
      font-size: 3rem;
    }
  }
  .link-bottom-image {
    .v-row {
      display: flex;
      align-items: center;
      gap: 30px;
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
}
</style>

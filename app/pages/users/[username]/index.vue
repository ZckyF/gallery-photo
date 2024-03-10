<template>
  <div class="page-user px-5">
    <LayoutElementsHeaderUser />
    <div class="link px-10 mt-4">
      <NuxtLink :to="`${username}`"> Photos <span class="ml-1" style="color: rgb(48, 48, 48); font-size: 15px">{{ dataImages.length }}</span></NuxtLink>
      <NuxtLink :to="`${username}/folders`"> Folders</NuxtLink>
      <NuxtLink :to="`${username}/saves`"> Saves</NuxtLink>
      <NuxtLink :to="`${username}/likes`"> Likes</NuxtLink>
    </div>
    <div class="px-10 mt-4">
      <v-row v-if="skeletonLoader">
        <v-col v-for="index in dataImages.length" :key="index" cols="12" sm="4" md="4">
          <v-skeleton-loader max-width="700" class="rounded-lg" type="image,image"></v-skeleton-loader>
        </v-col>
      </v-row>
      <CollectionImages v-else :images="dataImages" />
    </div>
  </div>
</template>

<script setup lang="ts">
const { username } = useRoute().params;
const url = useRuntimeConfig().public.URL_API;
const dataImages = ref<[]>([]);
let skeletonLoader = ref<boolean>(true);
console.log(dataImages.value);

try {
  const { data } = await useFetch(`${url}/user/${username}/photos`);
  dataImages.value = data.value.data;
} catch (error) {
  console.error('error', error);
} finally {
  setTimeout(() => {
    skeletonLoader.value = false;
  }, 1000);
}
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
.page-user {
  .link {
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
</style>

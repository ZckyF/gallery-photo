<template>
  <div>
    <LayoutElementsHeroImage :navigationItems="navigationItems" />

    <LayoutElementsLinkBottomImage />

    <header class="random-photo-title ml-10 mt-10">
      <h1>Random Photos</h1>
    </header>

    <div class="px-10 mt-4">
      <v-row v-if="skeletonLoader">
        <v-col v-for="index in images.length" :key="index" cols="12" sm="4" md="4">
          <v-skeleton-loader style="background-color: transparent" max-width="700" class="rounded-lg" type="image,image"></v-skeleton-loader>
        </v-col>
      </v-row>

      <CollectionImages v-else :images="images" />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { useUserStore } from '~/stores/store';

const store = useUserStore();

const navigationItems = ref<[]>(['Join', 'Upload']);
let skeletonLoader = ref<boolean>(true);
const totalImages = ref<number>(0);
const cookie = useCookie('auth_token');

const url = useRuntimeConfig().public.URL_API;
const urlImage = useRuntimeConfig().public.URL_IMAGE;

let images = ref<[]>([]);
let imageHero = ref<object>({});

try {
  const { data } = await useFetch(`${url}/random-photo`);
  images.value = data.value.data;
  totalImages.value = images.value.length;
} catch (error) {
  console.error('Error fetching images:', error);
} finally {
  setTimeout(() => {
    skeletonLoader.value = false;
  }, 500);
}
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';

.random-photo-title {
  h1 {
    color: variables.$primary-black;
    font-weight: 600;
  }
}

/* Add other styles as needed */
</style>

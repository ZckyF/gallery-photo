<template>
  <div class="image-container">
    <header class="name-application ml-7 mt-2">
      <NuxtLink to="/" class="font-aesphot"> Aesphot </NuxtLink>
    </header>
    <nav class="nav-main-image mr-7 mt-3">
      <template v-for="(item, index) in navigationItems">
        <!-- Pindahkan v-for ke elemen yang lebih dalam -->
        <template v-if="item.toLowerCase() === 'upload'">
          <v-btn class="mx-4 gradient-button text-none" :key="index" rounded prepend-icon="mdi-upload" to="upload">
            {{ item }}
          </v-btn>
        </template>
        <template v-else-if="item.toLowerCase() === 'join'">
          <v-btn v-if="!userLogin" to="login" variant="outlined" class="mx-4 join-button-navbar text-none" :key="`join-${item}`">
            <p>{{ item }}</p>
          </v-btn>

          <ButtonsAvatars v-else :key="index" />

          <!-- <v-card :key="item">
            <v-card-text>
              <div class="mx-auto text-center">
                <v-avatar color="brown">
                  <span class="text-h5">halo</span>
                </v-avatar>
                <h3>halo</h3>
                <p class="text-caption mt-1">halo</p>
                <v-divider class="my-3"></v-divider>
                <v-btn rounded variant="text"> Edit Account </v-btn>
                <v-divider class="my-3"></v-divider>
                <v-btn rounded variant="text"> Disconnect </v-btn>
              </div>
            </v-card-text>
          </v-card> -->
        </template>
        <template v-else>
          <NuxtLink :to="`/${item.toLowerCase()}`" :key="index" class="mx-4">
            {{ item }}
          </NuxtLink>
        </template>
      </template>
    </nav>
    <img class="main-image" :src="`/images/sea.jpg`" alt="Sea Image" />
    <div class="main-image-inner-shadow"></div>
    <div class="overlay-text">
      <h1>Images that resonate. Find royalty-free visuals that capture every emotion and story.</h1>
      <SearchsSearch density="default" />
    </div>
    <div class="image-from-user">
      <!-- <p>
        Photo by <NuxtLink :to="`/users/${imageHero.user.username}`"> {{ imageHero.user.username }} </NuxtLink>
      </p> -->
    </div>
  </div>
</template>

<script lang="ts" setup>
const url = useRuntimeConfig().public.URL_API;
const urlImage = useRuntimeConfig().public.URL_IMAGE;
const userLogin = useCookie('auth_token');
const props = defineProps(['navigationItems']);
let imageHero = ref<object>({});

try {
  const { data: hero } = await useFetch(`${url}/random-photo-hero`);
  imageHero.value = hero.value.data;
} catch (error) {
  console.error('Error fetching or using hero photo:', error);
} finally {
}
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
.image-container {
  position: relative;
  z-index: 3;

  .name-application {
    position: absolute;
    z-index: 2;
    a {
      font-size: 35px;
      font-weight: 300;
      color: variables.$primary-white;
      text-decoration: none;
      @include mixins.hoover-link(silver);

      font-family: 'Irish Grover', system-ui !important;
    }
  }

  .nav-main-image {
    position: absolute !important;
    z-index: 2;
    z-index: 2;
    top: 0;
    right: 0;
    a {
      text-decoration: none;
      font-weight: 600;
      color: variables.$primary-white;
      @include mixins.hoover-link(silver);
    }
  }

  .main-image {
    width: 100vw;
    height: 500px;

    object-fit: cover;
    filter: brightness(60%);
  }

  .overlay-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: start;
    color: variables.$primary-white;
  }

  .main-image-inner-shadow {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background-image: linear-gradient(#00000091, #00000000);
  }

  .image-from-user {
    position: absolute;
    z-index: 2;
    right: 120px;
    top: 450px;
    color: variables.$secondary-white;
    font-size: 0.9rem;
    font-weight: 400;
    a {
      text-decoration: underline;
      color: variables.$secondary-white;
    }
  }
}
</style>

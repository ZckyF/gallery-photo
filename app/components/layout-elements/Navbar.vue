<template>
  <v-app-bar :scroll-behavior="!isHomePage ? '' : 'inverted hide'" :scroll-threshold="!isHomePage ? '' : '490'" elevation="1" class="pr-7">
    <header class="name-application ml-7 mr-5 mt-2">
      <NuxtLink to="/" class="font-aesphot"> Aesphot </NuxtLink>
    </header>

    <SearchsSearch density="compact" />
    <div class="nav-main-image">
      <template v-for="(item, index) in navigationItems">
        <!-- Pindahkan v-for ke elemen yang lebih dalam -->
        <template v-if="item.toLowerCase() === 'upload'">
          <v-btn class="mx-4 gradient-button text-none upload" :key="index" rounded prepend-icon="mdi-upload" to="/upload">
            {{ item }}
          </v-btn>
        </template>
        <template v-else-if="item.toLowerCase() === 'join'">
          <v-btn v-if="!userLogin" to="/login" class="mx-4 join-button-navbar text-none" :key="`join-${item}`">
            <p>{{ item }}</p>
          </v-btn>

          <!-- <v-btn v-else icon :key="`${item}`" style="background-color: transparent">
            <v-avatar icon image="/images/sea.jpg"> </v-avatar>
          </v-btn> -->
          <ButtonsAvatars v-else :key="index" />
        </template>
        <template v-else>
          <NuxtLink :to="`/${item.toLowerCase()}`" :key="index" class="mx-4">
            {{ item }}
          </NuxtLink>
        </template>
      </template>
    </div>
  </v-app-bar>
</template>

<script lang="ts" setup>
const navigationItems = ref([ 'Join', 'Upload']);
const searchQuery = ref<string>('');

const userLogin = useCookie('auth_token');

const route = useRoute();
const isHomePage = ref(route.path === '/' || route.path === '/following');

const handleSearch = () => {
  if (searchQuery.value.trim() !== '') {
    // Arahkan pengguna ke halaman pencarian dengan parameter query
    router.push(`/search/${encodeURIComponent(searchQuery.value)}`);
  }
};

watchEffect(() => {
  isHomePage.value = route.path === '/' || route.path === '/following';
});
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
@import url('https://fonts.googleapis.com/css2?family=Irish+Grover&display=swap');
.name-application {
  a {
    font-size: 35px;
    font-weight: 300;
    color: variables.$primary-black;
    text-decoration: none;
    @include mixins.hoover-link(variables.$secondary-black);

    font-family: 'Irish Grover', system-ui !important;
  }
}
.nav-main-image {
  a {
    text-decoration: none;
    font-weight: 500;
    color: variables.$primary-black;
    @include mixins.hoover-link(silver);
  }

  .upload {
    font-weight: 700;
    color: variables.$primary-white;
  }
}
</style>

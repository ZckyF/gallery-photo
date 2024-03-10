<template>
  <!-- <v-list style="background-color: transparent" class="px-5">
    <v-list-item>
      <template v-slot:prepend>
        <div class="number px-5">
          <p>1</p>
        </div>
        <v-avatar size="48" image="/images/sea.jpg"></v-avatar>
      </template>

      <template v-slot:default>
        <v-list-item-content class="content-user">
          <h1>Username</h1>
          <p>follower</p>
        </v-list-item-content>
      </template>

      <template v-slot:append>
        <v-row class="px-6">
          <div class="images">
            <img src="/images/sea.jpg" alt="" />
          </div>
          <div class="images">
            <img src="/images/sea.jpg" alt="" />
          </div>
          <div class="images">
            <img src="/images/sea.jpg" alt="" />
          </div>
        </v-row>
      </template>
    </v-list-item>
  </v-list> -->
  <div class="px-12 mt-10">
    <v-row>
      <v-col cols="12" sm="3" md="3" style="display: flex">
        <div class="number mx-1">
          <p>1</p>
        </div>
        <NuxtLink :to="`/`" class="image-profile mx-3">
          <img src="/images/sea.jpg" alt="" />
        </NuxtLink>
        <div class="content-user">
          <NuxtLink to="/">
            <h1>Username</h1>
            <p>follower</p>
          </NuxtLink>
        </div>
      </v-col>
      <v-col cols="12" md="3">
        <NuxtLink :to="`/`" class="images">
          <img src="/images/sea.jpg" alt="" />
        </NuxtLink>
      </v-col>
      <v-col cols="12" md="3">
        <NuxtLink :to="`/`" class="images">
          <img src="/images/photo-cuy.jpg" alt="" />
        </NuxtLink>
      </v-col>
      <v-col cols="12" md="3">
        <NuxtLink :to="`/`" class="images">
          <img src="/images/sea.jpg" alt="" />
          <div class="overlay">
            <p class="photo-count">10</p>
          </div>
        </NuxtLink>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';

.number {
  p {
    color: variables.$primary-black;
    font-size: 2rem;
  }
}
.content-user {
  a {
    text-decoration: none;
  }
  h1 {
    color: variables.$primary-black;
    font-weight: 600;
  }
  p {
    color: variables.$secondary-black;
    font-weight: 500;
  }
}

.image-profile {
  img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    transition: all 0.2s ease;
    &:hover {
      filter: brightness(70%);
    }
  }
}

.images {
  position: relative;
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    background-size: contain;
    transition: all 0.2s ease;
    border-radius: 10px;
    // object-fit: contain;
    display: block;
  }

  &:hover img {
    filter: brightness(70%);
  }

  .overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    top: 0;
  }

  .photo-count {
    color: variables.$primary-white;
    font-size: 2rem;
    font-weight: 600;
    // text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  }
}
</style>

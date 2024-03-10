<template>
  <div class="images-grid">
    <div v-for="(image, index) in images" :key="index" class="box">
      <NuxtLink v-if="isUserSaveOrLikePage" :to="'/' + getPhotoDetailLink(image.photo.title, image.photo.file_name)">
        <img :src="`${urlImage}${image.photo.file_name}`" decoding="async" :alt="image.photo.title" loading="lazy" />

        <!-- <span style="top: 15px; right: 20px; position: absolute">
          <v-btn icon="mdi-bookmark-outline" class="icon-btn mr-2" size="35"> </v-btn>
          <v-btn icon="mdi-heart-outline" class="icon-btn" size="35"> </v-btn>
        </span>
        <span style="bottom: 15px; right: 20px; position: absolute">
          <v-btn icon="mdi-download" class="download-btn" size="35"> </v-btn>
        </span> -->
        <span style="bottom: 30px; left: 20px; position: absolute" class="profile-hover-image">
          <NuxtLink :to="'/users/' + image.photo.user.username">
            <img :src="`${urlAvatar}${image.photo.user.avatar_name}`" :alt="image.photo.user.username" loading="lazy" decoding="async" />
          </NuxtLink>
          <NuxtLink :to="'/users/' + image.photo.user.username"> {{ image.photo.user.username }}</NuxtLink>
        </span>
      </NuxtLink>
      <NuxtLink v-if="!isUserSaveOrLikePage" :to="'/' + getPhotoDetailLink(image.title, image.file_name)">
        <!-- <img :src="`https://source.unsplash.com/800x800`" alt="" loading="lazy" decoding="async" /> -->
        <img :src="`${urlImage}${image.file_name}`" alt="" loading="lazy" decoding="async" />

        <!-- <span style="top: 15px; right: 20px; position: absolute">
          <v-btn icon="mdi-bookmark-outline" class="icon-btn mr-2" size="35"> </v-btn>
          <v-btn icon="mdi-heart-outline" class="icon-btn" size="35"> </v-btn>
        </span>
        <span style="bottom: 15px; right: 20px; position: absolute">
          <v-btn icon="mdi-download" class="download-btn" size="35"> </v-btn>
        </span> -->
        <span style="bottom: 30px; left: 20px; position: absolute" class="profile-hover-image">
          <NuxtLink :to="'/users/' + image.user.username">
            <img :src="`${urlAvatar}${image.user.avatar_name}`" alt="" decoding="async" loading="lazy" />
          </NuxtLink>
          <NuxtLink :to="'/users/' + image.user.username"> {{ image.user.username }}</NuxtLink>
        </span>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
// const url = useRuntimeConfig().public.URL_API;
const urlImage = useRuntimeConfig().public.URL_IMAGE;
const urlAvatar = useRuntimeConfig().public.URL_AVATAR;

const props = defineProps(['images']);

const route = useRoute();
const isUserSaveOrLikePage = ref((route.path.startsWith('/users/') && route.path.endsWith('/saves')) || (route.path.startsWith('/users/') && route.path.endsWith('/likes')));

const getPhotoDetailLink = (title: string, file_name: string): string => {
  // Ubah spasi menjadi '-' pada title
  const formattedTitle = title.replace(/\s+/g, '-');
  // Ambil nama file tanpa ekstensi dari bagian akhir file_name
  const defaultImage = file_name.replace(/\.[^.]+$/, '');
  // Bentuk URL sesuai dengan format yang diinginkan
  return `photos/${formattedTitle}--${defaultImage}`;
};

watchEffect(() => {
  isUserSaveOrLikePage.value = (route.path.startsWith('/users/') && route.path.endsWith('/saves')) || (route.path.startsWith('/users/') && route.path.endsWith('/likes'));
});
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';

.images-grid {
  width: 100%;
  // display: grid;
  columns: 3;
  // grid-template-columns: repeat(3, 1fr);
  column-gap: 15px;

  .box {
    position: relative;
    width: 100%;
    height: min-content;
    margin-bottom: 7px;
    cursor: pointer;
    overflow: hidden;
    .load-image {
      background-image: url('/images/image-silver.png');

      // max-width: 100%;
      // max-height: 100%;
      height: 500px;

      border-radius: 10px;
    }
    img {
      max-width: 100%;
      max-height: 100%;
      min-height: 70%;
      border-radius: 10px;
      margin: 0;
      transition: all 0.2s ease;
      background-color: rgb(189, 189, 189);
      object-position: center;
      object-fit: cover;
      display: block;
    }
    &:hover {
      img {
        filter: brightness(70%);
      }
      .icon-btn,
      .download-btn,
      .profile-hover-image {
        opacity: 1;
      }
    }

    .icon-btn {
      // background-color: rgba(0, 0, 0, 0.5);
      opacity: 0;
    }

    .download-btn {
      bottom: 10px;
      opacity: 0;
    }

    .profile-hover-image {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 5px;
      opacity: 0;
      img {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        &:hover {
          filter: brightness(70%);
        }
      }
      a {
        color: variables.$primary-white; /* Adjust the color as needed */
        text-decoration: none;
        font-weight: 600;

        &:hover {
          color: variables.$secondary-white;
        }
      }
    }
  }
}
</style>

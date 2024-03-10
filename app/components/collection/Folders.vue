<template>
  <div class="images-grid">
    <v-card style="background-color: transparent" variant="flat" v-for="(folder, index) in dataFolders" :key="index">
      <div class="boxes">
        <NuxtLink v-if="folder.photos.data" :to="`/folders/${replaceSpaces(`${folder.user.username}--${folder.folder_name}`)}`">
          <div class="image-top">
            <img :src="`${urlImage}/${folder.photos.data[0]?.file_name}`" alt="" loading="lazy" />
          </div>
          <div class="image-bottom">
            <img v-for="(photo, photoIndex) in folder.photos.data.slice(1, 4)" :key="photoIndex" :src="`${urlImage}/${photo.file_name}`" alt="" loading="lazy" />

            <div v-for="i in Math.max(0, 4 - folder.photos.data.length)" :key="`null-${i}`" class="image-null-bottom"></div>
          </div>
        </NuxtLink>
        <NuxtLink v-else :to="`/folders/${replaceSpaces(`${folder.user.username}--${folder.folder_name}`)}`">
          <div class="image-null"></div>
        </NuxtLink>
      </div>
      <v-row>
        <v-col cols="12" md="8">
          <v-card-title class="title-folder px-1">
            <NuxtLink :to="`/folders/${replaceSpaces(`${folder.user.username}--${folder.folder_name}`)}`">
              {{ folder.folder_name }}
            </NuxtLink>
          </v-card-title>
          <v-card-subtitle class="amount-photo-in-folder px-1">
            <p>{{ folder.photos.count }} photos</p>
          </v-card-subtitle>
        </v-col>
        <v-col cols="12" md="4">
          <!-- <v-card-subtitle class="mt-1 created-at">
              <p>2 days ago</p>
            </v-card-subtitle> -->
          <v-card-subtitle class="by-folder mt-3">
            <p>
              By <NuxtLink :to="`/users/${folder.user.username}`"> {{ folder.user.username }} </NuxtLink>
            </p>
          </v-card-subtitle>
        </v-col>
      </v-row>
    </v-card>

    <v-card variant="flat" class="card-add-folder" v-if="isUserFoldersRoute">
      <div class="add-folder" @click="handleClickAddFolder">
        <v-icon class="plus-icon">mdi-plus</v-icon>
      </div>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { defineProps, ref, watchEffect } from 'vue';

const { dataFolders } = defineProps(['dataFolders']);
const urlImage = useRuntimeConfig().public.URL_IMAGE;
const route = useRoute();
const isUserFoldersRoute = ref<boolean>(false);
const emit = defineEmits();

const replaceSpaces = (text: string) => {
  return text.replace(/\s+/g, '-');
};

const handleClickAddFolder = () => {
  emit('add-folder-clicked');
};

watchEffect(() => {
  isUserFoldersRoute.value = /\/users\/[^/]+\/folders/.test(route.path);
});
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';

.images-grid {
  width: 100%;
  // position: relative;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  column-gap: 50px;
  row-gap: 50px;

  .boxes {
    position: relative;
    width: 100%;
    height: min-content;
    margin-bottom: 7px;
    cursor: pointer;
    overflow: hidden;
    border-radius: 10px;

    .image-null {
      background-color: rgb(219, 219, 219);
      width: 100%;
      height: 300px;
      transition: all 0.5s ease;

      &:hover {
        filter: brightness(80%);
        transition: filter 0.5s ease;
      }
    }

    .image-top {
      img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        object-position: center;
      }
    }
    .image-bottom {
      //   background-color: red;
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      position: relative;
      height: 100%;
      gap: 7px;
      align-items: end;
      overflow: hidden;
      .image-null-bottom {
        background-color: rgb(219, 219, 219);
        // width: 100px;
        min-height: 100%;
        transition: all 0.5s ease;
      }
    }
    img {
      max-width: 100%;
      // max-height: 100%;
      height: 280px;
      // min-height: 70%;
      margin: 0;
      transition: all 0.5s ease;
      background-color: silver;
      object-fit: cover;
      background-position: center;
      background-size: cover;
    }

    &:hover {
      img,
      .image-null-bottom {
        filter: brightness(70%);
      }
    }
  }

  .title-folder {
    a {
      color: variables.$primary-black;
      font-weight: 600;
      font-size: 2rem;
      text-decoration: none;
    }
  }
  .amount-photo-in-folder {
    p {
      color: variables.$secondary-black;
      font-weight: 500;
      font-size: 18px;
    }
  }

  .created-at {
    p {
      color: variables.$secondary-black;
      font-weight: 500;
      font-size: 14px;
    }
  }
  .by-folder {
    p {
      color: variables.$secondary-black;
      font-weight: 600;
      font-size: 16px;
    }
    a {
      text-decoration: none;
      color: variables.$secondary-black;
    }
  }

  .card-add-folder {
    background-color: transparent;

    .add-folder {
      width: 100%;
      height: 300px; /* Atur tinggi sesuai kebutuhan */
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f0f0f0; /* Warna latar belakang sesuai kebutuhan */
      border: 2px dashed #ccc; /* Ganti dengan warna garis sesuai kebutuhan */
      cursor: pointer;
      transition: all 0.5s ease;

      &:hover {
        filter: brightness(80%);
        transition: filter 0.3s ease;
      }
    }

    .plus-icon {
      font-size: 2rem; /* Sesuaikan ukuran ikon sesuai kebutuhan */
      color: #1d1d1d; /* Ganti dengan warna ikon sesuai kebutuhan */
    }
  }
}
</style>

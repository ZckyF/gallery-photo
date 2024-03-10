<template>
  <div class="users-grid">
    <v-card v-for="user in dataUsers" :key="user.id" :style="{ backgroundColor: getRandomColor() }" style="padding: 0px" :to="`/users/${user.username}`">
      <div class="image-container">
        <img class="background-image" v-if="user.latest_photo && user.latest_photo.file_name" :src="`${urlImage}${user.latest_photo.file_name}`" alt="" />
        <img class="background-image" v-else src="/images/file-name-null.jpg" alt="" />

        <div class="avatar-container">
          <v-avatar size="120" class="avatar" :image="`${urlAvatar}${user.avatar_name}`"> </v-avatar>
          <div class="username mt-5">{{ user.username }}</div>
        </div>
      </div>
    </v-card>
  </div>
</template>

<script setup lang="ts">
const { dataUsers } = defineProps(['dataUsers']);
const urlImage = useRuntimeConfig().public.URL_IMAGE;
const urlAvatar = useRuntimeConfig().public.URL_AVATAR;
const route = useRoute();

const emit = defineEmits();

// const replaceSpaces = (text: string) => {
//   return text.replace(/\s+/g, '-');
// };

const getRandomColor = () => {
  // Fungsi untuk mendapatkan warna acak dengan kegelapan
  const getRandomDarkColor = () => {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
      const letterIndex = Math.floor(Math.random() * letters.length);
      color += letters[letterIndex];
    }
    return color;
  };

  // Fungsi untuk menghasilkan warna yang lebih gelap
  const darkenColor = (color: any) => {
    // Konversi warna dari heksadesimal ke RGB
    const hexToRgb = (hex: any) => {
      const bigint = parseInt(hex.substring(1), 16);
      const r = (bigint >> 16) & 255;
      const g = (bigint >> 8) & 255;
      const b = bigint & 255;
      return `${r}, ${g}, ${b}`;
    };

    // Darken faktor (dari 0 hingga 1)
    const darkenFactor = 0.5;

    // Konversi warna ke RGB
    const rgbColor = hexToRgb(color);

    // Darken warna dengan mengurangi nilai RGB
    const darkenedColor = rgbColor
      .split(', ')
      .map((channel) => Math.floor(parseInt(channel) * (1 - darkenFactor)))
      .join(', ');

    return `rgb(${darkenedColor})`;
  };

  // Menghasilkan warna acak dan di-darken
  return darkenColor(getRandomDarkColor());
};
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';

.users-grid {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  column-gap: 30px;
  row-gap: 30px;

  .v-card {
    border-radius: 30px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 16px;
    height: 100%;
    cursor: pointer;
    transition: filter 0.3s ease;

    &:hover {
      filter: brightness(70%);
    }

    .image-container {
      width: 100%;
      height: 55%;
      position: relative;
    }
    .background-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      background-color: rgb(114, 114, 114);
    }

    .file-name-null {
      background-color: red;
      height: 200px;
    }

    .avatar-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin-top: -70px;
      z-index: 1;
    }

    .avatar {
      border: rgb(230, 230, 230) 3px solid;
    }

    .username {
      font-weight: 600;
      font-size: 1.4rem;
      color: variables.$primary-white;
    }
  }
}
</style>

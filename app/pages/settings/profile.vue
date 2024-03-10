<template>
  <v-container>
    <LayoutElementsSidebarSettings />
    <v-form @submit.prevent="save">
      <label for="file-input" class="profile">
        <div class="image-avatar" @mouseover="handleMouseOver" @mouseleave="handleMouseLeave">
          <img :src="profileUser.selectedImage || urlAvatar + profileUser.avatar_name" alt="Avatar" />
          <span class="icon-image">
            <v-icon size="50" color="white">mdi-image-edit</v-icon>
          </span>
        </div>
      </label>
      <input id="file-input" type="file" class="file-img" accept="image/png, image/gif, image/jpeg" @change="handleFileUpload" />
      <v-row class="mx-10 mt-10">
        <v-col cols="12" md="4">
          <v-text-field label="Username" placeholder="Enter your username " variant="outlined" prepend-inner-icon="mdi-account" v-model="profileUser.username" :disabled="loadingSave"></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field label="Full Name" placeholder="Enter your full name " variant="outlined" prepend-inner-icon="mdi-account-edit" v-model="profileUser.full_name" :disabled="loadingSave"></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field label="Email" placeholder="Enter your email " variant="outlined" prepend-inner-icon="mdi-email" v-model="profileUser.email" :disabled="loadingSave"></v-text-field>
        </v-col>
        <v-col cols="12" md="12">
          <v-textarea label="Address" placeholder="Enter your address " variant="outlined" prepend-inner-icon="mdi-map-marker" v-model="profileUser.address" :disabled="loadingSave"></v-textarea>
        </v-col>
        <v-col cols="12" md="12">
          <v-textarea label="Bio" placeholder="Enter your bio " variant="outlined" prepend-inner-icon="mdi-map-marker" v-model="profileUser.bio" :disabled="loadingSave"></v-textarea>
        </v-col>
        <v-col cols="12" md="12" class="text-end">
          <v-btn class="gradient-button text-none px-10" type="submit" rounded="lg" :loading="loadingSave">Save</v-btn>
        </v-col>
      </v-row>
    </v-form>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
  </v-container>
</template>

<script lang="ts" setup>
import axios from 'axios';
import { useUserStore } from '~/stores/store';

const store = useUserStore();

let profileUser = ref<object>({
  username: '',
  full_name: '',
  email: '',
  address: '',
  bio: '',
});

const url = useRuntimeConfig().public.URL_API;
const urlAvatar = useRuntimeConfig().public.URL_AVATAR;
let loadingSave = ref<boolean>(false);
let snackbar = ref<boolean>(false);
let snackMes = ref({ text: '', prependIcon: '' });
const cookie = useCookie('auth_token');
let uniqueKey = ref<number>(0);

const showSnackbar = (message: string, type: string = 'success') => {
  snackMes.value = { text: message, prependIcon: type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' };
  snackbar.value = true;
  setTimeout(() => {
    snackbar.value = false;
  }, 5000);
};

const handleFileUpload = (event: any) => {
  if (event.target.files.length > 0) {
    const file = event.target.files[0];
    if (file) {
      // Menggunakan URL.createObjectURL untuk membuat URL objek dari file
      profileUser.value.selectedImage = URL.createObjectURL(file);
      profileUser.value.imageSource = file;

      // Menambahkan uniqueKey untuk memicu perubahan pada elemen Vue yang bersifat reaktif
      uniqueKey.value += 1;
    }
  }
};

const handleMouseOver = () => {
  profileUser.isHovered = true;
};

const handleMouseLeave = () => {
  profileUser.isHovered = false;
};

const handleSnackbarClose = () => {
  snackbar.value = false;
};

const save = async () => {
  try {
    loadingSave.value = true;

    const formData = new FormData();
    formData.append('username', profileUser.value.username);
    formData.append('full_name', profileUser.value.full_name);
    formData.append('email', profileUser.value.email);
    formData.append('address', profileUser.value.address);
    formData.append('bio', profileUser.value.bio);

    // Jika ada gambar yang dipilih
    if (profileUser.value.imageSource instanceof File) {
      formData.append('file', profileUser.value.imageSource);
    }

    const response = await axios.post(`${url}/users/${store.getUser.id}?_method=PATCH`, formData, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${cookie.value}`,
        'Content-Type': 'multipart/form-data',
      },
    });

    console.log(response.data.data);
    store.setUser(response.data.data);

    window.location.reload();

    console.log(response);
    showSnackbar(response.data.message, 'success');
  } catch (error) {
    console.log('Update failed:', error);
    showSnackbar(error.response.data.message, 'error');
  } finally {
    loadingSave.value = false;
  }
};

try {
  const { data: userDetail } = await useFetch(`${url}/user-detail/${store.getUser.username}`);

  profileUser.value.username = userDetail.value.data.username;
  profileUser.value.full_name = userDetail.value.data.full_name;
  profileUser.value.address = userDetail.value.data.address;
  profileUser.value.email = userDetail.value.data.email;
  profileUser.value.bio = userDetail.value.data.bio;
  profileUser.value.avatar_name = userDetail.value.data.avatar_name;
  // profileUser.value = userDetail.value.data;
} catch (error) {
  console.error('error', error);
} finally {
}
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';

.profile {
  width: 50%;
}

.file-img {
  display: none;
}

.image-avatar {
  position: relative;
  overflow: hidden;
  transition: background-color 0.3s ease;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;

  img {
    object-fit: contain;
    border-radius: 10px;
    background-position: center;
    max-height: 300px; // Tambahkan properti max-height
  }

  .icon-image {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    background-color: rgba(0, 0, 0, 0.7);
    width: 100%;
    height: 100%;
    top: 100%;
    transition: top 0.3s ease, opacity 0.3s ease;
    opacity: 0;
  }

  &:hover .icon-image {
    top: 0;
    opacity: 1;
  }
}
</style>

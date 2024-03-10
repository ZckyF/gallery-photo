<template>
  <div class="page-user">
    <LayoutElementsHeaderUser />
    <div class="link mt-10 ml-16">
      <NuxtLink :to="`/users/${username}`"> Photos </NuxtLink>
      <NuxtLink to="folders">
        Folders <span class="ml-1" style="color: rgb(48, 48, 48); font-size: 15px">{{ dataFolders.length }}</span></NuxtLink
      >
      <NuxtLink :to="`/users/${username}/saves`"> Saves</NuxtLink>
      <NuxtLink :to="`/users/${username}/likes`"> Likes</NuxtLink>
    </div>
    <div class="px-10 mt-4">
      <v-row v-if="skeletonLoader">
        <v-col v-for="index in dataFolders.length" :key="index" cols="12" sm="4" md="4">
          <v-skeleton-loader style="background-color: transparent" max-width="700" type="image,image,image,sentences"></v-skeleton-loader>
        </v-col>
      </v-row>
      <CollectionFolders v-else :dataFolders="dataFolders" @add-folder-clicked="handleAddFolderClicked" />
    </div>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />

    <v-dialog v-model="dialog" width="500">
      <v-card>
        <v-card-title>
          <span class="text-h5">Add Folder</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="12">
                <v-text-field :disabled="loading" v-model="formDataFolder.folder_name" prepend-inner-icon="mdi-folder-image" variant="outlined" label="Name Folder" required></v-text-field>
              </v-col>
              <v-col cols="12" md="12">
                <v-textarea :disabled="loading" v-model="formDataFolder.description" prepend-inner-icon="mdi-text-long" variant="outlined" label="Description" type="text"></v-textarea>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="text-none" :loading="loading" @click="saveFolder" variant="text"> Save </v-btn>
          <v-btn class="text-none" :disabled="loading" @click="dialog = false" variant="text"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
const { username } = useRoute().params;

let dataFolders = ref<[]>([]);

let snackMes = ref<object>({ text: '', prependIcon: '' });
let snackbar = ref<boolean>(false);
let skeletonLoader = ref<boolean>(true);
let dialog = ref<boolean>(false);
const cookie = useCookie('auth_token');
let loading = ref<boolean>(false);

let formDataFolder = ref<{}>({
  folder_name: '',
  description: '',
});

const url = useRuntimeConfig().public.URL_API;

const handleSnackbarClose = () => {
  snackbar.value = false;
};
const showSnackbar = (message: string, type: string = 'success') => {
  snackMes.value = { text: message, prependIcon: type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' };
  snackbar.value = true;
  setTimeout(() => {
    snackbar.value = false;
  }, 5000);
};

const handleAddFolderClicked = () => {
  // Lakukan apa pun yang diperlukan saat folder ditambahkan
  // Aktifkan v-model di sini jika diperlukan
  dialog.value = true;
};

const saveFolder = async () => {
  try {
    loading.value = true;
    const response = await axios.post(`${url}/folders`, formDataFolder.value, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });
    console.log(response.data.data.message);
    // Refresh dataFolders setelah menambahkan folder
    const { data: folders } = await useFetch(`${url}/user/${username}/folders`);
    dataFolders.value = folders.value.data;
    dialog.value = false;
  } catch (error) {
    showSnackbar(error.response.data.message);
  } finally {
    loading.value = false;
  }
};

try {
  const { data: folders } = await useFetch(`${url}/user/${username}/folders`);
  dataFolders.value = folders.value.data;
  console.log(folders.value.message);
} catch (error) {
  console.log(error);
  // showSnackbar(error, 'error');
} finally {
  setTimeout(() => {
    skeletonLoader.value = false;
  }, 500);
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

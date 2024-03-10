<template>
  <div class="page-detail-folder mx-16">
    <v-row class="mt-16 mb-4">
      <v-col cols="12" md="8">
        <header class="title-description-folder">
          <v-skeleton-loader style="background-color: transparent" v-if="skeletonLoader" type="subtitle,sentences"></v-skeleton-loader>
          <div v-else>
            <h1>{{ dataDetailFolder.folder_name }}</h1>
            <p>
              {{ dataDetailFolder.description }}
            </p>
          </div>
        </header>
        <v-skeleton-loader style="background-color: transparent" v-if="skeletonLoader" class="mt-8" type="list-item-avatar" width="400"></v-skeleton-loader>
        <div v-else class="user-profile mt-8">
          <NuxtLink to="/" class="image-profile">
            <img :src="`${urlAvatar}${dataDetailFolder.user.avatar_name}`" :to="`/users/${dataDetailFolder.user.username}`" loading="lazy" alt="" />
          </NuxtLink>
          <NuxtLink :to="`/users/${dataDetailFolder.user.username}`">{{ dataDetailFolder.user.username }}</NuxtLink>
        </div>
        <div class="amount-image mt-5">
          <v-skeleton-loader style="background-color: transparent" v-if="skeletonLoader" class="mt-8" type="subtitle" width="100"></v-skeleton-loader>
          <p v-else>{{ dataDetailFolder.photos.count }} photos</p>
        </div>
      </v-col>
      <v-col cols="12" md="4" class="created-at">
        <v-menu transition="slide-y-transition">
          <template v-slot:activator="{ props }">
            <v-btn v-if="store.getUser.id == dataDetailFolder.user.id" class="mb-16 text-none" variant="outlined" size="small" icon="mdi-dots-horizontal" rounded="lg" v-bind="props"></v-btn>
          </template>
          <v-list>
            <v-list-item>
              <v-btn prepend-icon="mdi-pencil-outline" rounded variant="text" class="text-none" @click="dialogEdit = true">Edit</v-btn>
            </v-list-item>

            <v-list-item>
              <v-btn prepend-icon="mdi-delete-outline" rounded variant="text" class="text-none" @click="dialogDelete = true">Delete</v-btn>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-skeleton-loader style="background-color: transparent" v-if="skeletonLoader" class="mt-8" type="subtitle" width="100"></v-skeleton-loader>
        <p v-else>{{ convertDate(dataDetailFolder.created_at) }}</p>
      </v-col>
    </v-row>
    <v-row v-if="skeletonLoader">
      <v-col v-for="index in dataDetailFolder.photos.data.length" :key="index" cols="12" sm="4" md="4">
        <v-skeleton-loader style="background-color: transparent" max-width="700" class="rounded-lg" type="image,image,image"></v-skeleton-loader>
      </v-col>
    </v-row>
    <div v-else>
      <CollectionImages v-if="dataDetailFolder.photos.data" :images="dataDetailFolder.photos.data" />
    </div>
    <v-dialog v-model="dialogDelete" transition="dialog-top-transition" max-width="400">
      <v-card>
        <v-card-text class="pa-8 card-text-dialog">
          <span class="mx-auto icon-alert">
            <v-icon size="60">mdi-delete</v-icon>
          </span>

          <div class="text-center text-delete mt-5">
            <h3>Do you want to delete this folder,If you delete this folder, the photos in this folder will be deleted too ?</h3>
          </div>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn variant="text" class="text-none" @click="deleteSubmit" :loading="loading">Yes</v-btn>
          <v-btn variant="text" class="text-none" @click="dialogDelete = false" :disabled="loading">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogEdit" width="500">
      <v-card>
        <v-card-title>
          <span class="text-h5">Edit Folder</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" sm="6" md="12">
                <v-text-field :disabled="loading" v-model="formDataFolder.folder_name" prepend-inner-icon="mdi-folder-image" variant="outlined" label="Name Folder" required></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="12">
                <v-textarea :disabled="loading" v-model="formDataFolder.description" prepend-inner-icon="mdi-text-long" variant="outlined" label="Description" type="text"></v-textarea>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="#26c6da" class="text-none" :loading="loading" @click="editSubmit" variant="text"> Edit </v-btn>
          <v-btn color="#29b6f6" class="text-none" :disabled="loading" @click="dialogEdit = false" variant="text"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { convertDate } from '~/helpers/convert.ts';
import { useUserStore } from '~/stores/store';

const { usernameAndNameFolder } = useRoute().params;
const store = useUserStore();

const cookie = useCookie('auth_token');
const router = useRouter();
const urlImage = useRuntimeConfig().public.URL_IMAGE;
const urlAvatar = useRuntimeConfig().public.URL_AVATAR;
const url = useRuntimeConfig().public.URL_API;
let dataDetailFolder = ref<object>({});
let skeletonLoader = ref<boolean>(true);
let dialogDelete = ref<boolean>(false);
let dialogEdit = ref<boolean>(false);
let loading = ref<boolean>(false);
let snackMes = ref<object>({ text: '', prependIcon: '' });
let snackbar = ref<boolean>(false);
let formDataFolder = ref<object>({
  folder_name: '',
  description: '',
});

const editSubmit = async () => {
  try {
    loading.value = true;
    const response = await axios.patch(`${url}/folders/${dataDetailFolder.value.id}`, formDataFolder.value, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });
    const responseData = response.data.data;
    router.push(`/folders/${replaceSpaces(responseData.user.username + '--' + responseData.folder_name)}`);
    dialogEdit.value = false;
  } catch (error) {
    showSnackbar(error.response.data.message);
  } finally {
    loading.value = false;
  }
};
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
const deleteSubmit = async () => {
  try {
    loading.value = true;
    const response = await axios.delete(`${url}/folders/${dataDetailFolder.value.id}`, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });
    console.log(response.data.data.message);
    const username = dataDetailFolder.value.user.username;
    router.push(`/users/${username}/folders`);
  } catch (error) {
    showSnackbar(error.response.data.message);
  } finally {
    loading.value = false;
    dialog.value = false;
  }
};
const replaceSpaces = (text: string) => {
  return text.replace(/\s+/g, '-');
};
try {
  const { data: detailFolder } = await useFetch(`${url}/detail-folder/${usernameAndNameFolder}`);
  dataDetailFolder.value = detailFolder.value.data;
  formDataFolder.value.folder_name = dataDetailFolder.value.folder_name;
  formDataFolder.value.description = dataDetailFolder.value.description;
  console.log(detailFolder.value.message);
} catch (error) {
  console.log(error);
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

.page-detail-folder {
  .title-description-folder {
    h1 {
      color: variables.$primary-black;
      font-weight: 600;
      font-size: 3rem;
    }
    p {
      color: variables.$secondary-black;
      font-weight: 400;
      font-size: 15px;
    }
  }

  .user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    .image-profile {
      img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transition: all 0.2s ease;
        &:hover {
          filter: brightness(70%);
        }
      }
    }
    a {
      text-decoration: none;
      color: variables.$primary-black;
      font-weight: 500;
      &:hover {
        color: variables.$secondary-black;
      }
    }
  }

  .amount-image {
    p {
      color: variables.$primary-black;
      font-weight: 500;
      size: 2rem;
    }
  }

  .created-at {
    display: grid;
    justify-content: end;
    align-items: end;
  }
}
.card-text-dialog {
  .icon-alert {
    display: flex;
    justify-content: center;
    color: variables.$primary-black;
    font-weight: 500;
  }

  .text-delete {
    h3 {
      color: variables.$primary-black;
      font-weight: 500;
    }
  }
}
</style>

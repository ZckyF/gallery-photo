<template>
  <div class="image-detail-page px-10 pt-5">
    <header class="header-detail">
      <v-row>
        <v-col cols="12" md="6" v-if="skeletonLoader" class="px-0">
          <v-skeleton-loader width="300" style="background-color: transparent" type="list-item-avatar"></v-skeleton-loader>
        </v-col>

        <v-col v-else class="profile">
          <NuxtLink :to="`/users/${detail.user.username}`" class="image-profile">
            <img :src="`${urlAvatar}${detail.user.avatar_name}`" alt="" />
          </NuxtLink>
          <NuxtLink :to="`/users/${detail.user.username}`">{{ detail.user.username }}</NuxtLink>
        </v-col>
        <v-col class="text-end button-header-detail">
          <v-btn v-if="store.getUser.id != detail.user.id" prepend-icon="mdi-flag-outline" variant="outlined" class="text-none" @click="dialogReport = true">Report</v-btn>
          <v-btn v-if="skeletonLoader" variant="outlined">
            <v-skeleton-loader style="background-color: transparent" type="text"></v-skeleton-loader>
          </v-btn>

          <v-btn :loading="loadingSave" v-if="!skeletonLoader" :prepend-icon="isSaved ? 'mdi-bookmark' : 'mdi-bookmark-outline'" variant="outlined" class="text-none" @click="toggleSave">Save</v-btn>
          <v-btn v-if="skeletonLoader" variant="outlined">
            <v-skeleton-loader style="background-color: transparent" type="text"></v-skeleton-loader>
          </v-btn>
          <v-btn :loading="loadingLike" v-if="!skeletonLoader" :prepend-icon="isLiked ? 'mdi-heart' : 'mdi-heart-outline'" variant="outlined" class="text-none" @click="toggleLike" :color="isLiked ? 'red' : ''">
            <span>{{ likes.length }}</span>
          </v-btn>

          <v-menu transition="slide-y-transition">
            <template v-slot:activator="{ props }">
              <v-btn v-if="store.getUser.id == detail.user.id" class="mb-16 text-none" variant="outlined" size="small" icon="mdi-dots-horizontal" rounded="lg" v-bind="props"></v-btn>
            </template>
            <v-list>
              <v-list-item>
                <v-btn
                  prepend-icon="mdi-pencil-outline"
                  rounded
                  variant="text"
                  class="text-none"
                  @click="
                    dialogEdit = true;
                    formEditPhoto.title = detail.title;
                    formEditPhoto.description = detail.description;
                    formEditPhoto.folder = folders.some((folder) => folder.folder_name == detail.folder.folder_name) ? detail.folder.folder_name : 'none';
                  "
                  >Edit</v-btn
                >
              </v-list-item>

              <v-list-item>
                <v-btn prepend-icon="mdi-delete-outline" rounded variant="text" class="text-none" @click="dialogDelete = true">Delete</v-btn>
              </v-list-item>
            </v-list>
          </v-menu>
          <!-- <v-btn @click="toogleDownload" variant="outlined" class="gradient-button text-none" rounded style="border-color: white" :prepend-icon="cookie ? 'mdi-lock-open' : 'mdi-lock'" :loading="loadingDownload">Download</v-btn> -->
        </v-col>
      </v-row>
    </header>
    <div class="content-detail mt-5">
      <div class="image-content pa-5">
        <v-skeleton-loader v-if="skeletonLoader" style="background-color: transparent" width="800" type="image,image,image"></v-skeleton-loader>
        <img v-else :src="`${urlImage}${detail.file_name}`" alt="" loading="lazy" />
      </div>
      <div class="image-caption n mx-5 mt-5">
        <v-row>
          <v-col cols="12" md="8" class="title-description">
            <div v-if="skeletonLoader">
              <v-skeleton-loader style="background-color: transparent" width="800" type="text"></v-skeleton-loader>
              <v-skeleton-loader style="background-color: transparent" width="800" type="sentences"></v-skeleton-loader>
            </div>
            <div v-else>
              <h1>{{ detail.title }}</h1>
              <p>{{ detail.description }}</p>
            </div>
          </v-col>
          <v-col cols="12" md="4" class="text-end publish-image">
            <v-skeleton-loader v-if="skeletonLoader" class="ml-auto" style="background-color: transparent" width="100" type="sentences"></v-skeleton-loader>
            <div v-else>
              <h1>Published</h1>
              <p>{{ convertDate(detail.created_at) }}</p>
            </div>
          </v-col>
        </v-row>
      </div>

      <CollectionComments :detail="detail" :idPhoto="detail.id" />

      <v-divider :thickness="2" class="mt-10"></v-divider>

      <!-- <div class="similiar-photo">
        <header class="similiar-photo-title mt-3 text-center">
          <h1>Similiar Photo</h1>
        </header>
      </div> -->
    </div>
    <v-dialog v-model="dialogDelete" transition="dialog-top-transition" max-width="400">
      <v-card>
        <v-card-text class="pa-8 card-text-dialog">
          <span class="mx-auto icon-alert">
            <v-icon size="60">mdi-delete</v-icon>
          </span>

          <div class="text-center text-delete mt-5">
            <h3>Do you want to delete this image ?</h3>
          </div>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn variant="text" class="text-none" @click="deleteSubmit" :loading="loading">Yes</v-btn>
          <v-btn variant="text" class="text-none" @click="dialogDelete = false" :disabled="loading">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogReport" width="500">
      <v-card>
        <v-card-title>
          <span class="text-h5">Send Report</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="12">
                <v-text-field :disabled="loading" v-model="formDataReport.title" prepend-inner-icon="mdi-pencil" variant="outlined" label="Title Report" required></v-text-field>
              </v-col>
              <v-col cols="12" md="12">
                <v-textarea :disabled="loading" v-model="formDataReport.description" prepend-inner-icon="mdi-text-long" variant="outlined" label="Description" type="text"></v-textarea>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="text-none" :loading="loadingReport" @click="sendReport" variant="text"> Send </v-btn>
          <v-btn class="text-none" :disabled="loadingReport" @click="dialogReport = false" variant="text"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogEdit" width="500">
      <v-card>
        <v-card-title>
          <span class="text-h5">Edit Photo</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="12">
                <v-text-field :disabled="loading" v-model="formEditPhoto.title" prepend-inner-icon="mdi-folder-image" variant="outlined" label="Title" required></v-text-field>
              </v-col>
              <v-col cols="12" md="12">
                <v-textarea :disabled="loading" v-model="formEditPhoto.description" prepend-inner-icon="mdi-text-long" variant="outlined" label="Description" type="text"></v-textarea>
              </v-col>
              <v-col cols="12" md="12">
                <v-select v-model="formEditPhoto.folder" variant="outlined" prepend-inner-icon="mdi mdi-folder" :items="[...folders.map((folder) => folder.folder_name), 'none']" label="Select Folder" :disabled="loading"></v-select>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="text-none" :loading="loading" variant="text" @click="saveEdit"> Save </v-btn>
          <v-btn
            class="text-none"
            :disabled="loading"
            @click="
              dialogEdit = false;
              formEditPhoto = {};
            "
            variant="text"
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref } from 'vue';
import { convertDate } from '~/helpers/convert.ts';
import { useUserStore } from '~/stores/store';

const store = useUserStore();

useHead({
  title: 'Detail Photo',
});

const { titleAndFileName } = useRoute().params;

let detail = ref<object>({});
let likes = ref<[]>([]);
let saves = ref<[]>([]);

let formDataReport = ref<{}>({
  title: '',
  description: '',
  photo_id: '',
});

let formEditPhoto = ref<object>({
  title: '',
  description: '',
  folder: null,
});
let folders = ref<[object]>([
  {
    folder_name: null,
  },
]);

const url = useRuntimeConfig().public.URL_API;
const urlImage = useRuntimeConfig().public.URL_IMAGE;
const urlAvatar = useRuntimeConfig().public.URL_AVATAR;
const cookie = useCookie('auth_token');
const router = useRouter();
let dialogDelete = ref<boolean>(false);
let dialogReport = ref<boolean>(false);
let dialogEdit = ref<boolean>(false);
let loading = ref<boolean>(false);
let loadingLike = ref<boolean>(false);
let loadingSave = ref<boolean>(false);
let loadingReport = ref<boolean>(false);
let loadingDownload = ref<boolean>(false);
let skeletonLoader = ref<boolean>(false);
let snackMes = ref<object>({ text: '', prependIcon: '' });
let snackbar = ref<boolean>(false);

let isLiked = computed(() => {
  return likes.value.some((like) => like.user.id === store.getUser.id);
});
let isSaved = computed(() => {
  return saves.value.some((save) => save.user.id === store.getUser.id);
});

const handleEditPhoto = () => {};
const showSnackbar = (message: string, type: string = 'success') => {
  snackMes.value = { text: message, prependIcon: type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' };
  snackbar.value = true;
  setTimeout(() => {
    snackbar.value = false;
  }, 5000);
};
const handleSnackbarClose = () => {
  snackbar.value = false;
};

const deleteSubmit = async () => {
  try {
    loading.value = true;
    const response = await axios.delete(`${url}/photos/${detail.value.id}`, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });
    console.log(response.data.data.message);
    const username = detail.value.user.username;
    router.push(`/users/${username}`);
    dialogDelete.value = false;
  } catch (error) {
    showSnackbar(error.response.data.message);
  } finally {
    loading.value = false;
  }
};

const toggleLike = async () => {
  if (!cookie.value) {
    return router.push({ name: 'login' });
  }
  if (isLiked.value) await removeLike();
  else await addLike();
};
const toggleSave = async () => {
  if (!cookie.value) {
    return router.push({ name: 'login' });
  }
  if (isSaved.value) await removeSave();
  else await addSave();
};
const toogleDownload = async () => {
  if (!cookie.value) {
    return router.push({ name: 'login' });
  } else await downloadFile();
};
const addLike = async () => {
  try {
    loadingLike.value = true;
    // Kirim permintaan untuk menambahkan like
    const response = await axios.post(
      `${url}/like-photos`,
      {
        photo_id: detail.value.id,
      },
      {
        headers: {
          Authorization: `Bearer ${cookie.value}`,
        },
      }
    );

    // Tampilkan pesan sukses atau lakukan tindakan lain yang diperlukan
    console.log(response.data.message);

    // Perbarui data likes setelah like berhasil ditambahkan
    const { data: updatedLikes } = await useFetch(`${url}/photo/${detail.value.id}/likes`);
    likes.value = updatedLikes.value.data;
  } catch (error) {
    // Tampilkan pesan kesalahan atau lakukan tindakan lain yang diperlukan
    console.error('Error adding like:', error);
  } finally {
    loadingLike.value = false;
  }
};
const addSave = async () => {
  try {
    loadingSave.value = true;
    // Kirim permintaan untuk menambahkan like
    const response = await axios.post(
      `${url}/saves`,
      {
        photo_id: detail.value.id,
      },
      {
        headers: {
          Authorization: `Bearer ${cookie.value}`,
        },
      }
    );

    // Tampilkan pesan sukses atau lakukan tindakan lain yang diperlukan
    console.log(response);

    // Perbarui data likes setelah like berhasil ditambahkan
    const { data: updatedSave } = await useFetch(`${url}/photo/${detail.value.id}/saves`);
    saves.value = updatedSave.value.data;
  } catch (error) {
    // Tampilkan pesan kesalahan atau lakukan tindakan lain yang diperlukan
    console.error('Error adding like:', error);
  } finally {
    loadingSave.value = false;
  }
};
const removeLike = async () => {
  try {
    loadingLike.value = true;
    // Dapatkan id like yang dimiliki oleh user pada foto ini
    const likeId = likes.value.find((like) => like.user.id === store.getUser.id)?.id;

    if (likeId) {
      // Kirim permintaan untuk menghapus like berdasarkan id like
      const response = await axios.delete(`${url}/like-photos/${likeId}`, {
        headers: {
          Authorization: `Bearer ${cookie.value}`,
        },
      });

      // Tampilkan pesan sukses atau lakukan tindakan lain yang diperlukan
      console.log(response.data.message);

      // Perbarui data likes setelah like berhasil dihapus
      const { data: updatedLikes } = await useFetch(`${url}/photo/${detail.value.id}/likes`);
      likes.value = updatedLikes.value.data;
    }
  } catch (error) {
    // Tampilkan pesan kesalahan atau lakukan tindakan lain yang diperlukan
    console.error('Error removing like:', error);
  } finally {
    loadingLike.value = false;
  }
};
const removeSave = async () => {
  try {
    loadingSave.value = true;
    // Dapatkan id like yang dimiliki oleh user pada foto ini
    const saveId = saves.value.find((save) => save.user.id === store.getUser.id)?.id;

    if (saveId) {
      // Kirim permintaan untuk menghapus like berdasarkan id like
      const response = await axios.delete(`${url}/saves/${saveId}`, {
        headers: {
          Authorization: `Bearer ${cookie.value}`,
        },
      });

      // Tampilkan pesan sukses atau lakukan tindakan lain yang diperlukan
      console.log(response.data.message);

      // Perbarui data likes setelah like berhasil dihapus
      const { data: updatedSave } = await useFetch(`${url}/photo/${detail.value.id}/saves`);
      saves.value = updatedSave.value.data;
    }
  } catch (error) {
    // Tampilkan pesan kesalahan atau lakukan tindakan lain yang diperlukan
    console.error('Error removing like:', error);
  } finally {
    loadingSave.value = false;
  }
};

const sendReport = async () => {
  try {
    formDataReport.value.photo_id = detail.value.id;
    loadingReport.value = true;
    // Kirim permintaan untuk menambahkan like
    const response = await axios.post(`${url}/report-photos`, formDataReport.value, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });
    showSnackbar(response.data.message);
    // Tampilkan pesan sukses atau lakukan tindakan lain yang diperlukan
    console.log(response.data.message);
    dialogReport.value = false;
  } catch (error) {
    // Tampilkan pesan kesalahan atau lakukan tindakan lain yang diperlukan
    console.error('Error adding save:', error);
    showSnackbar(error.response.data.message);
  } finally {
    loadingReport.value = false;
  }
};

const saveEdit = async () => {
  try {
    loading.value = true;
    // Cari ID folder berdasarkan nama folder yang dipilih
    if (formEditPhoto.value.folder == 'none') {
      formEditPhoto.value.folder_id = '';
    } else {
      const selectedFolderName = formEditPhoto.value.folder;
      let matchingFolder = folders.value.find((folder) => folder.folder_name === selectedFolderName);

      formEditPhoto.value.folder_id = matchingFolder.id;
    }

    // Kirim data menggunakan Axios
    const response = await axios.patch(`${url}/photos/${detail.value.id}`, formEditPhoto.value, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${cookie.value}`,
      },
    });

    console.log('Response:', response.data);

    showSnackbar(response.data.message, 'success');

    formEditPhoto.value = {};
    dialogEdit.value = false;
    router.push(`/folders/${replaceSpaces(response.data.data.title + '--' + response.data.data.file_name)}`);
  } catch (error) {
    console.error('Error:', error);
    // showSnackbar(error.response.data.message, 'error');
    // Gagal, handle error di sini
  } finally {
    loading.value = false;
  }
};
const replaceSpaces = (text: string) => {
  return text.replace(/\s+/g, '-');
};

const downloadFile = async () => {
  try {
    loadingDownload.value = true;
    const response = await axios.get(`${urlImage}${detail.value.file_name}`, {
      responseType: 'blob',
    });
    let fileUrl = window.URL.createObjectURL(new Blob([response.data]));
    let fileLink = document.createElement('a');
    fileLink.href = fileUrl;

    fileLink.setAttribute('download', detail.value.file_name);
    document.body.appendChild(fileLink);

    fileLink.click();

    // // Membuat URL untuk blob response
    // const fileURL = window.URL.createObjectURL(new Blob([response.data]));

    // // Membuat anchor element untuk file URL
    // const link = document.createElement('a');
    // link.href = fileURL;

    // // Mendapatkan nama file dari header Content-Disposition
    // const contentDisposition = response.headers['content-disposition'];

    // const fileNameMatch = contentDisposition && contentDisposition.match(/filename="(.+)"$/);

    // const fileName = fileNameMatch ? fileNameMatch[1] : 'downloaded_file';

    // link.download = fileName;

    // // Menambahkan anchor element ke dokumen
    // document.body.appendChild(link);

    // // Mengklik link untuk memulai unduhan
    // link.click();

    // // Menghapus anchor element dari dokumen
    // document.body.removeChild(link);

    // // Melepaskan objek URL
    // window.URL.revokeObjectURL(fileURL);
  } catch (error) {
    console.error('Error downloading file:', error);
  } finally {
    loadingDownload.value = false;
  }
};

try {
  skeletonLoader.value = true;

  const { data: detailPhoto } = await useFetch(`${url}/detail-photo/${titleAndFileName}`);
  detail.value = detailPhoto.value.data;
  console.log(detail.value, 'halo');

  // Now that detail.description is available, call paramSimilarPhoto
} catch (error) {
  console.error('Error fetching images:', error);
} finally {
  setTimeout(() => {
    skeletonLoader.value = false;
  }, 500);
}

try {
  skeletonLoader.value = true;

  const { data: likePhotos } = await useFetch(`${url}/photo/${detail.value.id}/likes`);
  likes.value = likePhotos.value.data;

  // Now that detail.description is available, call paramSimilarPhoto
} catch (error) {
  console.error('Error fetching images:', error);
} finally {
  setTimeout(() => {
    skeletonLoader.value = false;
  }, 500);
}

try {
  skeletonLoader.value = true;

  const { data: savePhotos } = await useFetch(`${url}/photo/${detail.value.id}/saves`);
  console.log(savePhotos.value);
  saves.value = savePhotos.value.data;

  // Now that detail.description is available, call paramSimilarPhoto
} catch (error) {
  console.error('Error fetching images:', error);
} finally {
  setTimeout(() => {
    skeletonLoader.value = false;
  }, 500);
}

try {
  const { data: data } = await useFetch(`${url}/folders`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${cookie.value}`,
    },
  });
  folders.value = data.value.data;
  console.log(folders.value.includes(detail.value.folder.id));
  // console.log(folders.value, detail.value.folder);
} catch (error) {
  console.error('Error fetching images:', error);
} finally {
  // skeletonLoaders.value = false;
}

// const imageUrl = http://127.0.0.1:5000/${photo.fileName}; // Assuming this.fileName holds the image filename
//     axios({
//         url: imageUrl,
//         method: 'GET',
//         responseType: 'blob'
//     }).then((response) => {
//         let fileUrl = window.URL.createObjectURL(new Blob([response.data]))
//         let fileLink = document.createElement('a');
//         fileLink.href = fileUrl;

//         fileLink.setAttribute('download', photo.fileName);
//         document.body.appendChild(fileLink);

//         fileLink.click()
//     })

// watchEffect(() => {
//   // Callback ini akan dieksekusi ketika ada perubahan pada data yang dipantau
//   isLiked.value = likes.value.some((like) => like.user.id === store.getUser.id);
//   isSaved.value = saves.value.some((save) => save.user.id === store.getUser.id);
// });

// paramSimilarPhoto(titleAndFileName, detail.value.description).then((data) => console.log(data));
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';

.created-at {
  color: variables.$secondary-black;
  font-size: 14px;
  text-align: center;
}
.image-detail-page {
  .header-detail {
    .profile {
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
    .button-header-detail {
      display: flex;
      justify-content: end;
      gap: 20px;
      button {
        letter-spacing: normal;
        border-color: variables.$border-color-outlined;
        &:hover {
          border-color: variables.$hover-border-color-outlined;
        }
      }
    }
  }

  .content-detail {
    .image-content {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      background-color: variables.$primary-background-color;
      height: 700px;

      img {
        max-width: 100%;
        max-height: 100%;
      }
    }
    .image-caption {
      .title-description {
        h1 {
          color: variables.$primary-black;
          font-weight: 600;
        }
        p {
          color: variables.$secondary-black;
          font-size: 16px;
        }
      }
      .publish-image {
        h1 {
          color: variables.$primary-black;
          font-size: 15px;
          font-weight: 600;
        }
        p {
          color: variables.$secondary-black;
          font-size: 13px;
        }
      }
    }
  }

  .similiar-photo {
    .similiar-photo-title {
      h1 {
        color: variables.$primary-black;
        font-weight: 600;
      }
    }
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

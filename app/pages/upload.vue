<template>
  <v-container class="mt-16">
    <v-row>
      <v-col cols="12" md="6">
        <div class="file-upload">
          <label for="file-input" class="file-label" @dragover.prevent @drop="handleDrop">
            <v-img v-if="file" :src="fileUrl" class="ma-3">
              <span class="icon-image">
                <v-icon size="50" color="white">mdi-image-edit</v-icon>
              </span>
            </v-img>
            <input id="file-input" type="file" class="file-input" accept="image/png, image/gif, image/jpeg" @change="handleFileUpload" />
            <p v-if="!file" class="placeholder-text">Drag and drop or click to upload</p>
          </label>
        </div>
      </v-col>
      <v-col cols="12" md="6">
        <div class="upload-form">
          <v-form @submit.prevent="submitForm">
            <v-text-field v-model="formData.title" color="primary" label="Title" placeholder="Enter your title" variant="outlined" prepend-inner-icon="mdi mdi-format-title" :disabled="loading"></v-text-field>
            <v-textarea v-model="formData.description" label="Description" placeholder="Enter your description" variant="outlined" prepend-inner-icon="mdi mdi-text-long" :disabled="loading"></v-textarea>
            <v-select v-model="formData.folder" variant="outlined" prepend-inner-icon="mdi mdi-folder" :items="[...folders.map((folder) => folder.folder_name), 'none']" label="Select Folder" :disabled="loading"></v-select>
            <v-btn type="submit" class="gradient-button text-none" rounded="lg" size="large" min-width="500" :loading="loading">Submit</v-btn>
          </v-form>
        </div>
      </v-col>
    </v-row>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const file = ref<File | null>(null);
const totalFolders = ref<number>(0);
let folders = ref<[object]>([{}]);
const url = useRuntimeConfig().public.URL_API;
const getToken = useCookie('auth_token');
let loading = ref<boolean>(false);

let snackMes = ref<object>({ text: '', prependIcon: '' });
let snackbar = ref<boolean>(false);

let formData = ref<object>({
  title: '',
  description: '',
  folder: null,
});

definePageMeta({
  middleware: ['auth'],
});

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

const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files.length > 0) {
    file.value = input.files[0];
  }
};

const handleDrop = (event: DragEvent) => {
  event.preventDefault();
  const files = event.dataTransfer?.files;
  if (files && files.length > 0) {
    file.value = files[0];
  }
};

const fileUrl = computed(() => (file.value ? URL.createObjectURL(file.value) : ''));

const submitForm = async () => {
  try {
    loading.value = true;
    const formDataSend = new FormData();
    formDataSend.append('title', formData.value.title);
    formDataSend.append('description', formData.value.description);
    formDataSend.append('file', file.value);
    // Cari ID folder berdasarkan nama folder yang dipilih
    if (formData.value.folder == 'none') {
      formDataSend.append('folder_id', '');
    } else {
      const selectedFolderName = formData.value.folder;
      const matchingFolder = folders.value.find((folder) => folder.folder_name === selectedFolderName);

      const FolderId = matchingFolder?.id;

      if (FolderId) {
        // Jika folder ditemukan, tambahkan ID folder ke formData
        formDataSend.append('folder_id', FolderId);
      } else {
        // Jika folder tidak ditemukan, handle error atau tambahkan logika untuk membuat folder baru
        console.error('Folder not found:', selectedFolderName);
        // Implementasikan penanganan error atau logika pembuatan folder baru di sini
      }
    }

    // Kirim data menggunakan Axios
    const response = await axios.post(`${url}/photos`, formDataSend, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Accept: 'application/json',
        Authorization: `Bearer ${getToken.value}`,
      },
    });

    console.log('Response:', response.data);

    showSnackbar(response.data.message, 'success');

    formData.value = {};
    file.value = null;
  } catch (error) {
    console.error('Error:', error);
    showSnackbar(error.response.data.message, 'error');
    // Gagal, handle error di sini
  } finally {
    loading.value = false;
  }
};

onUnmounted(() => {
  if (file.value) {
    URL.revokeObjectURL(fileUrl.value);
  }
});

try {
  const { data: data } = await useFetch(`${url}/folders`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${getToken.value}`,
    },
  });
  folders.value = data.value.data;
  console.log(folders.value);
  alert.value = true;
} catch (error) {
  console.error('Error fetching images:', error);
} finally {
  // skeletonLoaders.value = false;
}
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
.file-upload {
  .file-label {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    min-width: 200px;
    border: 2px dashed #c7c7c7;
    border-radius: 10px;
    position: relative;
    cursor: pointer;
  }

  .file-input {
    display: none;
  }

  .placeholder-text {
    color: #808080;
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
    transition: top 0.5s ease, opacity 0.3s ease;
    opacity: 0;
  }

  .file-label:hover .icon-image {
    top: 0;
    opacity: 1;
  }
}

.upload-form {
  margin-top: 20px;

  .v-form {
    max-width: 500px;
    margin: auto;

    .v-select {
      width: 100%;
    }

    .v-btn {
      margin-top: 20px;
    }
  }
}
</style>

<template>
  <span>
    <v-menu min-width="200px" rounded>
      <template v-slot:activator="{ props }">
        <v-btn icon v-bind="props" style="background-color: transparent">
          <v-avatar color="#90A4AE" :image="`${urlAvatar}${store.getUser.avatar_name}`"> </v-avatar>
        </v-btn>
      </template>
      <v-card>
        <v-card-text>
          <div class="mx-auto text-center list-button">
            <v-avatar color="#90A4AE" :image="`${urlAvatar}${store.getUser.avatar_name}`"> </v-avatar>
            <h3 class="mt-3">{{ store.getUser.username }}</h3>
            <p class="text-caption mt-1">{{ store.getUser.bio }}</p>
            <v-divider class="my-3"></v-divider>
            <v-btn rounded variant="text" class="text-none" prepend-icon="mdi-image-multiple-outline" :to="'/users/' + store.getUser.username"> My Photos </v-btn>
            <v-divider class="my-3"></v-divider>
            <v-btn rounded variant="text" class="text-none" prepend-icon="mdi-cog-outline" :to="'/settings/profile'"> Settings </v-btn>
            <v-divider class="my-3"></v-divider>
            <v-btn rounded variant="text" class="text-none" prepend-icon="mdi-logout-variant" @click="logoutModel = true" persistent> Log out </v-btn>
          </div>
        </v-card-text>
      </v-card>
    </v-menu>
    <v-dialog v-model="logoutModel" transition="dialog-top-transition" max-width="400">
      <v-card>
        <v-card-text class="pa-8 card-text-dialog">
          <span class="mx-auto icon-alert">
            <v-icon size="60">mdi-logout-variant</v-icon>
          </span>

          <div class="text-center text-logout mt-5">
            <h3>Do you want to log out ?</h3>
          </div>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn variant="text" class="text-none" @click="logoutSubmit" :loading="loading">Yes</v-btn>
          <v-btn variant="text" class="text-none" @click="logoutModel = false" :disabled="loading">Close</v-btn>
        </v-card-actions>
      </v-card>
      <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
    </v-dialog>
  </span>
</template>

<script setup lang="ts">
import { useUserStore } from '~/stores/store';

const store = useUserStore();
const router = useRouter();
const urlAvatar = useRuntimeConfig().public.URL_AVATAR;
const url = useRuntimeConfig().public.URL_API;
let authToken = useCookie('auth_token');
let snackbar = ref<boolean>(false);
let snackMes = ref<object>({ text: '', prependIcon: '' });
const logoutModel = ref<boolean>(false);
let loading = ref<boolean>(false);

const showSnackbar = (message: string, type: string = 'success') => {
  snackMes.value = { text: message, prependIcon: type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' };
  snackbar.value = true;
  // $ref.snackbar.value.show(snackMes);
  setTimeout(() => {
    snackbar.value = false;
  }, 5000);
};
const handleSnackbarClose = () => {
  snackbar.value = false;
};
const logoutSubmit = async () => {
  try {
    loading.value = true;
    const { data } = await useFetch(`${url}/logout`, {
      method: 'GET', // Sesuaikan dengan metode yang dibutuhkan
      headers: {
        Authorization: `Bearer ${authToken.value}`,
      },
    });
    authToken.value = null;

    store.setUser({});
    console.log('Logout successful:', data);
    router.push('/login');
  } catch (error) {
    console.error('Logout failed:', error);
    showSnackbar(error.value.data.message, 'error');
  } finally {
    loading.value = false;
  }
};
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';

.card-text-dialog {
  .icon-alert {
    display: flex;
    justify-content: center;
    color: variables.$primary-black;
    font-weight: 500;
  }

  .text-logout {
    h3 {
      color: variables.$primary-black;
      font-weight: 500;
    }
  }
}
</style>

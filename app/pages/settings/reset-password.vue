<template>
  <v-container>
    <LayoutElementsSidebarSettings />
    <v-form @submit.prevent="submit">
      <v-row class="mx-10 mt-10">
        <v-col cols="12" md="6">
          <v-text-field
            label="New Password"
            placeholder="Enter your new password "
            variant="outlined"
            v-model="formDataResetPassword.newPassword"
            prepend-inner-icon="mdi-lock"
            :append-inner-icon="show2 ? 'mdi-eye-off' : 'mdi-eye'"
            :type="show2 ? 'text' : 'password'"
            @click:append-inner="show2 = !show2"
            :hint="hint"
            :disabled="submitLoading"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
            label="Confirm Password"
            placeholder="Enter your password "
            variant="outlined"
            v-model="formDataResetPassword.confirmPassword"
            prepend-inner-icon="mdi-lock"
            :append-inner-icon="show3 ? 'mdi-eye-off' : 'mdi-eye'"
            :type="show3 ? 'text' : 'password'"
            @click:append-inner="show3 = !show3"
            :hint="hint"
            :disabled="submitLoading"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="12" class="text-end">
          <v-btn class="gradient-button text-none px-10" type="submit" rounded="lg" :loading="submitLoading">Submit</v-btn>
        </v-col>
      </v-row>
    </v-form>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
  </v-container>
</template>

<script lang="ts" setup>
import axios from 'axios';

const url = useRuntimeConfig().public.URL_API;
let snackbar = ref<boolean>(false);
let snackMes = ref({ text: '', prependIcon: '' });
let show1 = ref<boolean>(false);
let show2 = ref<boolean>(false);
let show3 = ref<boolean>(false);
let submitLoading = ref<boolean>(false);
const cookie = useCookie('auth_token');

let formDataResetPassword = ref<object>({});

let hint = ref<string>('At least 8 characters');

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
const submit = async () => {
  try {
    submitLoading.value = true;

    console.log(formDataResetPassword.value);
    // Pastikan formDataResetPassword dan URL API telah ditentukan dengan benar
    const response = await axios.post(`${url}/reset-password`, formDataResetPassword.value, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${cookie.value}`,
      },
    });

    // Tampilkan snackbar dengan pesan sukses
    showSnackbar(response.data.message, 'success');

    // Reset nilai formulir setelah berhasil
    formDataResetPassword.value = {};
  } catch (error) {
    console.log(error);
    // Tangani kesalahan dan tampilkan snackbar dengan pesan kesalahan
    showSnackbar(error.response.data.message, 'error');
  } finally {
    // Setelah berhasil atau gagal, atur submitLoading ke false
    submitLoading.value = false;
  }
};
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
</style>

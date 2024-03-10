<template>
  <v-container>
    <v-form @submit.prevent="join">
      <v-row class="text-center page-login mx-auto mt-14">
        <v-col cols="12" md="12">
          <header class="title-login">
            <h1>Join Aesphot</h1>
            <div class="already-have-account mx-auto mt-3 mb-7">
              <p>
                Already have an account ?
                <NuxtLink to="login">Login</NuxtLink>
              </p>
            </div>
          </header>
        </v-col>

        <v-col cols="12" md="12">
          <v-text-field color="primary" label="Username" placeholder="Enter your username" variant="outlined" prepend-inner-icon="mdi-account" v-model="formData.username" :disabled="loading"></v-text-field>
        </v-col>
        <v-col cols="12" md="12">
          <v-text-field color="primary" label="Full Name" placeholder="Enter your password" variant="outlined" prepend-inner-icon="mdi-account-edit" v-model="formData.full_name" :disabled="loading"></v-text-field>
        </v-col>
        <v-col cols="12" md="12">
          <v-text-field color="primary" label="Email" placeholder="Enter your email" variant="outlined" prepend-inner-icon="mdi-email" v-model="formData.email" :disabled="loading"></v-text-field>
        </v-col>
        <v-col cols="12" md="12">
          <v-textarea color="primary" label="Address" placeholder="Enter your address" variant="outlined" prepend-inner-icon="mdi-map-marker" v-model="formData.address" :disabled="loading"></v-textarea>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field color="primary" label="Password" placeholder="Enter your password" variant="outlined" prepend-inner-icon="mdi-lock" type="password" v-model="formData.password" :disabled="loading"></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
            color="primary"
            label="Confrim Password"
            placeholder="Enter your confrim password"
            variant="outlined"
            prepend-inner-icon="mdi-lock"
            type="password"
            v-model="formData.confirmPassword"
            :disabled="loading"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="12">
          <v-btn class="gradient-button text-none" type="submit" rounded="lg" size="large" :loading="loading">Join</v-btn>
        </v-col>
      </v-row>
    </v-form>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />

    <v-dialog v-model="isOtpDialogOpen" transition="dialog-top-transition" width="auto" :click-outside="false">
      <template v-slot:default>
        <AuthVerifyOtp :formData="formData" routeUrl="/verify-otp-join" />
      </template>
    </v-dialog>
  </v-container>
</template>

<script setup lang="ts">
import axios from 'axios';
import { useRouter } from 'vue-router';
useHead({
  title: 'Join page',
});
definePageMeta({
  layout: 'auth',
  middleware: ['auth'],
});

const router = useRouter();
const url = useRuntimeConfig().public.URL_API;

let isOtpDialogOpen = ref<boolean>(false);
let loading = ref<boolean>(false);
let snackbar = ref<boolean>(false);

let formData = ref({
  username: '',
  full_name: '',
  email: '',
  address: '',
  password: '',
  confirmPassword: '',
});

let snackMes = ref({ text: '', prependIcon: '' });

const handleSnackbarClose = () => {
  snackbar.value = false;
};
const showSnackbar = (message: string, type: string = 'success') => {
  snackMes.value = { text: message, prependIcon: type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' };
  snackbar.value = true;
  // $ref.snackbar.value.show(snackMes);
};

const join = async () => {
  console.log('halo');
  try {
    loading.value = true;
    const response = await axios.post(
      `${url}/join`,
      {
        username: formData.value.username,
        email: formData.value.email,
        password: formData.value.password,
        confirmPassword: formData.value.confirmPassword,
      },
      {
        headers: {
          Accept: 'application/json',
        },
      }
    );
    showSnackbar(response.data.message, 'error');
    isOtpDialogOpen.value = true;
    console.log('Login successful:', response.data.message);
  } catch (error) {
    console.log('Login failed:', error);
    showSnackbar(error.response.data.message, 'error');
  } finally {
    loading.value = false;
  }
};
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
.page-login {
  display: flex;
  width: 50%;

  .title-login {
    h1 {
      font-weight: 500;
      color: variables.$primary-black;
    }
    p {
      font-weight: 400;
      color: variables.$secondary-black;
      margin-top: 7px;
    }
  }

  .gradient-button {
    width: 100%;
    font-size: 18px;
  }

  .already-have-account {
    color: variables.$primary-black;
    a {
      color: variables.$thirty-black;
      &:hover {
        color: variables.$primary-black;
      }
    }
  }
}
</style>

<template>
  <v-container>
    <v-form @submit.prevent="login">
      <v-row class="text-center page-login mx-auto mt-16">
        <v-col cols="12" md="12">
          <header class="title-login">
            <h1>Login</h1>
            <p>Welcome Back</p>
          </header>
        </v-col>

        <v-col cols="12" md="12">
          <v-text-field v-model="formData.usernameOrEmail" color="primary" label="Username Or Email" placeholder="Enter your username or email" variant="outlined" prepend-inner-icon="mdi-account" :disabled="loading"></v-text-field>
        </v-col>
        <v-col cols="12" md="12" style="text-align: start">
          <v-text-field v-model="formData.password" color="primary" label="Password" placeholder="Enter your password" variant="outlined" prepend-inner-icon="mdi-lock" type="password" :disabled="loading"></v-text-field>

          <NuxtLink to="/forgot-password" class="forgot-password-link">Forgot Password</NuxtLink>
        </v-col>
        <v-col cols="12" md="12">
          <v-btn type="submit" class="gradient-button text-none" rounded="lg" size="large" :loading="loading">Login</v-btn>
        </v-col>
        <v-divider class="mt-5" thickness="2"></v-divider>
        <div class="have-no-account mx-auto mt-8">
          <p>
            Don't have account
            <NuxtLink to="join">Join</NuxtLink>
          </p>
        </div>
      </v-row>
    </v-form>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />

    <v-dialog v-model="isOtpDialogOpen" transition="dialog-top-transition" width="auto" :click-outside="false">
      <template v-slot:default>
        <AuthVerifyOtp :formData="formData" routeUrl="/verify-otp" />
      </template>
    </v-dialog>
  </v-container>
</template>

<script setup lang="ts">
import axios from 'axios';
import Cookies from 'js-cookie';
import { useUserStore } from '~/stores/store';

const store = useUserStore();

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
  usernameOrEmail: '',
  password: '',
});

let snackMes = ref<object>({ text: '', prependIcon: '' });

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

const setCookie = (nameCookie: string, token: string): void => {
  Cookies.set(nameCookie, token, { expires: 7 });
};

const login = async () => {
  try {
    loading.value = true;
    const response = await axios.post(`${url}/login`, formData.value, {
      headers: {
        Accept: 'application/json',
      },
    });
    console.log(response);
    store.setUser(response.data.data.user);
    setCookie('auth_token', response.data.data.token);
    console.log('Login successful:', response.data.message);

    // Arahkan ke halaman utama
    router.push('/');
  } catch (error) {
    if (error.response.data.message == 'OTP expired.You have to send otp') {
      isOtpDialogOpen.value = true;
    }
    console.log('Login failed:', error.response.data.message);

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

  .forgot-password-link {
    text-decoration: underline;
    text-align: start !important;
    color: variables.$secondary-black;

    &:hover {
      color: variables.$primary-black;
    }
  }

  .gradient-button {
    width: 100%;
    font-size: 18px;
  }

  .have-no-account {
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

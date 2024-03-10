<template>
  <v-container>
    <v-form @submit.prevent="submit">
      <v-row class="text-center page-login mx-auto mt-16">
        <v-col cols="12" md="12">
          <header class="title-login">
            <h1>Forgot Password</h1>
            <p>Send your email</p>
          </header>
        </v-col>
        <v-col cols="12" md="12" style="text-align: start">
          <v-text-field v-model="formData.email" color="primary" label="Email" placeholder="Enter your email" variant="outlined" prepend-inner-icon="mdi-email" type="email" :disabled="loading"></v-text-field>
        </v-col>
        <v-col cols="12" md="12">
          <v-btn type="submit" class="gradient-button text-none" rounded="lg" size="large" :loading="loading">Submit</v-btn>
        </v-col>
        <v-divider class="mt-5" thickness="2"></v-divider>

        <div class="have-no-account mx-auto mt-8">
          <p>
            Do you remember your password ?
            <NuxtLink to="/login">Login</NuxtLink>
          </p>
        </div>
      </v-row>
    </v-form>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
  </v-container>
</template>

<script lang="ts" setup>
import axios from 'axios';
import Cookies from 'js-cookie';

useHead({
  title: 'Forgot password page',
});
definePageMeta({
  layout: 'auth',
  // middleware: ['reset-token'],
});

let formData = ref({
  email: '',
});

let loading = ref<boolean>(false);
let snackbar = ref<boolean>(false);
let snackMes = ref<object>({ text: '', prependIcon: '' });
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

const setCookie = (nameCookie: string, token: string): void => {
  Cookies.set(nameCookie, token, { expires: 7 });
};

const submit = async () => {
  try {
    loading.value = true;
    const response = await axios.post(`${url}/forgot-password`, formData.value, {
      headers: {
        Accept: 'application/json',
      },
    });

    setCookie('reset_token', response.data.data.token);
    console.log(response);
    // setCookie('auth_token', response.data.data.token);
    console.log('Login successful:', response.data.message);
    showSnackbar(response.data.message, 'success');
  } catch (error) {
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

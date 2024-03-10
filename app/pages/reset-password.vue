<template>
  <v-container>
    <v-form @submit.prevent="submit">
      <v-row class="text-center page-login mx-auto mt-16">
        <v-col cols="12" md="12">
          <header class="title-login">
            <h1>Reset Password</h1>
            <p>Change your password</p>
          </header>
        </v-col>
        <v-col cols="12" md="12" style="text-align: start">
          <v-text-field
            label="New Password"
            v-model="formData.newPassword"
            variant="outlined"
            prepend-inner-icon="mdi-lock"
            :append-inner-icon="show1 ? 'mdi-eye-off' : 'mdi-eye'"
            :type="show1 ? 'text' : 'password'"
            @click:append-inner="show1 = !show1"
            :hint="hint"
            :disabled="loading"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="12" style="text-align: start">
          <v-text-field
            label="Confirm Password"
            placeholder="Enter your confirm password "
            variant="outlined"
            v-model="formData.confirmPassword"
            prepend-inner-icon="mdi-lock"
            :append-inner-icon="show2 ? 'mdi-eye-off' : 'mdi-eye'"
            :type="show2 ? 'text' : 'password'"
            @click:append-inner="show2 = !show2"
            :hint="hint"
            :disabled="loading"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="12">
          <v-btn type="submit" class="gradient-button text-none" rounded="lg" size="large" :loading="loading">Submit</v-btn>
        </v-col>
        <!-- <v-divider class="mt-5" thickness="2"></v-divider> -->
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
  middleware: ['reset-token'],
});

let formData = ref({
  email: '',
});
const router = useRouter();

let show1 = ref<boolean>(false);
let show2 = ref<boolean>(false);
let loading = ref<boolean>(false);
let snackbar = ref<boolean>(false);
let snackMes = ref<object>({ text: '', prependIcon: '' });
const url = useRuntimeConfig().public.URL_API;
let cookie = useCookie('reset_token');

let hint = ref<string>('At least 8 characters');

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

const submit = async () => {
  try {
    loading.value = true;
    formData.value.token = cookie.value;
    const response = await axios.post(`${url}/reset-password`, formData.value, {
      headers: {
        Accept: 'application/json',
      },
    });

    cookie.value = null;
    console.log(response);
    showSnackbar(response.data.message, 'success');
    router.push('/login');
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

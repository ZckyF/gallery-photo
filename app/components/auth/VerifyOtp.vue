<template>
  <v-sheet class="py-10 px-8 mx-auto ma-4 text-center" elevation="4" rounded="lg" max-width="500" width="100%">
    <h3 class="text-h5">Verification Code</h3>

    <div class="text-subtitle-2 font-weight-light mb-3">Please enter the verification code sent to your email</div>

    <v-form>
      <v-otp-input v-model="otp" class="mb-8" length="6" variant="outlined" :loading="loading"></v-otp-input>
    </v-form>
    <div class="text-caption" v-if="countdown > 0">Expired OTP in {{ formatTime(countdown) }}</div>
    <div class="text-caption" v-else-if="countdown < 0">Otp is Expired</div>
    <div class="text-caption">
      <v-btn color="primary" size="x-small" text="Send New Code" variant="text" @click="resendOtp"></v-btn>
    </div>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
  </v-sheet>
</template>

<script lang="ts" setup>
import axios from 'axios';
import Cookies from 'js-cookie';
import { useUserStore } from '~/stores/store';

const store = useUserStore();

const otp = ref<string>('');
let loading = ref<boolean>(false);
let loadingSubmit = ref<boolean>(false);
const countdown = ref<number>(1800);
let resendTimeout: NodeJS.Timeout | null = null;
let snackMes = ref({ text: '', prependIcon: '' });
let snackbar = ref<boolean>(false);

const props = defineProps(['formData', 'routeUrl']);

const url = useRuntimeConfig().public.URL_API;
const router = useRouter();

const showSnackbar = (message: string, type: string = 'success') => {
  snackMes.value = { text: message, prependIcon: type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' };
  snackbar.value = true;
};
const handleSnackbarClose = () => {
  snackbar.value = false;
};
const submitOtp = async () => {
  try {
    loading.value = true;
    // Pastikan otp.value memiliki panjang 6
    if (otp.value.length === 6) {
      // Lakukan panggilan API ke /verify-otp dengan menggunakan Axios
      const dataSubmit = props.routeUrl == '/verify-otp' ? { otp: otp.value } : props.formData;
      dataSubmit.otp = otp.value;
      console.log(dataSubmit);
      const response = await axios.post(`${url}${props.routeUrl}`, dataSubmit);

      // Lakukan sesuatu dengan respons, jika diperlukan
      console.log('Verification successful:', response.data.message);
      store.setUser(response.data.data.user);
      setCookie('auth_token', response.data.data.token);
      showSnackbar(response.data.message, 'success');
      router.push('/');
    } else {
      console.log('OTP Invalid length');
    }
  } catch (error) {
    console.error('Verification failed:', error);
    showSnackbar(error.response.data.message, 'error');
  } finally {
    loading.value = false;
  }
};
const resendOtp = async () => {
  try {
    loading.value = true;
    loadingSubmit.value = true;

    // You can make an API call here to resend the OTP
    const response = await axios.post(`${url}/resend-otp`, { usernameOrEmail: props.formData.email });

    console.log(response.data.message);
    // Simulate a 30-minute countdown
    countdown.value = 1800;

    // Clear any existing interval
    if (resendTimeout !== null) {
      clearInterval(resendTimeout);
    }

    resendTimeout = setInterval(() => {
      countdown.value--;

      if (countdown.value <= 0) {
        loading.value = false;
        loadingSubmit.value = false;
        clearInterval(resendTimeout as NodeJS.Timeout);
      }
    }, 1000);
  } catch (error) {
    console.log(error);
    showSnackbar(error.response.data.message, 'error');
  } finally {
    loading.value = false;
    loadingSubmit.value = false;
  }
};

const formatTime = (seconds: number): string => {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
};
const setCookie = (nameCookie: string, token: string): void => {
  Cookies.set(nameCookie, token, { expires: 7 });
};

watchEffect(() => {
  // Callback when the value of otp changes
  if (otp.value.length === 6) {
    // If otp is full, you can trigger the submitOtp function or any other logic
    return submitOtp();
  }
  if (countdown.value === 1800) {
    // Clear any existing interval
    if (resendTimeout !== null) {
      clearInterval(resendTimeout);
    }

    resendTimeout = setInterval(() => {
      countdown.value--;

      if (countdown.value <= 0) {
        loading.value = false;
        clearInterval(resendTimeout as NodeJS.Timeout);
      }
    }, 1000);
  }
});

onUnmounted(() => {
  // Clear the interval when the component is unmounted
  if (resendTimeout !== null) {
    clearInterval(resendTimeout);
  }
});
</script>

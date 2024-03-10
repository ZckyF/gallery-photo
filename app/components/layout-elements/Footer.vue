<template>
  <v-footer class="text-center d-flex flex-column mt-16" style="background-color: transparent">
    <v-divider :thickness="3"></v-divider>
    <div>
      <v-btn v-for="icon in icons" :key="icon" class="mx-4" :icon="icon" variant="text"></v-btn>
    </div>

    <v-divider></v-divider>

    <div>{{ yearNow }} — <strong>Aesphot</strong></div>
  </v-footer>
</template>
<script lang="ts" setup>
import { ref } from 'vue';
const icons = ref(['mdi-facebook', 'mdi-twitter', 'mdi-linkedin', 'mdi-instagram']);
const yearNow = new Date().getFullYear();
</script>

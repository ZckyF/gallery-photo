<template>
  <div class="text-center ma-2">
    <v-snackbar v-model="props.snackbar" :timeout="4000" color="#FAFAFA">
      <v-icon class="mx-1">{{ snackMes.prependIcon }}</v-icon>
      <span> {{ snackMes.text }}</span>

      <template v-slot:actions>
        <span color="white" style="cursor: pointer" @click="$emit('update:snackbar', false)" text>
          <v-icon left small>mdi-close</v-icon>
        </span>
      </template>
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps(['snackbar', 'snackMes']);
</script>

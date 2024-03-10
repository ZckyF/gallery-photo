<template>
  <v-text-field v-model="searchQuery" placeholder="Search" prepend-inner-icon="mdi-magnify" @keydown.enter="handleSearch" variant="solo" rounded class="mt-5 w-full" :density="density"></v-text-field>
</template>

<script lang="ts" setup>
const props = defineProps(['density']);
const searchQuery = ref<string>('');
const router = useRouter();

const handleSearch = () => {
  if (searchQuery.value.trim() !== '') {
    // Arahkan pengguna ke halaman pencarian dengan parameter query
    router.push(`/search/${encodeURIComponent(searchQuery.value)}`);
  }
};
</script>

<style></style>

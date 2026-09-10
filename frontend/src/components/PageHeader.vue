<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
})
</script>

<template>
  <div class="mb-6">
    <nav class="text-sm text-gray-500 mb-3 flex items-center gap-2">
      <RouterLink to="/" class="text-blue-600 hover:underline">Accueil</RouterLink>
      <span>&gt;</span>
      <span>{{ title }}</span>
    </nav>

    <div class="bg-blue-900 text-white px-6 py-4 rounded">
      <h1 class="text-xl font-bold">{{ title }}</h1>
    </div>
  </div>
</template>
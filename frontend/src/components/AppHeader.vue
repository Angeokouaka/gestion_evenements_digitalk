<script setup>
</script>

<template>
  <header class="border-b border-gray-200 bg-white">
    <nav class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
      <RouterLink to="/" class="text-xl font-bold text-gray-800">
        Digitalk
      </RouterLink>
      <div class="flex items-center gap-6 text-sm">
        <RouterLink to="/evenements" class="text-gray-600 hover:text-blue-600">
          Evenements
        </RouterLink>
        <RouterLink to="/login" class="text-blue-600 hover:text-blue-800 font-medium">
          Connexion
        </RouterLink>
      </div>
    </nav>
  </header>
</template>
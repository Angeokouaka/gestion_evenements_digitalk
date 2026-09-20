<script setup>
import { ref } from 'vue'

const props = defineProps({
  modelValue: { type: Number, default: 0 },
  lecture: { type: Boolean, default: false },
  taille: { type: String, default: 'text-2xl' },
})

const emit = defineEmits(['update:modelValue'])

const survol = ref(0)

function choisir(note) {
  if (props.lecture) return
  emit('update:modelValue', note)
}
</script>

<template>
  <div class="flex items-center gap-1" :class="taille">
    <span
      v-for="etoile in 5"
      :key="etoile"
      @click="choisir(etoile)"
      @mouseenter="!lecture && (survol = etoile)"
      @mouseleave="!lecture && (survol = 0)"
      :class="[
        !lecture && 'cursor-pointer',
        (survol || modelValue) >= etoile ? 'text-yellow-400' : 'text-gray-300',
      ]"
    >
      ★
    </span>
  </div>
</template>
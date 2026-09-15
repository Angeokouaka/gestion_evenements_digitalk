<script setup>
import { ref, onMounted, watch } from 'vue'
import QRCode from 'qrcode'

const props = defineProps({
  value: { type: String, required: true },
  taille: { type: Number, default: 220 },
})

const canvasRef = ref(null)

async function dessiner() {
  if (!canvasRef.value) return
  await QRCode.toCanvas(canvasRef.value, props.value, {
    width: props.taille,
    margin: 1,
  })
}

function telecharger() {
  if (!canvasRef.value) return
  const lien = document.createElement('a')
  lien.download = 'qr-evenement.png'
  lien.href = canvasRef.value.toDataURL('image/png')
  lien.click()
}

onMounted(dessiner)
watch(() => props.value, dessiner)

defineExpose({ telecharger })
</script>

<template>
  <div class="flex flex-col items-center gap-3">
    <canvas ref="canvasRef"></canvas>
    <button
      @click="telecharger"
      class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-50"
    >
      Telecharger le QR
    </button>
  </div>
</template>
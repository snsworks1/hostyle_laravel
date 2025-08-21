<template>
  <div v-if="open" class="fixed z-40 top-24 left-20 w-80 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
    <!-- Header -->
    <div class="px-3 py-2 border-b bg-gray-50 flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <span class="text-sm font-medium text-gray-900">{{ title }}</span>
        <span class="text-xs text-gray-500">{{ templates.length }}개</span>
      </div>
      <button class="p-1 text-gray-400 hover:text-gray-600" @click="$emit('close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Body -->
    <div class="max-h-[70vh] overflow-auto p-3 space-y-3">
      <div 
        v-for="tpl in templates" 
        :key="tpl.id" 
        class="border rounded-lg overflow-hidden hover:shadow transition-shadow cursor-grab"
        draggable="true"
        @dragstart="onDragStart($event, tpl)"
      >
        <img :src="tpl.thumbnail" class="w-full h-28 object-cover" />
        <div class="px-3 py-2 text-sm text-gray-700">{{ tpl.name }}</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ open: boolean; title: string }>()
const emit = defineEmits<{ close: []; insertTemplate: [tpl: any] }>()

const templates = [
  { id: 'hero-1', name: 'Hero - 심플', type: 'hero', thumbnail: '/img/templates/hero1.svg' },
  { id: 'hero-2', name: 'Hero - 좌우', type: 'hero', thumbnail: '/img/templates/hero2.svg' },
  { id: 'features-3', name: 'Features - 3열', type: 'features', thumbnail: '/img/templates/features3.svg' },
  { id: 'cta-1', name: 'CTA - 그라데이션', type: 'cta', thumbnail: '/img/templates/cta1.svg' }
]

const onDragStart = (e: DragEvent, tpl: any) => {
  e.dataTransfer?.setData('application/x-builder-template', JSON.stringify(tpl))
  e.dataTransfer?.setDragImage(new Image(), 0, 0)
}
</script>

<style scoped>
</style>



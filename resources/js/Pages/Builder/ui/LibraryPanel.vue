<template>
  <div>
    <h3 class="font-semibold mb-2">섹션 라이브러리</h3>
    <div class="space-y-2">
      <button class="chip" @click="add('hero')">Hero</button>
      <button class="chip" @click="add('features')">Features</button>
      <button class="chip" @click="add('cta')">CTA</button>
    </div>

    <div class="mt-6">
      <h4 class="font-medium mb-1">사이트 토큰</h4>
      <label class="block text-sm">브랜드 컬러</label>
      <input class="i" v-model="store.tokens.brandColor" type="color"/>
      <label class="block text-sm mt-2">본문 폭</label>
      <input class="i" v-model="store.tokens.contentWidth" placeholder="1080px"/>
      <label class="block text-sm mt-2">기본 폰트</label>
      <input class="i" v-model="store.tokens.fontBase"/>
      <label class="block text-sm mt-2">Radius</label>
      <input class="i" v-model="store.tokens.radius"/>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useBuilder } from '@/builder/store';
import { sectionDefaults } from '@/builder/registry';

const store = useBuilder();

function add(type: 'hero'|'features'|'cta'){
  store.add({ type, props: JSON.parse(JSON.stringify(sectionDefaults[type])) });
}
</script>

<style scoped>
.chip{border:1px solid #ddd;padding:.4rem .7rem;border-radius:.5rem}
.i{width:100%;border:1px solid #ddd;padding:.4rem .6rem;border-radius:.4rem}
</style>

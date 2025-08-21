<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm">제목</label>
      <input class="i" v-model="m.title"/>
    </div>
    <div>
      <label class="block text-sm">부제</label>
      <input class="i" v-model="m.subtitle"/>
    </div>
    <div>
      <label class="block text-sm">정렬</label>
      <select class="i" v-model="m.align">
        <option>left</option>
        <option>center</option>
        <option>right</option>
      </select>
    </div>
    <div>
      <label class="block text-sm">배경</label>
      <input class="i" v-model="m.bg" placeholder="#ffffff 또는 url(...)"/>
    </div>
    <div>
      <label class="block text-sm">패딩Y(px)</label>
      <input class="i" type="number" v-model.number="m.py"/>
    </div>
    <div class="pt-2">
      <button class="btn" @click="$emit('update', m)">적용</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue';

const props = defineProps<{ value: any }>();
const m = reactive(JSON.parse(JSON.stringify(props.value||{})));

watch(() => props.value, v => Object.assign(m, JSON.parse(JSON.stringify(v||{}))));
</script>

<style scoped>
.i{width:100%;border:1px solid #ddd;padding:.4rem .6rem;border-radius:.4rem}
.btn{border:1px solid #111;padding:.5rem 1rem;border-radius:.5rem}
</style>

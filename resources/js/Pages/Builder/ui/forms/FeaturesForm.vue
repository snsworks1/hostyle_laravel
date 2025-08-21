<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm">열 개수</label>
      <select class="i" v-model.number="m.cols">
        <option :value="2">2</option>
        <option :value="3">3</option>
        <option :value="4">4</option>
      </select>
    </div>
    <div>
      <label class="block text-sm">아이템</label>
      <div v-for="(it,i) in m.items" :key="i" class="border rounded p-2 mt-2">
        <input class="i" v-model="it.title" placeholder="제목"/>
        <input class="i mt-1" v-model="it.text" placeholder="설명"/>
        <button class="text-xs mt-1" @click="remove(i)">삭제</button>
      </div>
      <button class="btn mt-2" @click="add">아이템 추가</button>
    </div>
    <div class="pt-2">
      <button class="btn" @click="$emit('update', m)">적용</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue';

const props = defineProps<{ value: any }>();
const m = reactive(JSON.parse(JSON.stringify(props.value||{ cols:3, items:[] })));

watch(() => props.value, v => Object.assign(m, JSON.parse(JSON.stringify(v||{}))));

function add(){ m.items.push({ title: '새 항목', text: '' }); }
function remove(i:number){ m.items.splice(i,1); }
</script>

<style scoped>
.i{width:100%;border:1px solid #ddd;padding:.4rem .6rem;border-radius:.4rem}
.btn{border:1px solid #111;padding:.5rem 1rem;border-radius:.5rem}
</style>

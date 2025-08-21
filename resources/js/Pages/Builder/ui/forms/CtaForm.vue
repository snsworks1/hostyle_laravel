<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm">문구</label>
      <input class="i" v-model="m.text"/>
    </div>
    <div class="grid grid-cols-2 gap-2">
      <div>
        <label class="block text-sm">버튼 라벨</label>
        <input class="i" v-model="m.button.label"/>
      </div>
      <div>
        <label class="block text-sm">버튼 링크</label>
        <input class="i" v-model="m.button.href"/>
      </div>
    </div>
    <div class="pt-2">
      <button class="btn" @click="$emit('update', m)">적용</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue';

const props = defineProps<{ value: any }>();
const m = reactive(JSON.parse(JSON.stringify(props.value||{ text:'', button:{label:'',href:'#'} })));

watch(() => props.value, v => Object.assign(m, JSON.parse(JSON.stringify(v||{}))));
</script>

<style scoped>
.i{width:100%;border:1px solid #ddd;padding:.4rem .6rem;border-radius:.4rem}
.btn{border:1px solid #111;padding:.5rem 1rem;border-radius:.5rem}
</style>

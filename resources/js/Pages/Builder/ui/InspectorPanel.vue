<template>
  <div>
    <h3 class="font-semibold mb-3">속성 편집</h3>
    <div v-if="!sel" class="text-gray-500">섹션을 선택하세요.</div>
    <component v-else :is="formOf(sel.type)" :value="sel.props" @update="update" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useBuilder } from '@/builder/store';
import HeroForm from '@/Pages/Builder/ui/forms/HeroForm.vue';
import FeaturesForm from '@/Pages/Builder/ui/forms/FeaturesForm.vue';
import CtaForm from '@/Pages/Builder/ui/forms/CtaForm.vue';

const store = useBuilder();
const sel = computed(() => store.selected);

function formOf(t: string){ 
  return ({ hero: HeroForm, features: FeaturesForm, cta: CtaForm } as any)[t]; 
}
function update(val: any){ store.updateSelected(val); }
</script>

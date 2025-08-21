<template>
  <div class="p-8 min-h-full flex justify-center">
    <div class="w-full" :style="{maxWidth: store.tokens.contentWidth}">
      <draggable v-model="sections" item-key="idx" handle=".drag-handle" @end="onEnd">
        <template #item="{element, index}">
          <div class="mb-6 group border rounded-lg bg-white shadow-sm">
            <div class="flex items-center justify-between border-b px-3 py-2 bg-gray-50">
              <div class="text-sm text-gray-600">{{ element.type }}</div>
              <div class="flex items-center gap-2">
                <span class="drag-handle cursor-move text-gray-400">⠿</span>
                <button class="text-xs" @click="store.select(index)">선택</button>
                <button class="text-xs text-red-600" @click="store.remove(index)">삭제</button>
              </div>
            </div>
            <component :is="map[element.type]" v-bind="element.props" class="p-6"/>
          </div>
        </template>
      </draggable>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useBuilder } from '@/builder/store';
import { VueDraggableNext as draggable } from 'vue-draggable-next';
import { sectionMap as map } from '@/builder/registry';

const store = useBuilder();
const sections = computed({
  get: () => store.schema.sections.map((s, idx) => ({ ...s, idx })),
  set: (val) => {/* v-model 요구로 noop */}
});

function onEnd(evt: any){
  if (evt.oldIndex !== evt.newIndex) store.move(evt.oldIndex, evt.newIndex);
}
</script>

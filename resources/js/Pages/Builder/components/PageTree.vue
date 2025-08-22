<template>
  <div class="page-tree">
    <draggable 
      v-model="treeData" 
      item-key="id"
      handle=".drag-handle"
      @end="onDragEnd"
      class="tree-container"
    >
      <template #item="{ element }">
        <PageNode 
          :page="element" 
          :current-page="currentPage"
        />
      </template>
    </draggable>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { VueDraggableNext as draggable } from 'vue-draggable-next';
import { useBuilder } from '@/builder/store';
import PageNode from './PageNode.vue';

const store = useBuilder();

// 현재 선택된 페이지
const currentPage = computed(() => store.currentPage);

// 트리 데이터 (계층 구조)
const treeData = computed({
  get: () => {
    const rootPages = store.pages.filter(p => p.parent_id === 0 || !p.parent_id);
    console.log('PageTree - store.pages:', store.pages);
    console.log('PageTree - rootPages:', rootPages);
    return rootPages;
  },
  set: (value) => {
    // v-model 요구로 noop
  }
});

function onDragEnd(evt: any) {
  if (evt.oldIndex !== evt.newIndex) {
    store.reorderPages(evt.oldIndex, evt.newIndex);
  }
}

function selectPage(id: number) {
  store.selectPage(id);
}

function editPage(id: number) {
  store.editPage(id);
}

function duplicatePage(id: number) {
  store.duplicatePage(id);
}

function deletePage(id: number) {
  if (confirm('정말 이 페이지를 삭제하시겠습니까?')) {
    store.deletePage(id);
  }
}
</script>

<style scoped>
.page-tree {
  padding: 0.5rem;
}

.tree-container {
  min-height: 100px;
}

.drag-handle {
  cursor: move;
  color: #9ca3af;
}

.drag-handle:hover {
  color: #6b7280;
}
</style>


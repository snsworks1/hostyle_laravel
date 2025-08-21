<template>
  <div class="page-node" :class="{ active: isActive, 'is-main': page.is_main }">
    <div class="node-content" @click="$emit('select', page.id)">
      <!-- 드래그 핸들 -->
      <div class="drag-handle">
        <i class="fas fa-grip-vertical"></i>
      </div>
      
      <!-- 페이지 아이콘 -->
      <div class="node-icon">
        <i :class="getPageIcon()"></i>
      </div>
      
      <!-- 페이지 정보 -->
      <div class="node-info">
        <div class="node-name">{{ page.name }}</div>
        <div class="node-meta">
          <span v-if="page.is_main" class="main-badge">메인</span>
          <span v-if="page.menu_hide" class="hidden-badge">숨김</span>
          <span class="node-type">{{ getPageTypeLabel() }}</span>
        </div>
      </div>
      
      <!-- 액션 버튼들 -->
      <div class="node-actions">
        <button class="action-btn" @click.stop="$emit('edit', page.id)" title="설정">
          <i class="fas fa-cog"></i>
        </button>
        <button class="action-btn" @click.stop="$emit('duplicate', page.id)" title="복제">
          <i class="fas fa-copy"></i>
        </button>
        <button class="action-btn" @click.stop="$emit('delete', page.id)" title="삭제">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
    
    <!-- 하위 페이지들 -->
    <div v-if="hasChildren" class="node-children">
      <PageNode 
        v-for="child in children" 
        :key="child.id" 
        :page="child"
        :currentPage="currentPage"
        @select="$emit('select', $event)"
        @edit="$emit('edit', $event)"
        @duplicate="$emit('duplicate', $event)"
        @delete="$emit('delete', $event)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useBuilder } from '@/builder/store';

interface PageNode {
  id: number;
  name: string;
  type: string;
  is_main: boolean;
  menu_hide: boolean;
  parent_id: number;
  children?: PageNode[];
}

const props = defineProps<{
  page: any;
  currentPage: any;
}>();

const store = useBuilder();

const isActive = computed(() => props.currentPage?.id === props.page.id);

const hasChildren = computed(() => props.page.children && props.page.children.length > 0);

const children = computed(() => props.page.children || []);

function getPageIcon() {
  switch (props.page.type) {
    case 'page':
      return 'fas fa-file-alt';
    case 'link':
      return 'fas fa-link';
    case 'category':
      return 'fas fa-folder';
    case 'popup':
      return 'fas fa-window-maximize';
    case 'data':
      return 'fas fa-database';
    default:
      return 'fas fa-file';
  }
}

function getPageTypeLabel() {
  switch (props.page.type) {
    case 'page':
      return '페이지';
    case 'link':
      return '링크';
    case 'category':
      return '카테고리';
    case 'popup':
      return '팝업';
    case 'data':
      return '데이터';
    default:
      return '기타';
  }
}
</script>

<style scoped>
.page-node {
  margin-bottom: 0.25rem;
}

.node-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
  border: 1px solid transparent;
}

.node-content:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
}

.node-content.active {
  background: #dbeafe;
  border-color: #3b82f6;
}

.node-content.is-main {
  background: #fef3c7;
  border-color: #f59e0b;
}

.drag-handle {
  color: #9ca3af;
  cursor: move;
  padding: 0.25rem;
}

.drag-handle:hover {
  color: #6b7280;
}

.node-icon {
  width: 1.5rem;
  text-align: center;
  color: #6b7280;
}

.node-info {
  flex: 1;
  min-width: 0;
}

.node-name {
  font-size: 0.875rem;
  font-weight: 500;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.node-meta {
  display: flex;
  gap: 0.25rem;
  margin-top: 0.125rem;
}

.main-badge {
  background: #f59e0b;
  color: white;
  font-size: 0.75rem;
  padding: 0.125rem 0.375rem;
  border-radius: 0.25rem;
}

.hidden-badge {
  background: #6b7280;
  color: white;
  font-size: 0.75rem;
  padding: 0.125rem 0.375rem;
  border-radius: 0.25rem;
}

.node-type {
  font-size: 0.75rem;
  color: #6b7280;
}

.node-actions {
  display: flex;
  gap: 0.25rem;
  opacity: 0;
  transition: opacity 0.2s;
}

.node-content:hover .node-actions {
  opacity: 1;
}

.action-btn {
  padding: 0.25rem;
  border: none;
  background: none;
  color: #6b7280;
  cursor: pointer;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #e5e7eb;
  color: #374151;
}

.node-children {
  margin-left: 1.5rem;
  padding-left: 1rem;
  border-left: 1px solid #e5e7eb;
}
</style>


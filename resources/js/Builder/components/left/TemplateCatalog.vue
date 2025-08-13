<template>
  <div class="template-catalog">
    <div class="template-catalog-header">
      <h3>템플릿</h3>
      <div class="template-filters">
        <select v-model="selectedCategory" class="category-filter">
          <option value="">모든 카테고리</option>
          <option value="corporate">기업</option>
          <option value="portfolio">포트폴리오</option>
          <option value="blog">블로그</option>
          <option value="ecommerce">쇼핑몰</option>
        </select>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="템플릿 검색..."
          class="search-input"
        >
      </div>
    </div>
    
    <div class="template-grid">
      <div 
        v-for="template in filteredTemplates" 
        :key="template.id"
        class="template-item"
        @click="insertTemplate(template)"
      >
        <div class="template-thumbnail">
          <img :src="template.thumbnail_url" :alt="template.title">
          <div class="template-overlay">
            <button class="insert-btn">
              <i class="fas fa-plus"></i>
              삽입
            </button>
          </div>
        </div>
        <div class="template-info">
          <h4 class="template-title">{{ template.title }}</h4>
          <span class="template-category">{{ getCategoryName(template.category) }}</span>
        </div>
      </div>
    </div>
    
    <div v-if="filteredTemplates.length === 0" class="no-templates">
      <i class="fas fa-search"></i>
      <p>검색 결과가 없습니다.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useBuilderStore } from '../../stores/builderStore'
import { mockApi } from '../../api/builderApi'

// Store
const store = useBuilderStore()

// Reactive data
const selectedCategory = ref('')
const searchQuery = ref('')
const templates = ref([])

// Computed
const filteredTemplates = computed(() => {
  let filtered = templates.value
  
  // 카테고리 필터
  if (selectedCategory.value) {
    filtered = filtered.filter(t => t.category === selectedCategory.value)
  }
  
  // 검색 필터
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(t => 
      t.title.toLowerCase().includes(query) ||
      t.category.toLowerCase().includes(query)
    )
  }
  
  return filtered
})

// Methods
function getCategoryName(category) {
  const categoryMap = {
    corporate: '기업',
    portfolio: '포트폴리오',
    blog: '블로그',
    ecommerce: '쇼핑몰'
  }
  return categoryMap[category] || category
}

function insertTemplate(template) {
  store.insertTemplate(template)
}

// 컴포넌트 마운트 시 템플릿 로드
onMounted(() => {
  // 모의 템플릿 데이터 로드
  templates.value = mockApi.getMockTemplates()
})
</script>

<style scoped>
.template-catalog {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.template-catalog-header {
  padding: 16px;
  border-bottom: 1px solid #e5e7eb;
}

.template-catalog-header h3 {
  margin: 0 0 16px 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.template-filters {
  display: flex;
  gap: 8px;
}

.category-filter,
.search-input {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.category-filter {
  background: white;
  min-width: 120px;
}

.search-input {
  flex: 1;
}

.category-filter:focus,
.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.template-grid {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 16px;
}

.template-item {
  cursor: pointer;
  border-radius: 8px;
  overflow: hidden;
  background: white;
  border: 1px solid #e5e7eb;
  transition: all 0.2s;
}

.template-item:hover {
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
  transform: translateY(-2px);
}

.template-thumbnail {
  position: relative;
  aspect-ratio: 16/10;
  overflow: hidden;
}

.template-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.template-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
}

.template-item:hover .template-overlay {
  opacity: 1;
}

.insert-btn {
  background: #667eea;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
}

.insert-btn:hover {
  background: #5a67d8;
  transform: scale(1.05);
}

.template-info {
  padding: 12px;
}

.template-title {
  margin: 0 0 4px 0;
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  line-height: 1.3;
}

.template-category {
  font-size: 12px;
  color: #6b7280;
  background: #f3f4f6;
  padding: 2px 6px;
  border-radius: 10px;
}

.no-templates {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  padding: 40px 20px;
}

.no-templates i {
  font-size: 48px;
  margin-bottom: 16px;
}

.no-templates p {
  margin: 0;
  font-size: 16px;
}

/* 반응형 디자인 */
@media (max-width: 768px) {
  .template-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }
  
  .template-filters {
    flex-direction: column;
  }
}
</style>

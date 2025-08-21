<template>
  <div class="features-section py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <h2 
          class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 cursor-pointer hover:bg-blue-50 hover:bg-opacity-50 rounded p-2 transition-colors"
          :class="{ 'ring-2 ring-blue-500 bg-blue-50': props.section?.selectedElement?.id === 'section-title' }"
          :data-element-id="`${props.section?.id}-section-title`"
          @click.stop="handleElementClick('text', 'section-title', $event)"
        >
          {{ props.section?.props?.title || '주요 기능' }}
        </h2>
        <p 
          class="text-xl text-gray-600 max-w-2xl mx-auto cursor-pointer hover:bg-blue-50 hover:bg-opacity-50 rounded p-2 transition-colors"
          :class="{ 'ring-2 ring-blue-500 bg-blue-50': props.section?.selectedElement?.id === 'section-subtitle' }"
          :data-element-id="`${props.section?.id}-section-subtitle`"
          @click.stop="handleElementClick('text', 'section-subtitle', $event)"
        >
          {{ props.section?.props?.subtitle || '우리 서비스의 핵심 기능들을 확인하세요' }}
        </p>
      </div>
      
      <!-- Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(feature, index) in props.section?.props?.features || defaultFeatures"
          :key="index"
          class="feature-card group p-6 bg-gray-50 rounded-xl hover:bg-white hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2"
        >
          <!-- Icon -->
          <div 
            class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors cursor-pointer"
            :class="{ 'ring-2 ring-blue-500': props.section?.selectedElement?.id === `feature-icon-${index}` }"
            :data-element-id="`${props.section?.id}-feature-icon-${index}`"
            @click.stop="handleElementClick('icon', `feature-icon-${index}`, $event)"
          >
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="feature.icon === 'star'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
              <path v-else-if="feature.icon === 'heart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              <path v-else-if="feature.icon === 'lightning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          
          <!-- Content -->
          <h3 
            class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors cursor-pointer hover:bg-blue-50 hover:bg-opacity-50 rounded p-2 transition-colors"
            :class="{ 'ring-2 ring-blue-500 bg-blue-50': props.section?.selectedElement?.id === `feature-title-${index}` }"
            :data-element-id="`${props.section?.id}-feature-title-${index}`"
            @click.stop="handleElementClick('text', `feature-title-${index}`, $event)"
          >
            {{ feature.title }}
          </h3>
          <p 
            class="text-gray-600 leading-relaxed cursor-pointer hover:bg-blue-50 hover:bg-opacity-50 rounded p-2 transition-colors"
            :class="{ 'ring-2 ring-blue-500 bg-blue-50': props.section?.selectedElement?.id === `feature-description-${index}` }"
            :data-element-id="`${props.section?.id}-feature-description-${index}`"
            @click.stop="handleElementClick('text', `feature-description-${index}`, $event)"
          >
            {{ feature.description }}
          </p>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-if="!props.section?.props?.features?.length" class="text-center py-12">
        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">기능이 없습니다</h3>
        <p class="text-gray-500">우측 패널에서 기능을 추가하세요</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Feature {
  title: string
  description: string
  icon: string
}

interface FeaturesProps {
  title?: string
  subtitle?: string
  features?: Feature[]
}

const props = defineProps<{
  section?: {
    id?: string
    props?: FeaturesProps
    selectedElement?: {
      type: string
      id: string
      data: any
    }
  }
  isSelected?: boolean
}>()

const emit = defineEmits<{
  click: []
  'element-click': [elementType: string, elementId: string, elementData: any, event: Event]
}>()

const handleElementClick = (elementType: string, elementId: string, event: Event) => {
  console.log('Features element clicked:', elementType, elementId);
  
  // 요소별 데이터 준비
  let elementData = {
    id: elementId,
    type: elementType,
    sectionId: props.section?.id || 'features',
    props: {}
  };

  if (elementType === 'text') {
    if (elementId === 'section-title') {
      elementData.props = {
        content: props.section?.props?.title || '주요 기능',
        type: 'h2',
        fontSize: 36,
        fontWeight: '700',
        color: '#111827',
        textAlign: 'center'
      };
    } else if (elementId === 'section-subtitle') {
      elementData.props = {
        content: props.section?.props?.subtitle || '우리 서비스의 핵심 기능들을 확인하세요',
        type: 'p',
        fontSize: 20,
        fontWeight: '400',
        color: '#6B7280',
        textAlign: 'center'
      };
    } else if (elementId.startsWith('feature-title-')) {
      const index = parseInt(elementId.replace('feature-title-', ''));
      const feature = (props.section?.props?.features || defaultFeatures)[index];
      elementData.props = {
        content: feature?.title || `기능 ${index + 1}`,
        type: 'h3',
        fontSize: 20,
        fontWeight: '600',
        color: '#111827',
        textAlign: 'center'
      };
    } else if (elementId.startsWith('feature-description-')) {
      const index = parseInt(elementId.replace('feature-description-', ''));
      const feature = (props.section?.props?.features || defaultFeatures)[index];
      elementData.props = {
        content: feature?.description || `설명 ${index + 1}`,
        type: 'p',
        fontSize: 16,
        fontWeight: '400',
        color: '#6B7280',
        textAlign: 'center'
      };
    }
  } else if (elementType === 'icon') {
    if (elementId.startsWith('feature-icon-')) {
      const index = parseInt(elementId.replace('feature-icon-', ''));
      const feature = (props.section?.props?.features || defaultFeatures)[index];
      elementData.props = {
        icon: feature?.icon || 'star',
        color: '#3B82F6',
        size: 48,
        backgroundColor: '#DBEAFE'
      };
    }
  }

  emit('element-click', elementType, elementId, elementData, event);
};

const defaultFeatures: Feature[] = [
  { title: '기능 1', description: '설명 1', icon: 'star' },
  { title: '기능 2', description: '설명 2', icon: 'heart' },
  { title: '기능 3', description: '설명 3', icon: 'lightning' }
]
</script>

<style scoped>
.features-section {
  min-height: 400px;
}

.feature-card {
  border: 1px solid transparent;
}

.feature-card:hover {
  border-color: #e5e7eb;
}
</style>

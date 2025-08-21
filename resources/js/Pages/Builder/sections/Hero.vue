<template>
  <div 
    class="hero-section relative overflow-hidden"
    :style="{ 
      paddingTop: `${props.section?.props?.py || 80}px`,
      paddingBottom: `${props.section?.props?.py || 80}px`
    }"
  >
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-100"></div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div 
        class="text-center"
        :class="{
          'text-left': props.section?.props?.align === 'left',
          'text-center': props.section?.props?.align === 'center',
          'text-right': props.section?.props?.align === 'right'
        }"
      >
        <!-- Main Title -->
        <h1 
          class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight cursor-pointer hover:bg-blue-50 hover:bg-opacity-50 rounded p-2 transition-colors"
          :class="{ 'ring-2 ring-blue-500 bg-blue-50': props.section?.selectedElement?.id === 'title' }"
          :data-element-id="`${props.section?.id}-title`"
          @click.stop="handleElementClick('text', 'title', $event)"
        >
          {{ props.section?.props?.title || '환영합니다' }}
        </h1>
        
        <!-- Subtitle -->
        <p 
          class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed cursor-pointer hover:bg-blue-50 hover:bg-opacity-50 rounded p-2 transition-colors"
          :class="{ 'ring-2 ring-blue-500 bg-blue-50': props.section?.selectedElement?.id === 'subtitle' }"
          :data-element-id="`${props.section?.id}-subtitle`"
          @click.stop="handleElementClick('text', 'subtitle', $event)"
        >
          {{ props.section?.props?.subtitle || '당신의 웹사이트에 오신 것을 환영합니다' }}
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <button 
            class="px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 relative group"
            :class="{ 'ring-2 ring-blue-500': props.section?.selectedElement?.id === 'primary-button' }"
            :data-element-id="`${props.section?.id}-primary-button`"
            @click.stop="handleElementClick('button', 'primary-button', $event)"
          >
            <span class="group-hover:opacity-75">시작하기</span>
            <div class="absolute inset-0 border-2 border-blue-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </button>
          <button 
            class="px-8 py-4 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 relative group"
            :class="{ 'ring-2 ring-blue-500': props.section?.selectedElement?.id === 'secondary-button' }"
            :data-element-id="`${props.section?.id}-secondary-button`"
            @click.stop="handleElementClick('button', 'secondary-button', $event)"
          >
            <span class="group-hover:opacity-75">자세히 보기</span>
            <div class="absolute inset-0 border-2 border-gray-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-200 rounded-full opacity-20 -translate-y-32 translate-x-32"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-200 rounded-full opacity-20 translate-y-24 -translate-x-24"></div>
  </div>
</template>

<script setup lang="ts">
interface HeroProps {
  title?: string
  subtitle?: string
  align?: 'left' | 'center' | 'right'
  py?: number
}

const props = defineProps<{
  section?: {
    id?: string
    props?: HeroProps
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
  console.log('Hero element clicked:', elementType, elementId);
  
  // 요소별 데이터 준비
  let elementData = {
    id: elementId,
    type: elementType,
    sectionId: props.section?.id || 'hero',
    props: {}
  };

  if (elementType === 'text') {
    if (elementId === 'title') {
      elementData.props = {
        content: props.section?.props?.title || '환영합니다',
        type: 'h1',
        fontSize: 48,
        fontWeight: '700',
        color: '#111827',
        textAlign: props.section?.props?.align || 'center'
      };
    } else if (elementId === 'subtitle') {
      elementData.props = {
        content: props.section?.props?.subtitle || '당신의 웹사이트에 오신 것을 환영합니다',
        type: 'p',
        fontSize: 20,
        fontWeight: '400',
        color: '#6B7280',
        textAlign: props.section?.props?.align || 'center'
      };
    }
  } else if (elementType === 'button') {
    if (elementId === 'primary-button') {
      elementData.props = {
        text: '시작하기',
        type: 'primary',
        size: 'large',
        backgroundColor: '#3B82F6',
        textColor: '#FFFFFF',
        borderRadius: 8,
        url: '#'
      };
    } else if (elementId === 'secondary-button') {
      elementData.props = {
        text: '자세히 보기',
        type: 'secondary',
        size: 'large',
        backgroundColor: '#FFFFFF',
        textColor: '#374151',
        borderRadius: 8,
        url: '#'
      };
    }
  }

  emit('element-click', elementType, elementId, elementData, event);
};
</script>

<style scoped>
.hero-section {
  min-height: 400px;
}

@media (max-width: 640px) {
  .hero-section {
    min-height: 300px;
  }
}
</style>

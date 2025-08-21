<template>
  <div class="cta-section py-20 bg-gradient-to-r from-blue-600 to-indigo-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-16 -translate-y-16"></div>
        <div class="absolute bottom-0 right-0 w-24 h-24 bg-white rounded-full translate-x-12 translate-y-12"></div>
      </div>
      
      <!-- Content -->
      <div class="relative z-10">
        <h2 
          class="text-3xl md:text-4xl font-bold text-white mb-6 leading-tight cursor-pointer hover:bg-white hover:bg-opacity-20 rounded p-2 transition-colors"
          :class="{ 'ring-2 ring-blue-500 bg-white bg-opacity-20': props.section?.selectedElement?.id === 'cta-title' }"
          :data-element-id="`${props.section?.id}-cta-title`"
          @click.stop="handleElementClick('text', 'cta-title', $event)"
        >
          {{ props.section?.props?.title || '지금 시작하세요' }}
        </h2>
        
        <p 
          class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto leading-relaxed cursor-pointer hover:bg-white hover:bg-opacity-20 rounded p-2 transition-colors"
          :class="{ 'ring-2 ring-blue-500 bg-white bg-opacity-20': props.section?.selectedElement?.id === 'cta-subtitle' }"
          :data-element-id="`${props.section?.id}-cta-subtitle`"
          @click.stop="handleElementClick('text', 'cta-subtitle', $event)"
        >
          {{ props.section?.props?.subtitle || '무료로 가입하고 모든 기능을 체험해보세요' }}
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <a
            :href="props.section?.props?.url || '#'"
            class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-block cursor-pointer relative group"
            :class="{ 'ring-2 ring-blue-500': props.section?.selectedElement?.id === 'cta-primary-button' }"
            :data-element-id="`${props.section?.id}-cta-primary-button`"
            @click.stop="handleElementClick('button', 'cta-primary-button', $event)"
          >
            <span class="group-hover:opacity-75">{{ props.section?.props?.button || '가입하기' }}</span>
            <div class="absolute inset-0 border-2 border-blue-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </a>
          
          <button 
            class="px-8 py-4 bg-transparent text-white font-semibold rounded-lg border-2 border-white hover:bg-white hover:text-blue-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 relative group"
            :class="{ 'ring-2 ring-blue-500': props.section?.selectedElement?.id === 'cta-secondary-button' }"
            :data-element-id="`${props.section?.id}-cta-secondary-button`"
            @click.stop="handleElementClick('button', 'cta-secondary-button', $event)"
          >
            <span class="group-hover:opacity-75">자세히 보기</span>
            <div class="absolute inset-0 border-2 border-white rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </button>
        </div>
        
        <!-- Additional Info -->
        <div class="mt-8 text-blue-100 text-sm">
          <p>이미 계정이 있으신가요? <a href="#" class="underline hover:text-white transition-colors">로그인</a></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface CtaProps {
  title?: string
  subtitle?: string
  button?: string
  url?: string
}

const props = defineProps<{
  section?: {
    id?: string
    props?: CtaProps
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
  console.log('CTA element clicked:', elementType, elementId);
  
  // 요소별 데이터 준비
  let elementData = {
    id: elementId,
    type: elementType,
    sectionId: props.section?.id || 'cta',
    props: {}
  };

  if (elementType === 'text') {
    if (elementId === 'cta-title') {
      elementData.props = {
        content: props.section?.props?.title || '지금 시작하세요',
        type: 'h2',
        fontSize: 36,
        fontWeight: '700',
        color: '#FFFFFF',
        textAlign: 'center'
      };
    } else if (elementId === 'cta-subtitle') {
      elementData.props = {
        content: props.section?.props?.subtitle || '무료로 가입하고 모든 기능을 체험해보세요',
        type: 'p',
        fontSize: 20,
        fontWeight: '400',
        color: '#DBEAFE',
        textAlign: 'center'
      };
    }
  } else if (elementType === 'button') {
    if (elementId === 'cta-primary-button') {
      elementData.props = {
        text: props.section?.props?.button || '가입하기',
        type: 'primary',
        size: 'large',
        backgroundColor: '#FFFFFF',
        textColor: '#2563EB',
        borderRadius: 8,
        url: props.section?.props?.url || '#'
      };
    } else if (elementId === 'cta-secondary-button') {
      elementData.props = {
        text: '자세히 보기',
        type: 'outline',
        size: 'large',
        backgroundColor: 'transparent',
        textColor: '#FFFFFF',
        borderRadius: 8,
        url: '#'
      };
    }
  }

  emit('element-click', elementType, elementId, elementData, event);
};
</script>

<style scoped>
.cta-section {
  min-height: 300px;
  position: relative;
}

@media (max-width: 640px) {
  .cta-section {
    min-height: 250px;
  }
}
</style>

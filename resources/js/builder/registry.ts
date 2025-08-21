import type { Section } from './schema/types';
import Hero from '@/Pages/Builder/sections/Hero.vue';
import Features from '@/Pages/Builder/sections/Features.vue';
import Cta from '@/Pages/Builder/sections/Cta.vue';

export const sectionMap: Record<Section['type'], any> = {
  hero: Hero,
  features: Features,
  cta: Cta,
};

export const sectionDefaults: Record<Section['type'], any> = {
  hero: { title: '섹션 제목', subtitle: '부제', align: 'center', py: 60 },
  features: { cols: 3, items: [ { title: '특징 A', text: '설명' }, { title: '특징 B', text: '설명' }, { title: '특징 C', text: '설명' } ] },
  cta: { text: '지금 시작해 보세요', button: { label: '시작하기', href: '#' } },
};

export const sectionRegistry: Record<string, { name: string; icon: string; component: any }> = {
  hero: {
    name: '히어로 섹션',
    icon: 'fas fa-star',
    component: Hero
  },
  features: {
    name: '특징 섹션',
    icon: 'fas fa-th-large',
    component: Features
  },
  cta: {
    name: 'CTA 섹션',
    icon: 'fas fa-bullhorn',
    component: Cta
  }
};

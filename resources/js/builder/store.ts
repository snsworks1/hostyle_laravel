import { defineStore } from 'pinia';
import type { PageSchema, Section } from './schema/types';

export interface BuilderPage {
  id: number;
  server_id: number;
  parent_id: number;
  type: string;
  name: string;
  slug: string;
  seq: number;
  read_level: string;
  menu_hide: boolean;
  hide_header: boolean;
  hide_footer: boolean;
  is_main: boolean;
  page_schema: PageSchema;
  site_tokens: any;
  is_published: boolean;
  published_at?: string;
  seo_title?: string;
  seo_keywords?: string;
  seo_description?: string;
  seo_search_ban: boolean;
  children?: BuilderPage[];
}

export const useBuilder = defineStore('builder', {
  state: () => ({
    loading: false,
    serverId: null as number | null,
    currentPageId: null as number | null,
    pages: [] as BuilderPage[],
    currentPage: null as BuilderPage | null,
    schema: { sections: [] } as PageSchema,
    tokens: { 
      brandColor: '#111111', 
      contentWidth: '1080px', 
      fontBase: 'Noto Sans KR, sans-serif', 
      radius: '12px' 
    },
    selectedIndex: -1,
    history: [] as PageSchema[],
    future: [] as PageSchema[],
  }),
  
  getters: {
    selected: (s) => s.schema.sections[s.selectedIndex] || null,
    rootPages: (s) => s.pages.filter(p => p.parent_id === 0 || !p.parent_id),
    mainPage: (s) => s.pages.find(p => p.is_main),
    selectedSection: (s) => s.schema.sections[s.selectedIndex] || null,
    canUndo: (s) => s.history.length > 0,
    canRedo: (s) => s.future.length > 0,
  },
  
  actions: {
    setIds(serverId: number, pageId?: number) {
      this.serverId = serverId;
      this.currentPageId = pageId || null;
    },
    
    async loadPages() {
      if (!this.serverId) return;
      
      console.log('loadPages 시작 - serverId:', this.serverId);
      this.loading = true;
      try {
        const res = await fetch(`/server/${this.serverId}/builder/api/pages`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
        });
        
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const json = await res.json();
        console.log('페이지 목록 로드 성공:', json);
        
        this.pages = this.buildTree(json);
        console.log('트리 구조 생성 완료:', this.pages);
      } catch (error) {
        console.error('Failed to load pages:', error);
      }
      this.loading = false;
    },
    
    async loadPage(pageId: number) {
      if (!this.serverId) return;
      
      this.loading = true;
      try {
        const res = await fetch(`/server/${this.serverId}/builder/api/pages/${pageId}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
        });
        
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const json = await res.json();
        this.currentPage = json;
        this.currentPageId = pageId;
        this.schema = json.page_schema || { sections: [] };
        this.tokens = json.site_tokens || this.tokens;
      } catch (error) {
        console.error('Failed to load page:', error);
      }
      this.loading = false;
    },
    
    selectPage(id: number) {
      this.currentPageId = id;
      this.loadPage(id);
    },
    
    editPage(id: number) {
      // 페이지 설정 모달 열기
      console.log('Edit page:', id);
    },
    
    duplicatePage(id: number) {
      // 페이지 복제
      console.log('Duplicate page:', id);
    },
    
    async deletePage(id: number) {
      if (!this.serverId) return;
      
      try {
        await fetch(`/api/builder/${this.serverId}/pages/${id}`, { method: 'DELETE' });
        await this.loadPages();
      } catch (error) {
        console.error('Failed to delete page:', error);
      }
    },
    
    async reorderPages(oldIndex: number, newIndex: number) {
      if (!this.serverId) return;
      
      try {
        const pages = this.rootPages;
        const [movedPage] = pages.splice(oldIndex, 1);
        pages.splice(newIndex, 0, movedPage);
        
        // 순서 업데이트
        const updates = pages.map((page, index) => ({
          id: page.id,
          seq: index
        }));
        
        await fetch(`/api/builder/${this.serverId}/pages/reorder`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ updates })
        });
        
        await this.loadPages();
      } catch (error) {
        console.error('Failed to reorder pages:', error);
      }
    },
    
    buildTree(pages: BuilderPage[]): BuilderPage[] {
      const pageMap = new Map();
      const tree: BuilderPage[] = [];
      
      // 모든 페이지를 맵에 저장
      pages.forEach(page => {
        pageMap.set(page.id, { ...page, children: [] });
      });
      
      // 트리 구조 생성
      pages.forEach(page => {
        const node = pageMap.get(page.id);
        if (page.parent_id === 0 || !page.parent_id) {
          tree.push(node);
        } else {
          const parent = pageMap.get(page.parent_id);
          if (parent) {
            parent.children.push(node);
          }
        }
      });
      
      // 각 레벨에서 seq로 정렬
      const sortTree = (nodes: BuilderPage[]) => {
        nodes.sort((a, b) => a.seq - b.seq);
        nodes.forEach(node => {
          if (node.children && node.children.length > 0) {
            sortTree(node.children);
          }
        });
      };
      
      sortTree(tree);
      return tree;
    },
    
    pushHistory() { 
      this.history.push(JSON.parse(JSON.stringify(this.schema))); 
      this.future = []; 
    },
    
    undo() { 
      if (this.history.length) { 
        this.future.push(this.schema); 
        this.schema = this.history.pop()!; 
      } 
    },
    
    redo() { 
      if (this.future.length) { 
        this.history.push(this.schema); 
        this.schema = this.future.pop()!; 
      } 
    },
    
    select(i: number) { this.selectedIndex = i; },
    
    async add(sectionType: string) { 
      this.pushHistory(); 
      const section = {
        id: Date.now().toString(),
        type: sectionType,
        props: {}
      } as unknown as Section;
      this.schema.sections.push(section); 
      this.selectedIndex = this.schema.sections.length - 1; 
      
      // 섹션 추가 후 자동 저장
      if (this.serverId && this.currentPageId) {
        try {
          await this.save();
          console.log('섹션 추가 후 자동 저장 완료');
        } catch (error) {
          console.error('섹션 추가 후 자동 저장 실패:', error);
        }
      }
    },
    
    remove(i: number) { 
      this.pushHistory(); 
      this.schema.sections.splice(i,1); 
      this.selectedIndex = -1; 
    },
    
    move(oldIdx: number, newIdx: number) { 
      this.pushHistory(); 
      const [it] = this.schema.sections.splice(oldIdx,1); 
      this.schema.sections.splice(newIdx,0,it); 
      this.selectedIndex = newIdx; 
    },
    
    updateSelected(props: any) { 
      if (this.selected) { 
        this.pushHistory(); 
        (this.selected as any).props = { ...(this.selected as any).props, ...props }; 
      } 
    },
    
    updatePageSchema(pageId: number, schema: PageSchema) {
      this.pushHistory();
      this.schema = schema;
      if (this.currentPage) {
        this.currentPage.page_schema = schema;
      }
    },
    
    updateSectionProps(sectionId: string, props: any) {
      this.pushHistory();
      const section = this.schema.sections.find(s => s.id === sectionId);
      if (section) {
        (section as any).props = { ...(section as any).props, ...props };
      }
    },
    
    async save() {
      if (!this.serverId || !this.currentPageId) return;
      
      try {
        // CSRF 토큰 가져오기
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        await fetch(`/server/${this.serverId}/builder/api/pages/${this.currentPageId}`, { 
          method: 'POST', 
          headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token || ''
          },
          credentials: 'same-origin',
          body: JSON.stringify({ 
            page_schema: this.schema, 
            site_tokens: this.tokens 
          }) 
        });
      } catch (error) {
        console.error('Failed to save:', error);
      }
    },
    
    async publish() {
      if (!this.serverId || !this.currentPageId) return;
      
      try {
        await this.save();
        
        // CSRF 토큰 가져오기
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        await fetch(`/server/${this.serverId}/builder/api/pages/${this.currentPageId}/publish`, { 
          method: 'POST',
          headers: { 
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token || ''
          },
          credentials: 'same-origin'
        });
      } catch (error) {
        console.error('Failed to publish:', error);
      }
    },

    addPage(page: BuilderPage) {
      this.pages.push(page);
      // 페이지 추가 후 API로 저장
      if (this.serverId) {
        this.savePageToAPI(page);
      }
    },

    removePage(pageId: number) {
      const index = this.pages.findIndex(p => p.id === pageId);
      if (index !== -1) {
        this.pages.splice(index, 1);
        // 페이지 삭제 후 API로 처리
        if (this.serverId) {
          this.deletePageFromAPI(pageId);
        }
      }
    },

    async savePageToAPI(page: BuilderPage) {
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        await fetch(`/server/${this.serverId}/builder/api/pages`, { 
          method: 'POST', 
          headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token || ''
          },
          credentials: 'same-origin',
          body: JSON.stringify(page) 
        });
      } catch (error) {
        console.error('Failed to save page:', error);
      }
    },

    async deletePageFromAPI(pageId: number) {
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        await fetch(`/server/${this.serverId}/builder/api/pages/${pageId}`, { 
          method: 'DELETE',
          headers: { 
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token || ''
          },
          credentials: 'same-origin'
        });
      } catch (error) {
        console.error('Failed to delete page:', error);
      }
    }
  }
});

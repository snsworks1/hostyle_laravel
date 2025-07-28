@php
    $hide = false;
    foreach ($popups as $popup) {
        if (isset($_COOKIE['hide_popup_' . $popup['id']])) {
            $hide = true;
            break;
        }
    }
@endphp

@if (!$hide && count($popups))
    <style>
        #popup-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.75rem;
        }
    </style>

    <div id="popup-wrapper" class="fixed inset-0 z-50 bg-black bg-opacity-40 flex justify-center items-center px-4">
        <div class="relative w-full max-w-screen-sm sm:max-w-[500px] md:max-w-[600px] lg:max-w-[800px] aspect-square bg-white rounded-xl overflow-hidden shadow-lg"
             x-data='popupSlider(@json($popups))'
             x-init="init">

            <!-- 슬라이드 이미지 영역 -->
            <template x-for="(popup, index) in popups" :key="popup.id">
                <div x-show="currentIndex === index" class="w-full h-full">
                    <div x-html="popup.content"></div>
                </div>
            </template>

            <!-- 화살표 -->
            <template x-if="popups.length > 1">
                <div class="absolute top-1/2 left-0 right-0 flex justify-between px-4 -translate-y-1/2">
                    <button @click="prev" class="text-3xl text-white hover:text-blue-300">&#8592;</button>
                    <button @click="next" class="text-3xl text-white hover:text-blue-300">&#8594;</button>
                </div>
            </template>

            <!-- 인디케이터 -->
            <template x-if="popups.length > 1">
                <div class="absolute bottom-5 left-0 right-0 flex justify-center gap-2">
                    <template x-for="(popup, index) in popups" :key="popup.id">
                        <div :class="{
                                'bg-blue-500': currentIndex === index,
                                'bg-white/50': currentIndex !== index
                            }"
                            class="w-2.5 h-2.5 rounded-full transition-all duration-300"></div>
                    </template>
                </div>
            </template>

            <!-- 하단 버튼 -->
            <div class="absolute top-2 right-2 flex items-center gap-2 text-xs sm:text-sm" x-data="{ hideChecked: false }">
                <label class="text-white bg-black/50 px-2 py-1 rounded cursor-pointer">
                    <input type="checkbox" class="mr-1" x-model="hideChecked">1일간 보지 않기
                </label>
                <button @click="() => {
                    if (hideChecked) {
                        const date = new Date();
                        date.setTime(date.getTime() + (1 * 24 * 60 * 60 * 1000));
                        document.cookie = `hide_popup_${popups[currentIndex].id}=1; expires=${date.toUTCString()}; path=/`;
                    }
                    close();
                }"
                class="text-white bg-black/50 px-3 py-1 rounded hover:bg-black">닫기</button>
            </div>
        </div>
    </div>

    <script>
        function popupSlider(popups) {
            return {
                popups,
                currentIndex: 0,
                interval: null,
                init() {
                    if (this.popups.length > 1) {
                        this.startAutoSlide();
                    }
                },
                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.popups.length;
                },
                prev() {
                    this.currentIndex = (this.currentIndex - 1 + this.popups.length) % this.popups.length;
                },
                startAutoSlide() {
                    this.interval = setInterval(() => {
                        this.next();
                    }, 5000);
                },
                stopAutoSlide() {
                    clearInterval(this.interval);
                },
                close() {
                    document.getElementById('popup-wrapper')?.remove();
                }
            }
        }
    </script>
@endif

<!DOCTYPE html>
<html lang="ko">
<head>
    
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/IconOnly_Transparent.png') }}">
    <meta charset="UTF-8">
    <meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if (!empty($post?->meta_title))
        <meta name="title" content="{{ $post->meta_title }}">
    @endif
    @if (!empty($post?->meta_description))
        <meta name="description" content="{{ $post->meta_description }}">
    @endif
    <title>{{ $title ?? 'Hostyle - 블로그' }}</title>
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white text-gray-900">

<header class="py-4 px-4 sm:px-6 border-b bg-white shadow-sm">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-0">
        <a href="/blog" class="text-lg sm:text-xl font-bold text-blue-600 whitespace-nowrap">Hostyle 블로그</a>

        <div class="flex gap-1 sm:gap-2 text-xs sm:text-sm">
            <a href="/" class="px-2 sm:px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-800 whitespace-nowrap">
                🏠 메인으로
            </a>
            <a href="/dashboard" class="px-2 sm:px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded whitespace-nowrap">
                📊 대시보드
            </a>
        </div>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6 sm:py-8" x-data="{ showCategory: false }">
    <div class="md:hidden mb-4">
    <button @click="showCategory = !showCategory"
            class="w-full px-4 py-2 bg-gray-100 text-sm text-gray-700 rounded hover:bg-gray-200"
            x-text="showCategory ? '📁 카테고리 숨기기' : '📁 카테고리 보기'">
    </button>
</div>

    <div class="flex flex-col md:flex-row gap-6 md:gap-8">
        {{-- 사이드바 --}}
        <aside class="w-full md:w-48 shrink-0 space-y-2"
               x-show="showCategory || window.innerWidth >= 768"
               x-transition
               x-cloak>
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-2">📁 카테고리</h2>
            <a href="{{ route('blog.index') }}"
               class="block px-3 py-2 rounded hover:bg-blue-50 {{ request('category') ? '' : 'bg-blue-100 font-bold text-blue-600' }}">
                전체 보기
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat->id]) }}"
                   class="flex justify-between items-center px-4 py-2 rounded hover:bg-blue-50 {{ request('category') == $cat->id ? 'bg-blue-100 font-semibold text-blue-700' : '' }}">
                    <span class="truncate">
                        <i class="fa-solid fa-folder mr-2 text-gray-400"></i>{{ $cat->name }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $cat->posts()->count() }}</span>
                </a>
            @endforeach
        </aside>

        {{-- 본문 --}}
        <section class="flex-1">
            @yield('content')
        </section>
    </div>
</main>

<footer class="py-6 text-center text-xs sm:text-sm text-gray-500">
    © {{ date('Y') }} Hostyle. All rights reserved.
</footer>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-Y7BBE8FQ2H"></script>

<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-Y7BBE8FQ2H');
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  },
  "headline": "{{ $post->title }}",
  "description": "{{ $post->meta_description ?? Str::limit(strip_tags($post->body), 160) }}",
  "image": "{{ !empty($post->thumbnail_url) ? asset('storage/' . $post->thumbnail_url) : asset('images/default-og.jpg') }}",
  "datePublished": "{{ $post->created_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": {
    "@type": "Organization",
    "name": "Hostyle"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Hostyle",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  }
}
</script>
<!-- Smartlog -->
    <script type="text/javascript">
        var hpt_info={'_account':'UHPT-32552', '_server': 'a29'};
    </script>
    <script language="javascript" src="//cdn.smlog.co.kr/core/smart.js" charset="utf-8"></script>
    <noscript><img src="//a29.smlog.co.kr/smart_bda.php?_account=32552" style="display:none;width:0;height:0;" border="0"/></noscript>
</body>
</html>

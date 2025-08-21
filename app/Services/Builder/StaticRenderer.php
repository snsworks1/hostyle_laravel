<?php

namespace App\Services\Builder;

use App\Models\Server;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaticRenderer
{
    protected PathNormalizer $pathNormalizer;
    
    public function __construct(PathNormalizer $pathNormalizer)
    {
        $this->pathNormalizer = $pathNormalizer;
    }

    /**
     * 정적 사이트 생성
     */
    public function build(Server $server, array $pages, array $siteTokens): string
    {
        // 임시 디렉터리 생성
        $buildId = Str::uuid();
        $buildPath = config('builder.build.temp_dir') . '/' . $buildId;
        
        // 빌드 디렉터리 생성
        Storage::makeDirectory($buildPath);
        
        try {
            // 메인 페이지 생성
            $this->createMainPage($buildPath, $pages, $siteTokens);
            
            // 추가 페이지들 생성
            $this->createAdditionalPages($buildPath, $pages, $siteTokens);
            
            // 공통 에셋 생성
            $this->createCommonAssets($buildPath, $siteTokens);
            
            // .htaccess 생성 (보안 설정)
            $this->createHtaccess($buildPath);
            
            return $buildPath;
            
        } catch (\Exception $e) {
            // 실패 시 임시 디렉터리 정리
            Storage::deleteDirectory($buildPath);
            throw $e;
        }
    }

    /**
     * 메인 페이지 생성
     */
    protected function createMainPage(string $buildPath, array $pages, array $siteTokens): void
    {
        $mainPage = collect($pages)->firstWhere('is_main', true);
        
        if (!$mainPage) {
            throw new \Exception('메인 페이지를 찾을 수 없습니다.');
        }
        
        $html = $this->renderPage($mainPage, $siteTokens);
        $html = $this->pathNormalizer->normalizeHtml($html);
        $html = $this->pathNormalizer->addExternalUrlWarning($html);
        
        Storage::put($buildPath . '/index.html', $html);
    }

    /**
     * 추가 페이지들 생성
     */
    protected function createAdditionalPages(string $buildPath, array $pages, array $siteTokens): void
    {
        $additionalPages = collect($pages)->where('is_main', false)->where('type', 'page');
        
        foreach ($additionalPages as $page) {
            $html = $this->renderPage($page, $siteTokens);
            $html = $this->pathNormalizer->normalizeHtml($html);
            $html = $this->pathNormalizer->addExternalUrlWarning($html);
            
            // 디렉터리형 URL 생성 (/about -> /about/index.html)
            $pagePath = $buildPath . '/' . $page['slug'];
            Storage::makeDirectory($pagePath);
            Storage::put($pagePath . '/index.html', $html);
        }
    }

    /**
     * 공통 에셋 생성
     */
    protected function createCommonAssets(string $buildPath, array $siteTokens): void
    {
        // CSS 생성
        $css = $this->renderCss($siteTokens);
        $css = $this->pathNormalizer->normalizeCss($css);
        
        Storage::makeDirectory($buildPath . '/assets');
        Storage::put($buildPath . '/assets/app.css', $css);
        
        // JavaScript 생성
        $js = $this->renderJs($siteTokens);
        $js = $this->pathNormalizer->normalizeJs($js);
        
        Storage::put($buildPath . '/assets/app.js', $js);
        
        // 업로드 디렉터리 생성
        Storage::makeDirectory($buildPath . '/assets/uploads');
        Storage::makeDirectory($buildPath . '/assets/uploads/' . date('Y'));
        Storage::makeDirectory($buildPath . '/assets/uploads/' . date('Y') . '/' . date('m'));
    }

    /**
     * 페이지 렌더링
     */
    protected function renderPage(array $page, array $siteTokens): string
    {
        $schema = $page['page_schema'] ?? ['sections' => []];
        
        $html = '<!DOCTYPE html>' . "\n";
        $html .= '<html lang="ko">' . "\n";
        $html .= '<head>' . "\n";
        $html .= '    <meta charset="UTF-8">' . "\n";
        $html .= '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
        $html .= '    <title>' . htmlspecialchars($page['name']) . '</title>' . "\n";
        $html .= '    <link rel="stylesheet" href="/assets/app.css">' . "\n";
        $html .= '    <script src="/assets/app.js" defer></script>' . "\n";
        $html .= '</head>' . "\n";
        $html .= '<body>' . "\n";
        
        // 섹션 렌더링
        foreach ($schema['sections'] ?? [] as $section) {
            $html .= $this->renderSection($section, $siteTokens);
        }
        
        $html .= '</body>' . "\n";
        $html .= '</html>';
        
        return $html;
    }

    /**
     * 섹션 렌더링
     */
    protected function renderSection(array $section, array $siteTokens): string
    {
        $type = $section['type'];
        $props = $section['props'] ?? [];
        
        return match($type) {
            'hero' => $this->renderHeroSection($props, $siteTokens),
            'features' => $this->renderFeaturesSection($props, $siteTokens),
            'cta' => $this->renderCtaSection($props, $siteTokens),
            default => '<!-- Unknown section type: ' . $type . ' -->'
        };
    }

    /**
     * Hero 섹션 렌더링
     */
    protected function renderHeroSection(array $props, array $siteTokens): string
    {
        $title = $props['title'] ?? '제목';
        $subtitle = $props['subtitle'] ?? '부제목';
        $align = $props['align'] ?? 'center';
        $py = $props['py'] ?? 80;
        
        return <<<HTML
        <section class="hero" style="padding: {$py}px 0; text-align: {$align};">
            <div class="container">
                <h1 class="hero-title">{$title}</h1>
                <p class="hero-subtitle">{$subtitle}</p>
            </div>
        </section>
        HTML;
    }

    /**
     * Features 섹션 렌더링
     */
    protected function renderFeaturesSection(array $props, array $siteTokens): string
    {
        $cols = $props['cols'] ?? 3;
        $items = $props['items'] ?? [];
        
        $html = '<section class="features">';
        $html .= '<div class="container">';
        $html .= '<div class="features-grid" style="grid-template-columns: repeat(' . $cols . ', 1fr);">';
        
        foreach ($items as $item) {
            $title = $item['title'] ?? '특징';
            $text = $item['text'] ?? '설명';
            
            $html .= '<div class="feature-item">';
            $html .= '<h3>' . htmlspecialchars($title) . '</h3>';
            $html .= '<p>' . htmlspecialchars($text) . '</p>';
            $html .= '</div>';
        }
        
        $html .= '</div></div></section>';
        
        return $html;
    }

    /**
     * CTA 섹션 렌더링
     */
    protected function renderCtaSection(array $props, array $siteTokens): string
    {
        $text = $props['text'] ?? '지금 시작하세요';
        $button = $props['button'] ?? ['label' => '시작하기', 'href' => '#'];
        
        return <<<HTML
        <section class="cta">
            <div class="container">
                <p class="cta-text">{$text}</p>
                <a href="{$button['href']}" class="cta-button">{$button['label']}</a>
            </div>
        </section>
        HTML;
    }

    /**
     * CSS 렌더링
     */
    protected function renderCss(array $siteTokens): string
    {
        $brandColor = $siteTokens['brandColor'] ?? '#111111';
        $contentWidth = $siteTokens['contentWidth'] ?? '1080px';
        $fontBase = $siteTokens['fontBase'] ?? 'Noto Sans KR, sans-serif';
        $radius = $siteTokens['radius'] ?? '12px';
        
        return <<<CSS
        :root {
            --brand-color: {$brandColor};
            --content-width: {$contentWidth};
            --font-base: {$fontBase};
            --radius: {$radius};
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-base);
            line-height: 1.6;
        }
        
        .container {
            max-width: var(--content-width);
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .hero {
            background: linear-gradient(135deg, var(--brand-color), #333);
            color: white;
        }
        
        .hero-title {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
        }
        
        .features {
            padding: 80px 0;
        }
        
        .features-grid {
            display: grid;
            gap: 2rem;
        }
        
        .feature-item {
            text-align: center;
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .cta {
            background: var(--brand-color);
            color: white;
            text-align: center;
            padding: 80px 0;
        }
        
        .cta-button {
            display: inline-block;
            background: white;
            color: var(--brand-color);
            padding: 1rem 2rem;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: bold;
            margin-top: 1rem;
        }
        CSS;
    }

    /**
     * JavaScript 렌더링
     */
    protected function renderJs(array $siteTokens): string
    {
        return <<<JS
        // 기본 JavaScript 기능
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Builder site loaded');
        });
        JS;
    }

    /**
     * .htaccess 파일 생성
     */
    protected function createHtaccess(string $buildPath): void
    {
        $htaccess = <<<HTACCESS
        # 보안 설정
        <Files "*.php">
            Order Deny,Allow
            Deny from all
        </Files>
        
        <Files "*.phar">
            Order Deny,Allow
            Deny from all
        </Files>
        
        <Files "*.cgi">
            Order Deny,Allow
            Deny from all
        </Files>
        
        # 업로드 폴더 실행 차단
        <Directory "assets/uploads">
            <Files "*.php">
                Order Deny,Allow
                Deny from all
            </Files>
        </Directory>
        
        # 정적 파일 캐싱
        <IfModule mod_expires.c>
            ExpiresActive On
            ExpiresByType text/css "access plus 1 year"
            ExpiresByType application/javascript "access plus 1 year"
            ExpiresByType image/png "access plus 1 year"
            ExpiresByType image/jpg "access plus 1 year"
            ExpiresByType image/jpeg "access plus 1 year"
            ExpiresByType image/gif "access plus 1 year"
            ExpiresByType image/webp "access plus 1 year"
        </IfModule>
        HTACCESS;
        
        Storage::put($buildPath . '/.htaccess', $htaccess);
    }
}

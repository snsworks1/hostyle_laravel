<?php

namespace App\Services\Builder;

class PathNormalizer
{
    /**
     * HTML 내의 경로를 루트 상대경로로 정규화
     */
    public function normalizeHtml(string $html): string
    {
        // src 속성 정규화
        $html = preg_replace(
            '/src=["\'](https?:\/\/[^"\']+)["\']/i',
            'src="/assets/external"',
            $html
        );
        
        // href 속성 정규화 (외부 링크는 유지, 내부 링크는 상대경로로)
        $html = preg_replace_callback(
            '/href=["\']([^"\']+)["\']/i',
            function ($matches) {
                $url = $matches[1];
                
                // 외부 링크는 유지
                if (preg_match('/^https?:\/\//', $url)) {
                    return $matches[0];
                }
                
                // 내부 링크를 상대경로로 변환
                if (str_starts_with($url, '/')) {
                    return 'href="' . $url . '"';
                }
                
                // 상대경로는 그대로 유지
                return $matches[0];
            },
            $html
        );
        
        // url() CSS 함수 내의 경로 정규화
        $html = preg_replace(
            '/url\(["\']?(https?:\/\/[^"\')\s]+)["\']?\)/i',
            'url("/assets/external")',
            $html
        );
        
        // 이중 슬래시 제거
        $html = preg_replace('/\/\//', '/', $html);
        
        return $html;
    }

    /**
     * CSS 내의 경로를 루트 상대경로로 정규화
     */
    public function normalizeCss(string $css): string
    {
        // url() 함수 내의 절대 도메인 제거
        $css = preg_replace(
            '/url\(["\']?(https?:\/\/[^"\')\s]+)["\']?\)/i',
            'url("/assets/external")',
            $css
        );
        
        // @import 내의 절대 도메인 제거
        $css = preg_replace(
            '/@import\s+["\'](https?:\/\/[^"\']+)["\']/i',
            '@import "/assets/external"',
            $css
        );
        
        // 이중 슬래시 제거
        $css = preg_replace('/\/\//', '/', $css);
        
        return $css;
    }

    /**
     * JavaScript 내의 경로를 루트 상대경로로 정규화
     */
    public function normalizeJs(string $js): string
    {
        // fetch, XMLHttpRequest 등의 URL 정규화
        $js = preg_replace(
            '/(fetch|XMLHttpRequest\.open)\s*\(\s*["\'](https?:\/\/[^"\']+)["\']/i',
            '$1("/assets/external"',
            $js
        );
        
        // 이미지 소스 등 정규화
        $js = preg_replace(
            '/\.src\s*=\s*["\'](https?:\/\/[^"\']+)["\']/i',
            '.src = "/assets/external"',
            $js
        );
        
        return $js;
    }

    /**
     * 모든 컨텐츠 타입에 대해 경로 정규화
     */
    public function normalizeAll(string $content, string $type = 'html'): string
    {
        return match($type) {
            'css' => $this->normalizeCss($content),
            'js' => $this->normalizeJs($content),
            default => $this->normalizeHtml($content),
        };
    }

    /**
     * 내부 링크를 디렉터리형으로 변환
     */
    public function normalizeInternalLinks(string $html): string
    {
        // /about -> /about/ 변환
        $html = preg_replace(
            '/href=["\']\/([^\/"\'#]+)(?!\/)["\']/i',
            'href="/$1/"',
            $html
        );
        
        return $html;
    }

    /**
     * 외부 URL 경고를 위한 주석 추가
     */
    public function addExternalUrlWarning(string $html): string
    {
        // 외부 URL이 있는 경우 주석 추가
        if (preg_match('/https?:\/\//', $html)) {
            $html = "<!-- External URLs detected. Consider using internal assets for better performance. -->\n" . $html;
        }
        
        return $html;
    }
}


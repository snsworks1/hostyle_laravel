<?php

namespace App\Services\Builder;

use Illuminate\Support\Facades\View;

class Renderer
{
    /** 미리보기용 */
    public function preview(array $schema, array $tokens = []): array
    {
        return $this->render($schema, $tokens, 'publish.page');
    }

    /** 퍼블리시용 */
    public function publish(array $schema, array $tokens = []): array
    {
        return $this->render($schema, $tokens, 'publish.page');
    }

    private function render(array $schema, array $tokens, string $view): array
    {
        $html = View::make($view, ['schema' => $schema, 'tokens' => $tokens])->render();
        $css = View::make('publish.styles', ['tokens' => $tokens])->render();
        return ['html' => $html, 'css' => $css];
    }
}


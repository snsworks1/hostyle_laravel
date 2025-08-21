<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('builder_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable()->default(0); // 계층 구조
            $table->string('type')->default('page'); // page, link, category, popup, data
            $table->string('name'); // 페이지 이름
            $table->string('slug')->unique();
            $table->integer('seq')->default(0); // 정렬 순서
            $table->string('read_level')->default('all'); // all, login, admin, group
            $table->boolean('menu_hide')->default(false); // 메뉴에 숨김
            $table->boolean('hide_header')->default(false); // 헤더 숨김
            $table->boolean('hide_footer')->default(false); // 푸터 숨김
            $table->boolean('is_main')->default(false); // 메인 페이지 여부
            $table->json('page_schema')->nullable(); // JSON 스키마
            $table->json('site_tokens')->nullable(); // 색상/타이포/폭 등
            $table->longText('preview_html')->nullable();
            $table->longText('preview_css')->nullable();
            $table->longText('published_html')->nullable();
            $table->longText('published_css')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // SEO 필드들
            $table->string('seo_title')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('seo_search_ban')->default(false); // 검색 제외
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('builder_pages');
    }
};

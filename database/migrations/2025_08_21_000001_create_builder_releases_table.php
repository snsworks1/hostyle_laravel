<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('builder_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
            $table->string('version', 14)->comment('YYYYMMDDHHMMSS 형식');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('notes')->nullable()->comment('배포 노트 또는 에러 메시지');
            $table->json('metadata')->nullable()->comment('배포 메타데이터 (파일 수, 크기 등)');
            $table->timestamp('deployed_at')->nullable()->comment('배포 완료 시간');
            $table->timestamps();
            
            // 인덱스
            $table->index(['server_id', 'status']);
            $table->unique(['server_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('builder_releases');
    }
};


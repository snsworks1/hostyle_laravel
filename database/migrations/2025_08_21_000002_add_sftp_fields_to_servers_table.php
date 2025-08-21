<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            // SFTP 관련 필드들은 cyberpanel_servers 테이블에서 가져올 예정이므로 제거
            // sftp_host, sftp_port, sftp_username, sftp_password, sftp_key_path
            // docroot, home_path도 제거 (cyberpanel에서 동적으로 생성)
            
            // 빌더 설정만 추가
            $table->boolean('builder_enabled')->default(false)->after('status');
            $table->json('builder_settings')->nullable()->after('builder_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn([
                'builder_enabled', 'builder_settings'
            ]);
        });
    }
};

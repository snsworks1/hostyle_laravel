<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            // 미사용 SFTP 필드들 제거
            $table->dropColumn([
                'sftp_host',
                'sftp_port', 
                'sftp_username',
                'sftp_password',
                'sftp_key_path',
                'docroot',
                'home_path'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            // 롤백 시 필드들 다시 추가
            $table->string('sftp_host')->nullable()->after('status');
            $table->integer('sftp_port')->default(22)->after('sftp_host');
            $table->string('sftp_username')->nullable()->after('sftp_port');
            $table->text('sftp_password')->nullable()->after('sftp_username');
            $table->string('sftp_key_path')->nullable()->after('sftp_password');
            $table->string('docroot')->nullable()->after('sftp_key_path');
            $table->string('home_path')->nullable()->after('docroot');
        });
    }
};

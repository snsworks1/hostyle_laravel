<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecurityEventsTable extends Migration
{
    public function up()
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // 'token_invalid', 'token_missing', 'unauthorized_access', 'domain_tampering'
            $table->string('domain');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->string('source')->default('cyberpanel'); // 'cyberpanel', 'laravel'
            $table->string('severity')->default('medium'); // 'low', 'medium', 'high', 'critical'
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // 인덱스
            $table->index(['event_type', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['domain', 'created_at']);
            $table->index(['severity', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('security_events');
    }
}

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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('site_name');
            $table->string('domain')->unique();
            $table->string('fqdn')->nullable();
            $table->unsignedBigInteger('cyberpanel_server_id')->nullable();
            $table->string('region');
            $table->string('platform');
            $table->string('plan');
            $table->integer('months')->default(1);
            $table->timestamp('expires_at')->nullable();
            $table->integer('total_months')->default(0);
            $table->decimal('total_paid_amount', 10, 2)->default(0);
            $table->enum('status', ['active', 'suspended', 'cancelled'])->default('active');
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->foreign('cyberpanel_server_id')->references('id')->on('cyberpanel_servers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};

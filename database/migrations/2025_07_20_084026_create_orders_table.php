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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique(); // 토스 주문번호
            $table->enum('order_type', ['purchase', 'extension']); // 구매/연장
            $table->decimal('amount', 10, 2); // 결제 금액
            $table->integer('months'); // 구매 개월수
            $table->string('plan'); // 요금제
            $table->enum('status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->timestamp('expires_at'); // 해당 주문의 만료일
            $table->json('payment_data')->nullable(); // 토스 결제 응답 데이터
            $table->timestamps();
            $table->index(['server_id', 'order_type']);
            $table->index(['order_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

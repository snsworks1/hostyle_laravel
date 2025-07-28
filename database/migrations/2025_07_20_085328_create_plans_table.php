<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 예: free, starter, business, enterprise
            $table->string('label'); // 사용자 표시명
            $table->integer('price')->default(0); // 월 요금 (원)
            $table->integer('trial_days')->nullable(); // 무료 체험일수
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

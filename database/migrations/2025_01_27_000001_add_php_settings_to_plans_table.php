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
        Schema::table('plans', function (Blueprint $table) {
            $table->string('memory_limit')->default('128M')->after('price');
            $table->string('upload_max_filesize')->default('16M')->after('memory_limit');
            $table->string('post_max_size')->default('20M')->after('upload_max_filesize');
            $table->integer('max_execution_time')->default(30)->after('post_max_size');
            $table->integer('max_input_time')->default(60)->after('max_execution_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'memory_limit',
                'upload_max_filesize', 
                'post_max_size',
                'max_execution_time',
                'max_input_time'
            ]);
        });
    }
}; 
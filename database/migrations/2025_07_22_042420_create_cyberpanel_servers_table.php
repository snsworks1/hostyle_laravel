<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cyberpanel_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('host');
            $table->integer('api_port')->default(8090);
            $table->string('api_user');
            $table->string('api_password');
            $table->boolean('ssl')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('cyberpanel_servers');
    }
};

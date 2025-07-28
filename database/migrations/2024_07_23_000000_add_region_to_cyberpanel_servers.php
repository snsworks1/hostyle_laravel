<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('cyberpanel_servers', function (Blueprint $table) {
            $table->string('region')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('cyberpanel_servers', function (Blueprint $table) {
            $table->dropColumn('region');
        });
    }
}; 
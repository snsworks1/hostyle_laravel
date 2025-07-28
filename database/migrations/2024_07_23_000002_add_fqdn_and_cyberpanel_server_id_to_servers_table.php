<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->string('fqdn')->nullable()->after('domain');
            $table->unsignedBigInteger('cyberpanel_server_id')->nullable()->after('fqdn');
            $table->foreign('cyberpanel_server_id')->references('id')->on('cyberpanel_servers');
        });
    }

    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign(['cyberpanel_server_id']);
            $table->dropColumn(['fqdn', 'cyberpanel_server_id']);
        });
    }
}; 
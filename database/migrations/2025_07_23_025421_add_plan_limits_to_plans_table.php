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
            $table->string('disk')->nullable()->after('trial_days');
            $table->string('traffic')->nullable()->after('disk');
            $table->integer('domains')->nullable()->after('traffic');
            $table->integer('subdomains')->nullable()->after('domains');
            $table->integer('databases')->nullable()->after('subdomains');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['disk', 'traffic', 'domains', 'subdomains', 'databases']);
        });
    }
};

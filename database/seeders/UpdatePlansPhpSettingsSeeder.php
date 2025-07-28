<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatePlansPhpSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 무료 플랜
        DB::table('plans')->where('name', 'free')->update([
            'memory_limit' => '128M',
            'upload_max_filesize' => '16M',
            'post_max_size' => '20M',
            'max_execution_time' => 30,
            'max_input_time' => 60,
        ]);

        // Starter 플랜
        DB::table('plans')->where('name', 'starter')->update([
            'memory_limit' => '256M',
            'upload_max_filesize' => '32M',
            'post_max_size' => '40M',
            'max_execution_time' => 60,
            'max_input_time' => 90,
        ]);

        // Business 플랜
        DB::table('plans')->where('name', 'business')->update([
            'memory_limit' => '512M',
            'upload_max_filesize' => '64M',
            'post_max_size' => '80M',
            'max_execution_time' => 120,
            'max_input_time' => 180,
        ]);

        // Enterprise 플랜
        DB::table('plans')->where('name', 'enterprise')->update([
            'memory_limit' => '1024M',
            'upload_max_filesize' => '128M',
            'post_max_size' => '128M',
            'max_execution_time' => 300,
            'max_input_time' => 300,
        ]);
    }
} 
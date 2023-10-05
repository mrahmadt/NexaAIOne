<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::table('apps')->insert([
            'name' => 'Demo App',
            'description' => 'Demo app.',
            'owner' => 'admin',
            'authToken' => bin2hex(openssl_random_pseudo_bytes(16)). bin2hex(random_bytes(5)),
            'docToken' => bin2hex(openssl_random_pseudo_bytes(16)). bin2hex(random_bytes(5)),
            'isActive' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('api_app')->insert([
            'api_id' => 1,
            'app_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('api_app')->insert([
            'api_id' => 2,
            'app_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('api_app')->insert([
            'api_id' => 3,
            'app_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('api_app')->insert([
            'api_id' => 4,
            'app_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('api_app')->insert([
            'api_id' => 5,
            'app_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('api_app')->insert([
            'api_id' => 6,
            'app_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('api_app')->insert([
            'api_id' => 7,
            'app_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

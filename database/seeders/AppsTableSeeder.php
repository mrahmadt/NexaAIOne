<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Api;
use Kdabrow\SeederOnce\SeederOnce;
class AppsTableSeeder extends SeederOnce
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
            'enableAppCollection' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $apis = Api::where(['isActive'=>true])->get();
        foreach($apis as $api){
            DB::table('api_app')->insert([
                'api_id' => $api->id,
                'app_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}

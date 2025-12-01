<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlobinUserSeeder extends Seeder
{
    public function run(): void
    {
        // 기존 데이터가 있으면 건너뛰기 (중복 방지)
        $exists = DB::table('plobin_users')
            ->where('email', 'admin@example.com')
            ->exists();

        if ($exists) {
            $this->command->info('Admin user already exists. Skipping...');
            return;
        }

        // Admin 사용자 생성
        DB::table('plobin_users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'department' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Admin user created successfully.');
    }
}

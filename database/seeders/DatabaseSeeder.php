<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Test 사용자 직접 생성 (Factory 미사용)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        // Plobin 사용자 시더
        $this->call([
            PlobinUserSeeder::class,
            PlobinPmsKanbanSeeder::class,
            PlobinGanttSeeder::class,
        ]);
    }
}

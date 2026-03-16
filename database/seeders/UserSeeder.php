<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin DIGI-TONTINE',
            'phone' => '+2250700000000',
            'email' => 'admin@digitontine.ci',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'status' => 'active',
            'phone_verified_at' => now(),
        ]);

        // Utilisateurs de test
        $users = [
            ['name' => 'Kouassi Yao', 'phone' => '+2250701010101', 'email' => 'kouassi@test.ci'],
            ['name' => 'Adjoua Ama', 'phone' => '+2250502020202', 'email' => 'adjoua@test.ci'],
            ['name' => 'Koné Ibrahim', 'phone' => '+2250103030303', 'email' => 'kone@test.ci'],
            ['name' => 'Touré Fatou', 'phone' => '+2250704040404', 'email' => 'toure@test.ci'],
            ['name' => 'Bamba Moussa', 'phone' => '+2250505050505', 'email' => 'bamba@test.ci'],
            ['name' => 'Coulibaly Awa', 'phone' => '+2250706060606', 'email' => null],
            ['name' => 'N\'Guessan Koffi', 'phone' => '+2250107070707', 'email' => null],
            ['name' => 'Diallo Mariame', 'phone' => '+2250708080808', 'email' => 'diallo@test.ci'],
            ['name' => 'Aka Serge', 'phone' => '+2250509090909', 'email' => null],
            ['name' => 'Ouattara Sita', 'phone' => '+2250710101010', 'email' => 'ouattara@test.ci'],
        ];

        foreach ($users as $userData) {
            User::create(array_merge($userData, [
                'password' => Hash::make('password'),
                'status' => 'active',
                'phone_verified_at' => now(),
            ]));
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'user1',
                'email' => 'user1@gmail.com',
                'password' => bcrypt('12345678'),
            ],
            [
                'name' => 'user2',
                'email' => 'user2@gmail.com',
                'password' => bcrypt('12345678'),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('test');

        $users = [
            ['username' => 'ttn', 'email' => 'ttn@email.com', 'full_name' => 'Trần Trung Nhân', 'phone_number' => '0123456780', 'role' => 'teacher', 'password' => $password],
            ['username' => 'ttn2', 'email' => 'ttn2@email.com', 'full_name' => 'Trần Trung Nhân', 'phone_number' => '0123456781', 'role' => 'student', 'password' => $password],
            ['username' => 'teacher1', 'email' => 'teacher1@email.com', 'full_name' => 'Giáo Viên 1', 'phone_number' => '0123456782', 'role' => 'teacher', 'password' => $password],
            ['username' => 'teacher2', 'email' => 'teacher2@email.com', 'full_name' => 'Giáo Viên 2', 'phone_number' => '0123456783', 'role' => 'teacher', 'password' => $password],
            ['username' => 'student1', 'email' => 'student1@email.com', 'full_name' => 'Sinh Viên 1', 'phone_number' => '0123456784', 'role' => 'student', 'password' => $password],
            ['username' => 'student2', 'email' => 'student2@email.com', 'full_name' => 'Sinh Viên 2', 'phone_number' => '0123456785', 'role' => 'student', 'password' => $password],
            ['username' => 'student3', 'email' => 'student3@email.com', 'full_name' => 'Sinh Viên 3', 'phone_number' => '0123456786', 'role' => 'student', 'password' => $password],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
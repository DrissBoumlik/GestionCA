<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'firstname' => 'John',
            'lastname' => 'SuperAdmin',
            'email' => 'sa@sa.sa',
            'status' => true,
            'picture' => '/media/avatars/superAdmin.png',
            'gender' => 'male',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'role_id' => 1
        ]);
        User::create([
            'firstname' => 'John',
            'lastname' => 'Admin',
            'email' => 'a@a.a',
            'status' => true,
            'picture' => '/media/avatars/admin.png',
            'gender' => 'male',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'role_id' => 1
        ]);
    }
}

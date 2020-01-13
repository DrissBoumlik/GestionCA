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
            'lastname' => 'Doe',
            'email' => 'a@a.a',
            'status' => true,
            'picture' => 'https://images2.imgbox.com/9c/5e/F8JZCJLn_o.png',
            'gender' => 'male',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'role_id' => 1
        ]);
        User::create([
            'firstname' => 'dosez6',
            'lastname' => 'Agent',
            'email' => 'dosez6@circet.fr',
            'status' => true,
            'picture' => 'https://images2.imgbox.com/9c/5e/F8JZCJLn_o.png',
            'gender' => 'male',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'role_id' => 2,
            'agence_name' => '6 - DOSEZ6'
        ]);
    }
}

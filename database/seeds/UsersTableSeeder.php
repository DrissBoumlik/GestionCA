<?php

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
        \App\Models\User::create([
            'firstname' => 'Driss',
            'lastname' => 'Boumlik',
            'email' => 'a@a.a',
            'status' => true,
            'picture' => 'https://images2.imgbox.com/9c/5e/F8JZCJLn_o.png',
            'gender' => 'male',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'role_id' => 1
        ]);
        factory(App\Models\User::class, 100)->create();
    }
}

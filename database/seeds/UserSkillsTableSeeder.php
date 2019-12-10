<?php

use Illuminate\Database\Seeder;

class UserSkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\UserSkill::class, 500)->create();
    }
}

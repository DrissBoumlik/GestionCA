<?php

use Illuminate\Database\Seeder;

class ProjectSkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\ProjectSkill::class, 100)->create();
    }
}

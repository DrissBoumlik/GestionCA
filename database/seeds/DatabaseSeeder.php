<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);

        $this->call(SkillsTableSeeder::class);
        $this->call(UserSkillsTableSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(ProjectSkillsTableSeeder::class);
        $this->call(ProjectUsersTableSeeder::class);

        $this->call(StatsTableSeeder::class);
        $this->call(CodesTableSeeder::class);
        $this->call(CodeInterventionsTableSeeder::class);
    }
}

<?php

use Illuminate\Database\Seeder;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $techs = ['Angular', 'ReactJS', 'VueJS', 'Php', 'Laravel', 'Symphony','CodeIgniter','Javascript', 'Asp.Net', 'C#',
            'Python', 'Django', 'C', 'C++', 'Java', 'HTML', 'CSS', 'jQuery', 'Wordpress', 'Ruby', 'NodeJS', 'Express', 'Zend',
            'Spring boot', 'Drupal', 'Bootstrap'];
        foreach ($techs as $tech) {
            \App\Models\Skill::create([
                'name' => $tech
            ]);
        }
//        factory(App\Models\Skill::class, 100)->create();
    }
}

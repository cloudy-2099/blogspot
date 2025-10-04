<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Category;
use \App\Models\Post;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'name' => "Rizky Wahyudi",
            'username' => "rizky",
            'email' => 'rizky@gmail.com',
            'password' => bcrypt('123456')
        ]);
        // User::create([
        //     'name' => "Camellya",
        //     'email' => 'camellya@gmail.com',
        //     'password' => bcrypt('123456')
        // ]);

        User::factory(3)->create();

        Category::create([
            'name' => 'Web programming',
            'Slug' => 'web-programming'
        ]);
        Category::create([
            'name' => 'Personal',
            'Slug' => 'personal'
        ]);
        Category::create([
            'name' => 'Design',
            'Slug' => 'design'
        ]);

        Post::factory(20)->create();
    }
}

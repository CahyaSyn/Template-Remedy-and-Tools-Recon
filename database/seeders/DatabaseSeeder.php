<?php

namespace Database\Seeders;

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
        $this->call([
            Create_application_seeder::class,
            Create_parent_kedb_seeder::class,
            Create_child_kedb_seeder::class,
            Create__kedb_seeder::class,
        ]);
    }
}

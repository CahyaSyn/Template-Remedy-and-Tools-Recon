<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;

class Create_application_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'app_id' => 1,
                'app_name' => 'DIGIPOS'
            ],
            [
                'app_id' => 2,
                'app_name' => 'DSC'
            ],
            [
                'app_id' => 3,
                'app_name' => 'CUG'
            ]
        ];

        foreach ($data as $key => $value) {
            Application::create($value);
        }
    }
}

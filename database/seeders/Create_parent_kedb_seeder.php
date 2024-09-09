<?php

namespace Database\Seeders;

use App\Models\KedbParent;
use Illuminate\Database\Seeder;

class Create_parent_kedb_seeder extends Seeder
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
                'kedb_parent_id' => 1,
                'kedb_parent_name' => 'REAKTIVASI PREPAID'
            ],
            [
                'kedb_parent_id' => 2,
                'kedb_parent_name' => 'Migrasi Postpaid to Prepaid'
            ],
            [
                'kedb_parent_id' => 3,
                'kedb_parent_name' => 'MSISDN HYBRID'
            ]
        ];

        foreach ($data as $key => $value) {
            KedbParent::create($value);
        }
    }
}

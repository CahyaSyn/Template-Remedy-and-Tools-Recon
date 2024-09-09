<?php

namespace Database\Seeders;

use App\Models\KedbChild;
use Illuminate\Database\Seeder;

class Create_child_kedb_seeder extends Seeder
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
                'kedb_child_id' => 1,
                'kedb_parent_id' => 1,
                'kedb_child_name' => 'K14-Reaktivasi Gagal',
            ],
            [
                'kedb_child_id' => 2,
                'kedb_parent_id' => 2,
                'kedb_child_name' => 'K59-Gagal/Error Migrasi Post to Pre',
            ],
            [
                'kedb_child_id' => 3,
                'kedb_parent_id' => 3,
                'kedb_child_name' => 'P32-Permintaan Aktivasi Layanan',
            ]
        ];

        foreach ($data as $key => $value) {
            KedbChild::create($value);
        }
    }
}

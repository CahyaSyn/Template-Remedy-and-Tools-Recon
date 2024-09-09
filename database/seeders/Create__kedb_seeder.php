<?php

namespace Database\Seeders;

use App\Models\Kedb;
use Illuminate\Database\Seeder;

class Create__kedb_seeder extends Seeder
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
                'kedb_id' => 1,
                'kedb_parent_id' => 1,
                'kedb_child_id' => 1,
                'app_id' => 2,
                'old_kedb' => '[]DSC[]Reaktivasi_Prepaid_Failed[]Cek_/api/reactivate[]Complaint',
                'new_symtom_kedb' => 'Unsync Southbound CRM BE',
                'new_specific_symtom_kedb' => 'Status CRM BE Active',
                'kedb_finalisasi' => 'KIP_Q32024_480_9',
                'action' => 'eskalasi to CRM BE',
                'responsibility_action' => 'L1',

            ],
            [
                'kedb_id' => 2,
                'kedb_parent_id' => 1,
                'kedb_child_id' => 1,
                'app_id' => 2,
                'old_kedb' => '[]DSC[]Reaktivasi_Prepaid_Failed[]Cek_/api/reactivate[]Complaint',
                'new_symtom_kedb' => 'Unsync Southbound DSP',
                'new_specific_symtom_kedb' => 'Status HLR In Active',
                'kedb_finalisasi' => 'KIP_Q32024_480_10',
                'action' => 'eskalasi to DSP',
                'responsibility_action' => 'L1',

            ],
            [
                'kedb_id' => 3,
                'kedb_parent_id' => 2,
                'kedb_child_id' => 2,
                'app_id' => 2,
                'old_kedb' => '[]DSC[]Kendala_Migrasi_PostToPre[]Customer_Profile[]Complaint',
                'new_symtom_kedb' => 'Unsync Southbound TC',
                'new_specific_symtom_kedb' => 'Status TC Still Active',
                'kedb_finalisasi' => 'KIP_Q32024_1031_1',
                'action' => 'eskalasi to TC',
                'responsibility_action' => 'L1',

            ]

        ];

        foreach ($data as $key => $value) {
            Kedb::create($value);
        }
    }
}

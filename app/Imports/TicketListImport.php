<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TicketListImport implements WithMultipleSheets
{
    public $importedCount = 0;

    public function sheets(): array
    {
        $reqRemedySheetImport = new ReqRemedySheetImport($this);

        return [
            'Req_Remedy' => $reqRemedySheetImport,
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Form;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketListExportPerDay implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // export only data from today
        return Form::join('applications', 'forms.app_id', '=', 'applications.app_id')
            ->join('kedbs', 'forms.kedb_id', '=', 'kedbs.kedb_id')
            ->join('users', 'forms.user_id', '=', 'users.user_id')
            ->select('forms.ticket_id', 'kedbs.kedb_finalisasi', 'forms.assignment', 'users.username', 'forms.notes')
            ->whereDate('forms.created_at', today())
            ->get();
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'KEDB',
            'Status',
            'PIC Handling',
            'Notes',
        ];
    }
}

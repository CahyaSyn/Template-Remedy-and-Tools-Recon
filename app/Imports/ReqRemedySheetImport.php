<?php

namespace App\Imports;

use App\Models\Form;
use App\Models\User;
use App\Models\Kedb;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ReqRemedySheetImport implements ToModel, WithHeadingRow
{
    private $parentImport;

    public function __construct($parentImport)
    {
        $this->parentImport = $parentImport;
    }

    public function model(array $row)
    {
        if (
            empty($row['no_tiket'])
            || empty($row['date'])
            || empty($row['resolution_note'])
            || empty($row['status'])
            || empty($row['pic_handling'])
            || empty($row['kedb'])
        ) {
            return null;
        }

        $caseName = explode("\n", $row['resolution_note']);
        $caseName = array_filter($caseName, function ($value) {
            return strpos($value, 'Case Name : ') !== false || strpos($value, 'Case Name:') !== false;
        });
        $caseName = array_values($caseName);
        if (isset($caseName[0])) {
            $caseName = str_replace(['Case Name : ', 'Case Name:'], '', $caseName[0]);
            $caseName = trim($caseName);
        } else {
            $caseName = '';
        }

        $action = explode("\n", $row['resolution_note']);
        $action = array_filter($action, function ($value) {
            return strpos($value, 'Action : ') !== false || strpos($value, 'Action:') !== false;
        });
        $action = array_values($action);
        if (isset($action[0])) {
            $action = str_replace(['Action : ', 'Action:'], '', $action[0]);
            $action = trim($action);
        } else {
            $action = '';
        }

        $nextAction = explode("\n", $row['resolution_note']);
        $nextAction = array_filter($nextAction, function ($value) {
            return strpos($value, 'Next Action / Request Action : ') !== false
                || strpos($value, 'Resolution : ') !== false
                || strpos($value, 'Next Action / Request Action:') !== false
                || strpos($value, 'Resolution:') !== false
                || strpos($value, 'Next Action :') !== false
                || strpos($value, 'Next Action:') !== false;
        });
        $nextAction = array_values($nextAction);
        if (isset($nextAction[0])) {
            $nextAction = str_replace(['Next Action / Request Action : ', 'Resolution : ', 'Next Action / Request Action:', 'Resolution:', 'Next Action :', 'Next Action:'], '', $nextAction[0]);
            $nextAction = trim($nextAction);
        } else {
            $nextAction = '';
        }

        if (strpos($row['resolution_note'], "Evidence Log :") !== false) {
            $evidence = explode("Evidence Log :", $row['resolution_note']);
        } elseif (strpos($row['resolution_note'], "Evidence :") !== false) {
            $evidence = explode("Evidence :", $row['resolution_note']);
        } else {
            $evidence = [];
        }

        $evidence = array_values($evidence);
        if (count($evidence) >= 2) {
            $evidence = explode("Terima kasih", $evidence[1]);
            $evidence = array_values($evidence);
            $evidence = $evidence[0];
        } else {
            $evidence = '';
        }

        $ticket_id = $row['no_tiket'];
        $pic = User::where('username', $row['pic_handling'])->first();
        $kedb = Kedb::where('kedb_finalisasi', $row['kedb'])->first();
        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d');
        $date_db_format = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d H:i:s');

        $existingRecord = Form::where('created_at', $date_db_format)
            ->where('ticket_id', $row['no_tiket'])
            ->where('casename', $caseName)
            ->where('action', $action)
            ->where('nextaction', $nextAction)
            ->where('assignment', $row['status'])
            ->where('notes', $row['resolution_note'])
            ->where('user_id', $pic ? $pic->user_id : 0)
            ->first();

        if (!$existingRecord) {
            $this->parentImport->importedCount++;
            return new Form([
                'starts_at' => $date,
                'ends_at' => $date,
                'app_id' => $kedb ? $kedb->app_id : 0,
                'created_at' => $date,
                'ticket_id' => $ticket_id,
                'casename' => $caseName,
                'action' => $action,
                'nextaction' => $nextAction,
                'evidence' => $evidence,
                'kedb_id' => $kedb ? $kedb->kedb_id : 0,
                'assignment' => $row['status'],
                'user_id' => $pic ? $pic->user_id : 0,
                'notes' => $row['resolution_note']
            ]);
        }

        return null;
    }
}

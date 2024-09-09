<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;

class ToolsController extends Controller
{
    public function index()
    {
        return view('tools');
    }

    public function importFiles(Request $request)
    {
        $file = $request->file('recon_file_arp');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet_data_recon = $spreadsheet->getSheetByName('DATA_RECON');
        $sheet_already_success = $spreadsheet->getSheetByName('ALREADY_SUCCESS');
        $sheet_los = $spreadsheet->getSheetByName('LOS');
        $sheet_upcc = $spreadsheet->getSheetByName('UPCC');
        $sheet_tc = $spreadsheet->getSheetByName('TC');
        $sheet_dom = $spreadsheet->getSheetByName('DOM');
        $sheet_ngrs = $spreadsheet->getSheetByName('NGRS');
        $sheet_summary = $spreadsheet->getSheetByName('SUMMARY');
        $sheet_format_gui_update = $spreadsheet->getSheetByName('FORMAT_UPDATE_GUI');

        // Set default font to Calibri for the entire workbook
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');

        // add header
        $sheet_data_recon->setCellValue('O1', 'UPDATED');
        $sheet_data_recon->setCellValue('P1', 'LOS');
        $sheet_data_recon->setCellValue('Q1', 'UPCC');
        $sheet_data_recon->setCellValue('R1', 'TC_STATUS');
        $sheet_data_recon->setCellValue('S1', 'LAST_DENOM');
        $sheet_data_recon->setCellValue('T1', 'DOM');
        $sheet_data_recon->setCellValue('U1', 'NGRS');
        $sheet_data_recon->setCellValue('V1', 'PARAMETER_ALREADY_SUCCESS');
        $sheet_data_recon->setCellValue('W1', 'PARAMETER_STATUS_UPDATE');
        $sheet_data_recon->setCellValue('X1', 'ACTIVATION_STATUS');
        $sheet_data_recon->setCellValue('Y1', 'RECHARGE_STATUS');
        $sheet_data_recon->setCellValue('Z1', 'PACKAGE_STATUS');
        $sheet_format_gui_update->setCellValue('A1', 'GUI_UPDATE');

        $sheet_data_recon->getStyle('K2:K' . $sheet_data_recon->getHighestRow())->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        $sheet_data_recon->getStyle('L2:L' . $sheet_data_recon->getHighestRow())->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        $sheet_data_recon->getStyle('Q2:Q' . $sheet_data_recon->getHighestRow())->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');

        $los_lookup = [];
        foreach ($sheet_los->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            if (!empty($cells[0])) {
                $los_lookup[$cells[0]] = $cells[1]; // Assuming MSISDN is in column A and LOS is in column B
            }
        }

        // Iterate through DATA_RECON sheet and update LOS column
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $msisdn = $cells[3]; // Assuming MSISDN is in column D
            if (isset($los_lookup[$msisdn])) {
                $sheet_data_recon->setCellValue('P' . $row->getRowIndex(), $los_lookup[$msisdn]); // Assuming LOS should be placed in column P
            }
        }

        // Create a lookup array for UPCC sheet starting from row 2
        $upcc_lookup = [];
        foreach ($sheet_upcc->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            // if (!empty($cells[0])) {
            //     $upcc_lookup[$cells[0]] = $cells[5]; // Assuming MSISDN is in column A and SUBSCRIBE DATE is in column F
            // }
            if (!empty($cells[0])) {
                $subscribe_date = $cells[5]; // Assuming MSISDN is in column A and SUBSCRIBE DATE is in column F
                $formatted_date = DateTime::createFromFormat('Y-m-d\TH:i:s', $subscribe_date);
                if ($formatted_date) {
                    $upcc_lookup[$cells[0]] = $formatted_date->format('Y-m-d H:i:s');
                } else {
                    $upcc_lookup[$cells[0]] = $subscribe_date; // Keep original if parsing fails
                }
            }
        }

        // Iterate through DATA_RECON sheet and update UPCC column
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $msisdn = $cells[3]; // Assuming MSISDN is in column D
            if (isset($upcc_lookup[$msisdn])) {
                $sheet_data_recon->setCellValue('Q' . $row->getRowIndex(), $upcc_lookup[$msisdn]); // Assuming UPCC should be placed in column Q
            }
        }

        // Create a lookup array for TC sheet starting from row 2
        $tc_lookup = [];
        foreach ($sheet_tc->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            if (!empty($cells[1])) {
                $tc_lookup[$cells[1]] = $cells[2]; // Assuming MSISDN is in column B and subscriber_status is in column C
            }
        }

        // Iterate through DATA_RECON sheet and update TC_STATUS column
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $msisdn = $cells[3]; // Assuming MSISDN is in column D
            if (isset($tc_lookup[$msisdn])) {
                $sheet_data_recon->setCellValue('R' . $row->getRowIndex(), $tc_lookup[$msisdn]); // Assuming TC_STATUS should be placed in column R
            }
        }

        // Create a lookup array for DOM sheet starting from row 2
        $dom_lookup = [];
        foreach ($sheet_dom->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            if (!empty($cells[0])) {
                $dom_lookup[$cells[0]] = $cells[5]; // Assuming trx_id is in column A and detailedCode is in column F
            }
        }

        // Iterate through DATA_RECON sheet and update DOM column
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $trx_id = $cells[7]; // Assuming trx_id is in column H
            if (isset($dom_lookup[$trx_id])) {
                $sheet_data_recon->setCellValue('T' . $row->getRowIndex(), $dom_lookup[$trx_id]); // Assuming DOM should be placed in column T
            }
        }

        // Create a lookup array for NGRS sheet starting from row 2
        $ngrs_lookup = [];
        foreach ($sheet_ngrs->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            if (!empty($cells[0])) {
                $ngrs_lookup[$cells[0]] = $cells[1]; // Assuming trx_id is in column A and PROCSTATE is in column B
            }
        }

        // Iterate through DATA_RECON sheet and update NGRS column
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $trx_id = $cells[7]; // Assuming trx_id is in column H
            if (isset($ngrs_lookup[$trx_id])) {
                $sheet_data_recon->setCellValue('U' . $row->getRowIndex(), $ngrs_lookup[$trx_id]); // Assuming NGRS should be placed in column U
            }
        }

        // Create a lookup array for ALREADY_SUCCESS sheet starting from row 2
        $already_success_lookup = [];
        foreach ($sheet_already_success->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            if (!empty($cells[0])) {
                $already_success_lookup[$cells[2]] = $cells[8]; // Assuming trx_id is in column C and REASON is in column I
            }
        }

        // Iterate through DATA_RECON sheet and update PARAMETER_ALREADY_SUCCESS column
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $trx_id = $cells[7]; // Assuming trx_id is in column H
            if (isset($already_success_lookup[$trx_id])) {
                $sheet_data_recon->setCellValue('V' . $row->getRowIndex(), $already_success_lookup[$trx_id]); // Assuming PARAMETER_ALREADY_SUCCESS should be placed in column V
            }
        }

        // Set ACTIVATION_STATUS in column X to SUKSES or GAGAL based on conditions
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $trx_type = $cells[13]; // Assuming TRX_TYPE is in column N
            $parameter_already_success = $cells[21]; // Assuming PARAMETER_ALREADY_SUCCESS is in column V
            $tc_status = $cells[17]; // Assuming TC_STATUS is in column R

            if ($trx_type !== 'BYU_AP' && empty($parameter_already_success) && ($tc_status === 'A' || $tc_status === 'ACTIVE')) {
                $sheet_data_recon->setCellValue('X' . $row->getRowIndex(), 'SUKSES'); // Set ACTIVATION_STATUS to SUKSES in column X
            } elseif ($trx_type === 'BYU_AP' && empty($parameter_already_success)) {
                $sheet_data_recon->setCellValue('X' . $row->getRowIndex(), 'SUKSES'); // Set ACTIVATION_STATUS to SUKSES in column X
            } else {
                $sheet_data_recon->setCellValue('X' . $row->getRowIndex(), 'GAGAL'); // Set ACTIVATION_STATUS to GAGAL in column X
            }
        }

        // Set PACKAGE_STATUS in column Z
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $trx_type = $cells[13]; // Assuming TRX_TYPE is in column N
            $parameter_already_success = $cells[21]; // Assuming PARAMETER_ALREADY_SUCCESS is in column V
            $tc_status = $cells[17]; // Assuming TC_STATUS is in column R
            $los = $cells[15]; // Assuming LOS is in column P
            $upcc = $cells[16]; // Assuming UPCC is in column Q
            $time_trx = $cells[10]; // Assuming TIME_TRX is in column K
            $ngrs = $cells[20]; // Assuming NGRS is in column U
            $dom = $cells[19]; // Assuming DOM is in column T

            // Convert TIME_TRX from Excel serial date format to DateTime
            $time_trx_date = Date::excelToDateTimeObject($time_trx)->format('Y-m-d H:i:s');
            if ($trx_type !== 'BYU_AP') {
                if (empty($parameter_already_success) && ($tc_status === 'A' || $tc_status === 'ACTIVE') && $los < 30 && !empty($upcc) && ($dom === 'SUCCESS' || $dom === 'success')) {
                    $upcc_date = DateTime::createFromFormat('Y-m-d H:i:s', $upcc);
                    if ($upcc_date) {
                        $interval = $upcc_date->diff(new DateTime($time_trx_date));
                        if ($interval->h < 1 && $interval->days === 0) {
                            $sheet_data_recon->setCellValue('Z' . $row->getRowIndex(), 'SUKSES'); // Set PACKAGE_STATUS to SUKSES in column Z
                            continue;
                        }
                    }
                }
            } elseif ($trx_type === 'BYU_AP') {
                if (empty($parameter_already_success) && $ngrs === 'Completed') {
                    $sheet_data_recon->setCellValue('Z' . $row->getRowIndex(), 'SUKSES'); // Set PACKAGE_STATUS to SUKSES in column Z
                    continue;
                }
            }
            $sheet_data_recon->setCellValue('Z' . $row->getRowIndex(), 'GAGAL'); // Set PACKAGE_STATUS to GAGAL in column Z
        }

        // Set RECHARGE_STATUS in column Y
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $activation_status = $cells[23]; // Assuming ACTIVATION_STATUS is in column X
            $package_status = $cells[25]; // Assuming PACKAGE_STATUS is in column Z

            if ($activation_status === 'SUKSES' && $package_status === 'SUKSES') {
                $sheet_data_recon->setCellValue('Y' . $row->getRowIndex(), 'SUKSES'); // Set RECHARGE_STATUS to SUKSES in column Y
            } else {
                $sheet_data_recon->setCellValue('Y' . $row->getRowIndex(), 'GAGAL'); // Set RECHARGE_STATUS to GAGAL in column Y
            }
        }

        // Set PARAMETER_STATUS_UPDATE in column W and UPDATED in column O
        foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $activation_status = $cells[23]; // Assuming ACTIVATION_STATUS is in column X
            $recharge_status = $cells[24]; // Assuming RECHARGE_STATUS is in column Y
            $package_status = $cells[25]; // Assuming PACKAGE_STATUS is in column Z

            if ($activation_status === 'SUKSES' && $recharge_status === 'SUKSES' && $package_status === 'SUKSES') {
                $sheet_data_recon->setCellValue('W' . $row->getRowIndex(), 'SUKSES'); // Set PARAMETER_STATUS_UPDATE to SUKSES in column W
                $sheet_data_recon->setCellValue('O' . $row->getRowIndex(), 'SUKSES'); // Set UPDATED to SUKSES in column O
            } else {
                $sheet_data_recon->setCellValue('W' . $row->getRowIndex(), 'GAGAL'); // Set PARAMETER_STATUS_UPDATE to GAGAL in column W
                $sheet_data_recon->setCellValue('O' . $row->getRowIndex(), 'GAGAL'); // Set UPDATED to GAGAL in column O
            }
        }

        // Iterate through DATA_RECON sheet starting from row 2
        $rowIndex = 2;
        while ($sheet_data_recon->getCell('H' . $rowIndex)->getValue() !== null) {
            $trx_id = $sheet_data_recon->getCell('H' . $rowIndex)->getValue();
            $activation_status = $sheet_data_recon->getCell('X' . $rowIndex)->getValue();
            $recharge_status = $sheet_data_recon->getCell('Y' . $rowIndex)->getValue();
            $package_status = $sheet_data_recon->getCell('Z' . $rowIndex)->getValue();
            $updated = $sheet_data_recon->getCell('O' . $rowIndex)->getValue();

            $gui_update_value = "$trx_id|$activation_status|$recharge_status|$package_status|$updated";
            $sheet_format_gui_update->setCellValue('A' . $rowIndex, $gui_update_value);

            $rowIndex++;
        }

        // Auto-adjust column width for all sheets
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        // download updated data
        $filename = 'recon_updated_' . date('YmdHis') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}

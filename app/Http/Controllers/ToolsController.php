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
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ToolsController extends Controller
{
    public function index()
    {
        return view('tools');
    }

    public function importFiles(Request $request)
    {
        try {
            // Validate the request to ensure the file is present
            if (!$request->hasFile('recon_file_arp')) {
                throw new \Exception('No file uploaded');
            }

            $file = $request->file('recon_file_arp');

            // Check if the file is valid
            if (!$file->isValid()) {
                throw new \Exception('File upload error');
            }
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
            $sheet_data_recon->setCellValue('R1', 'INTERVAL_TRX_DATE_TO_UPCC');
            $sheet_data_recon->setCellValue('S1', 'TC_STATUS');
            $sheet_data_recon->setCellValue('T1', 'LAST_DENOM');
            $sheet_data_recon->setCellValue('U1', 'DOM');
            $sheet_data_recon->setCellValue('V1', 'NGRS');
            $sheet_data_recon->setCellValue('W1', 'PARAMETER_ALREADY_SUCCESS');
            $sheet_data_recon->setCellValue('X1', 'PARAMETER_STATUS_UPDATE');
            $sheet_data_recon->setCellValue('Y1', 'ACTIVATION_STATUS');
            $sheet_data_recon->setCellValue('Z1', 'RECHARGE_STATUS');
            $sheet_data_recon->setCellValue('AA1', 'PACKAGE_STATUS');
            $sheet_format_gui_update->setCellValue('A1', 'GUI_UPDATE');

            $sheet_data_recon->getStyle('G2:G' . $sheet_data_recon->getHighestRow())->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
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
                    $sheet_data_recon->setCellValue('S' . $row->getRowIndex(), $tc_lookup[$msisdn]); // Assuming TC_STATUS should be placed in column S
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
                    $dom_lookup[$cells[2]] = $cells[7]; // Assuming trx_id is in column C and detailedCode is in column H
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
                    $sheet_data_recon->setCellValue('U' . $row->getRowIndex(), $dom_lookup[$trx_id]); // Assuming DOM should be placed in column U
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
                    $sheet_data_recon->setCellValue('V' . $row->getRowIndex(), $ngrs_lookup[$trx_id]); // Assuming NGRS should be placed in column U
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
                    $sheet_data_recon->setCellValue('W' . $row->getRowIndex(), $already_success_lookup[$trx_id]); // Assuming PARAMETER_ALREADY_SUCCESS should be placed in column W
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
                $parameter_already_success = $cells[22]; // Assuming PARAMETER_ALREADY_SUCCESS is in column W
                $tc_status = $cells[18]; // Assuming TC_STATUS is in column S

                if ($trx_type !== 'BYU_AP' && empty($parameter_already_success) && ($tc_status === 'A' || $tc_status === 'ACTIVE')) {
                    $sheet_data_recon->setCellValue('Y' . $row->getRowIndex(), 'SUKSES'); // Set ACTIVATION_STATUS to SUKSES in column Y
                } elseif ($trx_type === 'BYU_AP' && empty($parameter_already_success)) {
                    $sheet_data_recon->setCellValue('Y' . $row->getRowIndex(), 'SUKSES'); // Set ACTIVATION_STATUS to SUKSES in column Y
                } else {
                    $sheet_data_recon->setCellValue('Y' . $row->getRowIndex(), 'GAGAL'); // Set ACTIVATION_STATUS to GAGAL in column Y
                }
            }

            // Set PACKAGE_STATUS in column AA and INTERVAL_TRX_DATE_TO_UPCC in column R
            foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                $trx_type = $cells[13]; // Assuming TRX_TYPE is in column N
                $parameter_already_success = $cells[22]; // Assuming PARAMETER_ALREADY_SUCCESS is in column W
                $tc_status = $cells[18]; // Assuming TC_STATUS is in column S
                $los = $cells[15]; // Assuming LOS is in column P
                $upcc = $cells[16]; // Assuming UPCC is in column Q
                $time_trx = $cells[6]; // Assuming TIME_TRX is in column G
                $ngrs = $cells[21]; // Assuming NGRS is in column V
                $dom = $cells[20]; // Assuming DOM is in column U

                // Convert TIME_TRX from '07-SEP-24 12.33.51.000000000 AM' to 'Y-m-d H:i:s'
                $time_trx_date = DateTime::createFromFormat('d-M-y h.i.s.u A', $time_trx)->format('Y-m-d H:i:s');

                // Calculate the interval in minutes between TIME_TRX and UPCC
                $upcc_date = DateTime::createFromFormat('Y-m-d H:i:s', $upcc);
                if ($upcc_date) {
                    $interval = $upcc_date->diff(new DateTime($time_trx_date));
                    $hours = $interval->h + ($interval->days * 24);
                    $minutes = $interval->i;
                    $seconds = $interval->s;
                    $formatted_interval = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                    $sheet_data_recon->setCellValue('R' . $row->getRowIndex(), $formatted_interval); // Set INTERVAL_TRX_DATE_TO_UPCC in column R
                } else {
                    $sheet_data_recon->setCellValue('R' . $row->getRowIndex(), 'Invalid UPCC Date'); // Handle invalid UPCC date
                }

                if ($trx_type !== 'BYU_AP') {
                    if (empty($parameter_already_success) && ($tc_status === 'A' || $tc_status === 'ACTIVE') && (empty($los) || $los < 30) && !empty($upcc) && ($dom === 'SUCCESS' || $dom === 'success')) {
                        if ($upcc_date && $interval->days * 24 * 60 + $interval->h * 60 + $interval->i <= 60) {
                            $sheet_data_recon->setCellValue('AA' . $row->getRowIndex(), 'SUKSES'); // Set PACKAGE_STATUS to SUKSES in column AA
                            continue;
                        }
                    }
                } elseif ($trx_type === 'BYU_AP') {
                    if (empty($parameter_already_success) && $ngrs === 'Completed') {
                        $sheet_data_recon->setCellValue('AA' . $row->getRowIndex(), 'SUKSES'); // Set PACKAGE_STATUS to SUKSES in column AA
                        continue;
                    }
                }
                $sheet_data_recon->setCellValue('AA' . $row->getRowIndex(), 'GAGAL'); // Set PACKAGE_STATUS to GAGAL in column AA
            }

            // Set RECHARGE_STATUS in column Y
            foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                $activation_status = $cells[24]; // Assuming ACTIVATION_STATUS is in column Y
                $package_status = $cells[26]; // Assuming PACKAGE_STATUS is in column AA

                if ($activation_status === 'SUKSES' && $package_status === 'SUKSES') {
                    $sheet_data_recon->setCellValue('Z' . $row->getRowIndex(), 'SUKSES'); // Set RECHARGE_STATUS to SUKSES in column Z
                } else {
                    $sheet_data_recon->setCellValue('Z' . $row->getRowIndex(), 'GAGAL'); // Set RECHARGE_STATUS to GAGAL in column Z
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
                $activation_status = $cells[24]; // Assuming ACTIVATION_STATUS is in column Y
                $recharge_status = $cells[25]; // Assuming RECHARGE_STATUS is in column Z
                $package_status = $cells[26]; // Assuming PACKAGE_STATUS is in column AA

                if ($activation_status === 'SUKSES' && $recharge_status === 'SUKSES' && $package_status === 'SUKSES') {
                    $sheet_data_recon->setCellValue('X' . $row->getRowIndex(), 'SUKSES'); // Set PARAMETER_STATUS_UPDATE to SUKSES in column X
                    $sheet_data_recon->setCellValue('O' . $row->getRowIndex(), 'SUKSES'); // Set UPDATED to SUKSES in column O
                } else {
                    $sheet_data_recon->setCellValue('X' . $row->getRowIndex(), 'GAGAL'); // Set PARAMETER_STATUS_UPDATE to GAGAL in column X
                    $sheet_data_recon->setCellValue('O' . $row->getRowIndex(), 'GAGAL'); // Set UPDATED to GAGAL in column O
                }
            }

            // Add conditional formatting rules
            $conditionalStyles = $sheet_data_recon->getStyle('X2:AA' . $sheet_data_recon->getHighestRow())->getConditionalStyles();

            // Green background if all values in columns X, Y, Z, and AA are SUKSES
            $conditionalGreen = new Conditional();
            $conditionalGreen->setConditionType(Conditional::CONDITION_EXPRESSION)
                ->setOperatorType(Conditional::OPERATOR_NONE)
                ->addCondition('AND($X2="SUKSES",$Y2="SUKSES",$Z2="SUKSES",$AA2="SUKSES")');
            $conditionalGreen->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00B050');

            // Yellow background if values in columns Y, Z, and AA are SUKSES, GAGAL, GAGAL
            $conditionalYellow = new Conditional();
            $conditionalYellow->setConditionType(Conditional::CONDITION_EXPRESSION)
                ->setOperatorType(Conditional::OPERATOR_NONE)
                ->addCondition('AND($Y2="SUKSES",$Z2="GAGAL",$AA2="GAGAL")');
            $conditionalYellow->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFC000');

            // Red background if all values in columns X, Y, Z, and AA are GAGAL
            $conditionalRed = new Conditional();
            $conditionalRed->setConditionType(Conditional::CONDITION_EXPRESSION)
                ->setOperatorType(Conditional::OPERATOR_NONE)
                ->addCondition('AND($X2="GAGAL",$Y2="GAGAL",$Z2="GAGAL",$AA2="GAGAL")');
            $conditionalRed->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');

            // Add the conditional styles to the range
            $conditionalStyles[] = $conditionalGreen;
            $conditionalStyles[] = $conditionalYellow;
            $conditionalStyles[] = $conditionalRed;

            $sheet_data_recon->getStyle('X2:AA' . $sheet_data_recon->getHighestRow())->setConditionalStyles($conditionalStyles);

            // Function to apply header formatting with optional text color
            function applyHeaderFormatting(Worksheet $sheet, $range, $bgColor, $textColor = null)
            {
                $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);
                if ($textColor !== null) {
                    $sheet->getStyle($range)->getFont()->getColor()->setARGB($textColor);
                }
            }

            // Apply conditional formatting to header row
            applyHeaderFormatting($sheet_data_recon, 'A1:O1', 'FFC000'); // Columns A to O with color FFC000
            applyHeaderFormatting($sheet_data_recon, 'P1:V1', '00B050'); // Columns P to V with color 00B050
            applyHeaderFormatting($sheet_data_recon, 'W1', 'ED7D31'); // Columns W with color ED7D31
            applyHeaderFormatting($sheet_data_recon, 'X1:AA1', '002060', 'FFFFFF'); // Columns X to AA with color 002060 and text color 000000

            // Iterate through DATA_RECON sheet starting from row 2
            $rowIndex = 2;
            while ($sheet_data_recon->getCell('H' . $rowIndex)->getValue() !== null) {
                $trx_id = $sheet_data_recon->getCell('H' . $rowIndex)->getValue();
                $activation_status = $sheet_data_recon->getCell('Y' . $rowIndex)->getValue();
                $recharge_status = $sheet_data_recon->getCell('Z' . $rowIndex)->getValue();
                $package_status = $sheet_data_recon->getCell('AA' . $rowIndex)->getValue();
                $updated = $sheet_data_recon->getCell('O' . $rowIndex)->getValue();

                $gui_update_value = "$trx_id|$updated|$activation_status|$recharge_status|$package_status";
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
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

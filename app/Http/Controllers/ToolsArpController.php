<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ToolsArpController extends Controller
{
    public function importFilesArp(Request $request)
    {
        try {
            // ========================================================================================================================
            // Validation for the uploaded files
            // ========================================================================================================================
            // Validate the request to ensure the file is present
            if (!$request->hasFile('recon_file_arp')) {
                throw new \Exception('No file uploaded');
            }
            $file = $request->file('recon_file_arp');
            // Check if the file is valid
            if (!$file->isValid()) {
                throw new \Exception('File upload error');
            }

            // ========================================================================================================================
            // Load the Excel file and get the required sheets
            // ========================================================================================================================
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet_data_recon = $spreadsheet->getSheetByName('DATA_RECON');
            $sheet_already_success = $spreadsheet->getSheetByName('ALREADY_SUCCESS');
            $sheet_los = $spreadsheet->getSheetByName('LOS');
            $sheet_upcc = $spreadsheet->getSheetByName('UPCC');
            $sheet_tc = $spreadsheet->getSheetByName('TC');
            $sheet_dom = $spreadsheet->getSheetByName('DOM');
            $sheet_ngrs = $spreadsheet->getSheetByName('NGRS');
            $sheet_summary = $spreadsheet->getSheetByName('SUMMARY');
            $sheet_format_gui_update_arp = $spreadsheet->getSheetByName('FORMAT_UPDATE_GUI_ARP');
            $sheet_format_gui_update_ap_byu_ap = $spreadsheet->getSheetByName('FORMAT_UPDATE_GUI_AP_BYU_AP');

            // ========================================================================================================================
            // Add new required columns headers to DATA_RECON sheet, FORMAT_UPDATE_GUI_ARP sheet, and FORMAT_UPDATE_GUI_AP_BYU_AP sheet
            // ========================================================================================================================
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
            $sheet_format_gui_update_arp->setCellValue('A1', 'TRX_ID');
            $sheet_format_gui_update_arp->setCellValue('B1', 'PARAMETER_STATUS_UPDATE');
            $sheet_format_gui_update_arp->setCellValue('C1', 'ACTIVATION_STATUS');
            $sheet_format_gui_update_arp->setCellValue('D1', 'RECHARGE_STATUS');
            $sheet_format_gui_update_arp->setCellValue('E1', 'PACKAGE_STATUS');
            $sheet_format_gui_update_arp->setCellValue('F1', 'TRX_TYPE');
            $sheet_format_gui_update_arp->setCellValue('G1', 'GUI_UPDATE_ARP');
            $sheet_format_gui_update_ap_byu_ap->setCellValue('A1', 'TRX_ID');
            $sheet_format_gui_update_ap_byu_ap->setCellValue('B1', 'PARAMETER_STATUS_UPDATE');
            $sheet_format_gui_update_ap_byu_ap->setCellValue('C1', 'ACTIVATION_STATUS');
            $sheet_format_gui_update_ap_byu_ap->setCellValue('D1', 'RECHARGE_STATUS');
            $sheet_format_gui_update_ap_byu_ap->setCellValue('E1', 'PACKAGE_STATUS');
            $sheet_format_gui_update_ap_byu_ap->setCellValue('F1', 'TRX_TYPE');
            $sheet_format_gui_update_ap_byu_ap->setCellValue('G1', 'GUI_UPDATE_AP_BYU_AP');

            $sheet_data_recon->getStyle('G2:G' . $sheet_data_recon->getHighestRow())->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
            $sheet_data_recon->getStyle('Q2:Q' . $sheet_data_recon->getHighestRow())->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');

            // ========================================================================================================================
            // Vlookup for LOS
            // ========================================================================================================================
            // Create a lookup array for LOS sheet starting from row 2
            $los_lookup = [];
            if ($sheet_los->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // Iterate through DATA_RECON sheet and update LOS column
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // ========================================================================================================================
            // Vlookup for UPCC
            // ========================================================================================================================
            // Create a lookup array for UPCC sheet starting from row 2
            $upcc_lookup = [];
            if ($sheet_upcc->getHighestRow() >= 2) { // Check if there are at least 2 rows
                foreach ($sheet_upcc->getRowIterator(2) as $row) { // Start from row 2 to skip header
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
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
            }

            // Iterate through DATA_RECON sheet and update UPCC column
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // ========================================================================================================================
            // Vlookup for TC
            // ========================================================================================================================
            // Create a lookup array for TC sheet starting from row 2
            $tc_lookup = [];
            if ($sheet_tc->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // Iterate through DATA_RECON sheet and update TC_STATUS column
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // ========================================================================================================================
            // Vlookup for DOM
            // ========================================================================================================================
            // Create a lookup array for DOM sheet starting from row 2
            $dom_lookup = [];
            if ($sheet_dom->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // Iterate through DATA_RECON sheet and update DOM column
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // ========================================================================================================================
            // Vlookup for NGRS
            // ========================================================================================================================
            // Create a lookup array for NGRS sheet starting from row 2
            $ngrs_lookup = [];
            if ($sheet_ngrs->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // Iterate through DATA_RECON sheet and update NGRS column
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
                foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    $trx_id = $cells[7]; // Assuming trx_id is in column H
                    if (isset($ngrs_lookup[$trx_id])) {
                        $sheet_data_recon->setCellValue('V' . $row->getRowIndex(), $ngrs_lookup[$trx_id]); // Assuming NGRS should be placed in column V
                    }
                }
            }

            // ========================================================================================================================
            // Vlookup for ALREADY_SUCCESS
            // ========================================================================================================================
            // Create a lookup array for ALREADY_SUCCESS sheet starting from row 2
            $already_success_lookup = [];
            if ($sheet_already_success->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // Iterate through DATA_RECON sheet and update PARAMETER_ALREADY_SUCCESS column
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
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
            }

            // ========================================================================================================================
            // Set ACTIVATION_STATUS in column X to SUKSES or GAGAL based on conditions
            // ========================================================================================================================
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

            // ========================================================================================================================
            // Set PACKAGE_STATUS in column AA and INTERVAL_TRX_DATE_TO_UPCC in column R
            // ========================================================================================================================
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

            // ========================================================================================================================
            // Set RECHARGE_STATUS in column Z
            // ========================================================================================================================
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
                $ngrs = $cells[21]; // Assuming NGRS is in column V

                if ($trx_type !== 'BYU_AP' && empty($parameter_already_success) && ($tc_status === 'A' || $tc_status === 'ACTIVE') && $ngrs === 'Completed') {
                    $sheet_data_recon->setCellValue('Z' . $row->getRowIndex(), 'SUKSES'); // Set RECHARGE_STATUS to SUKSES in column Z
                } elseif ($trx_type === 'BYU_AP' && empty($parameter_already_success) && $ngrs === 'Completed') {
                    $sheet_data_recon->setCellValue('Z' . $row->getRowIndex(), 'SUKSES'); // Set RECHARGE_STATUS to SUKSES in column Z
                } else {
                    $sheet_data_recon->setCellValue('Z' . $row->getRowIndex(), 'GAGAL'); // Set RECHARGE_STATUS to GAGAL in column Z
                }
            }

            // ========================================================================================================================
            // Set PARAMETER_STATUS_UPDATE in column X and UPDATED in column O
            // ========================================================================================================================
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

            // ========================================================================================================================
            // Set data in FORMAT_UPDATE_GUI_AP_BYU_AP sheet
            // ========================================================================================================================
            // Iterate through each row in DATA_RECON sheet
            $rowIndex = 2; // Assuming the first row is the header
            $outputRowIndex = 2; // Start from the second row in FORMAT_UPDATE_GUI_ARP
            while ($sheet_data_recon->getCell('H' . $rowIndex)->getValue() !== null) {
                $trxType = $sheet_data_recon->getCell('N' . $rowIndex)->getValue();
                if ($trxType === 'ARP') {
                    $trxId = $sheet_data_recon->getCell('H' . $rowIndex)->getValue();
                    $parameterStatusUpdate = $sheet_data_recon->getCell('X' . $rowIndex)->getValue();
                    $activationStatus = $sheet_data_recon->getCell('Y' . $rowIndex)->getValue();
                    $rechargeStatus = $sheet_data_recon->getCell('Z' . $rowIndex)->getValue();
                    $packageStatus = $sheet_data_recon->getCell('AA' . $rowIndex)->getValue();

                    $sheet_format_gui_update_arp->setCellValue('A' . $outputRowIndex, $trxId);
                    $sheet_format_gui_update_arp->setCellValue('B' . $outputRowIndex, $parameterStatusUpdate);
                    $sheet_format_gui_update_arp->setCellValue('C' . $outputRowIndex, $activationStatus);
                    $sheet_format_gui_update_arp->setCellValue('D' . $outputRowIndex, $rechargeStatus);
                    $sheet_format_gui_update_arp->setCellValue('E' . $outputRowIndex, $packageStatus);
                    $sheet_format_gui_update_arp->setCellValue('F' . $outputRowIndex, $trxType);
                    $sheet_format_gui_update_arp->setCellValue('G' . $outputRowIndex, "$trxId|$parameterStatusUpdate|$activationStatus|$rechargeStatus|$packageStatus");

                    $outputRowIndex++;
                }
                $rowIndex++;
            }

            // ========================================================================================================================
            // Set data in FORMAT_UPDATE_GUI_AP_BYU_AP sheet
            // ========================================================================================================================
            // Iterate through each row in DATA_RECON sheet
            $rowIndex = 2; // Assuming the first row is the header
            $outputRowIndex = 2; // Start from the second row in FORMAT_UPDATE_GUI_AP_BYU_AP
            while ($sheet_data_recon->getCell('H' . $rowIndex)->getValue() !== null) {
                $trxType = $sheet_data_recon->getCell('N' . $rowIndex)->getValue();
                if ($trxType === 'AP' || $trxType === 'BYU_AP') {
                    $trxId = $sheet_data_recon->getCell('H' . $rowIndex)->getValue();
                    $parameterStatusUpdate = $sheet_data_recon->getCell('X' . $rowIndex)->getValue();
                    $activationStatus = $sheet_data_recon->getCell('Y' . $rowIndex)->getValue();
                    $rechargeStatus = $sheet_data_recon->getCell('Z' . $rowIndex)->getValue();
                    $packageStatus = $sheet_data_recon->getCell('AA' . $rowIndex)->getValue();

                    $sheet_format_gui_update_ap_byu_ap->setCellValue('A' . $outputRowIndex, $trxId);
                    $sheet_format_gui_update_ap_byu_ap->setCellValue('B' . $outputRowIndex, $parameterStatusUpdate);
                    $sheet_format_gui_update_ap_byu_ap->setCellValue('C' . $outputRowIndex, $activationStatus);
                    $sheet_format_gui_update_ap_byu_ap->setCellValue('D' . $outputRowIndex, $rechargeStatus);
                    $sheet_format_gui_update_ap_byu_ap->setCellValue('E' . $outputRowIndex, $packageStatus);
                    $sheet_format_gui_update_ap_byu_ap->setCellValue('F' . $outputRowIndex, $trxType);
                    $sheet_format_gui_update_ap_byu_ap->setCellValue('G' . $outputRowIndex, "$trxId|$parameterStatusUpdate|$activationStatus|$rechargeStatus|$packageStatus");

                    $outputRowIndex++;
                }
                $rowIndex++;
            }

            // ========================================================================================================================
            // Create format for entire sheet DATA_RECON and SUMMARY
            // ========================================================================================================================

            // Set default font to Calibri for the entire workbook
            $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');

            // Auto-adjust column width for all sheets
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            }

            // Define the columns to apply conditional formatting in DATA_RECON sheet
            $columns = ['X', 'Y', 'Z', 'AA'];

            // Loop through each column and apply conditional formatting
            foreach ($columns as $column) {
                // Get the current conditional styles for the column
                $conditionalStyles = $sheet_data_recon->getStyle($column . '2:' . $column . $sheet_data_recon->getHighestRow())->getConditionalStyles();

                // Green background if the value is SUKSES
                $conditionalGreen = new Conditional();
                $conditionalGreen->setConditionType(Conditional::CONDITION_CELLIS)
                    ->setOperatorType(Conditional::OPERATOR_EQUAL)
                    ->addCondition('"SUKSES"');
                $conditionalGreen->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00B050');

                // Red background if the value is GAGAL
                $conditionalRed = new Conditional();
                $conditionalRed->setConditionType(Conditional::CONDITION_CELLIS)
                    ->setOperatorType(Conditional::OPERATOR_EQUAL)
                    ->addCondition('"GAGAL"');
                $conditionalRed->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');

                // Add the conditional styles to the column
                $conditionalStyles[] = $conditionalGreen;
                $conditionalStyles[] = $conditionalRed;

                // Apply the conditional styles to the column
                $sheet_data_recon->getStyle($column . '2:' . $column . $sheet_data_recon->getHighestRow())->setConditionalStyles($conditionalStyles);
            }

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

            $sheet_data_recon->setAutoFilter('A1:AA' . $sheet_data_recon->getHighestRow());

            // Apply styles to headers in SUMMARY sheet
            $summary_headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '145F82']]
            ];
            $summary_trx_typeStyle = [
                'font' => ['bold' => true],
            ];
            $summary_bodyStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ];
            $summary_footerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF']
                ],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '145F82']]
            ];

            $sheet_summary->getStyle('B3:F4')->applyFromArray($summary_headerStyle);
            $sheet_summary->getStyle('B3:B4')->applyFromArray($summary_headerStyle);
            $sheet_summary->getStyle('C3:C4')->applyFromArray($summary_headerStyle);
            $sheet_summary->getStyle('D3:F3')->applyFromArray($summary_headerStyle);
            $sheet_summary->getStyle('B5:B10')->applyFromArray($summary_trx_typeStyle);
            $sheet_summary->getStyle('B11:B12')->applyFromArray($summary_footerStyle);
            $sheet_summary->getStyle('F11:F12')->applyFromArray($summary_footerStyle);
            $sheet_summary->getStyle('B5:F12')->applyFromArray($summary_bodyStyle);

            // ========================================================================================================================
            // Create SUMMARY in SUMMARY sheet
            // ========================================================================================================================

            // Initialize arrays to store counts and sums
            $summaryData = [
                'AP' => ['count_updated' => 0, 'sum_price' => 0, 'count_gagal' => 0, 'sum_price_gagal' => 0, 'count_sukses' => 0, 'sum_price_sukses' => 0],
                'ARP' => ['count_updated' => 0, 'sum_price' => 0, 'count_gagal' => 0, 'sum_price_gagal' => 0, 'count_sukses' => 0, 'sum_price_sukses' => 0],
                'BYU_AP' => ['count_updated' => 0, 'sum_price' => 0, 'count_gagal' => 0, 'sum_price_gagal' => 0, 'count_sukses' => 0, 'sum_price_sukses' => 0],
            ];

            // Iterate through the rows of the DATA_RECON sheet
            foreach ($sheet_data_recon->getRowIterator(2) as $row) {
                $trx_type = $sheet_data_recon->getCell('N' . $row->getRowIndex())->getValue();
                $updated = $sheet_data_recon->getCell('O' . $row->getRowIndex())->getValue();
                $price = $sheet_data_recon->getCell('I' . $row->getRowIndex())->getValue();
                $parameter_status_update = $sheet_data_recon->getCell('X' . $row->getRowIndex())->getValue();

                if (isset($summaryData[$trx_type])) {
                    // Count UPDATED
                    if ($updated) {
                        $summaryData[$trx_type]['count_updated']++;
                    }

                    // Sum PRICE
                    if ($price) {
                        $summaryData[$trx_type]['sum_price'] += $price;
                    }

                    // Count and sum for SUKSES and GAGAL
                    if ($parameter_status_update === 'SUKSES') {
                        $summaryData[$trx_type]['count_sukses']++;
                        $summaryData[$trx_type]['sum_price_sukses'] += $price;
                    } elseif ($parameter_status_update === 'GAGAL') {
                        $summaryData[$trx_type]['count_gagal']++;
                        $summaryData[$trx_type]['sum_price_gagal'] += $price;
                    }
                }
            }

            // Write the summary data to the SUMMARY sheet
            $sheet_summary->setCellValue('B3', 'TRX_TYPE');
            $sheet_summary->setCellValue('C3', 'Values');
            $sheet_summary->setCellValue('D3', 'UPDATED');
            $sheet_summary->setCellValue('D4', 'GAGAL');
            $sheet_summary->setCellValue('E4', 'SUKSES');
            $sheet_summary->setCellValue('F4', 'Grand Total');

            // Merge cells for headers
            $sheet_summary->mergeCells('B3:B4');
            $sheet_summary->mergeCells('C3:C4');
            $sheet_summary->mergeCells('D3:F3');

            // Set headers for TRX_TYPE
            $sheet_summary->setCellValue('B5', 'AP');
            $sheet_summary->setCellValue('B7', 'ARP');
            $sheet_summary->setCellValue('B9', 'BYU_AP');
            $sheet_summary->setCellValue('B11', 'Total Count of UPDATED');
            $sheet_summary->setCellValue('B12', 'Total Sum of PRICE');

            // Set values for counts and sums
            $sheet_summary->setCellValue('C5', 'Count of UPDATED');
            $sheet_summary->setCellValue('C6', 'Sum of PRICE');
            $sheet_summary->setCellValue('C7', 'Count of UPDATED');
            $sheet_summary->setCellValue('C8', 'Sum of PRICE');
            $sheet_summary->setCellValue('C9', 'Count of UPDATED');
            $sheet_summary->setCellValue('C10', 'Sum of PRICE');

            // Calculate totals
            $total_count_updated = $summaryData['AP']['count_updated'] + $summaryData['ARP']['count_updated'] + $summaryData['BYU_AP']['count_updated'];
            $total_sum_price = $summaryData['AP']['sum_price'] + $summaryData['ARP']['sum_price'] + $summaryData['BYU_AP']['sum_price'];

            $sheet_summary->setCellValue('F11', $total_count_updated);
            $sheet_summary->setCellValue('F12', $total_sum_price);

            // Set values for GAGAL, SUKSES, and Grand Total
            $sheet_summary->setCellValue('D5', $summaryData['AP']['count_gagal']);
            $sheet_summary->setCellValue('D6', $summaryData['AP']['sum_price_gagal']);
            $sheet_summary->setCellValue('D7', $summaryData['ARP']['count_gagal']);
            $sheet_summary->setCellValue('D8', $summaryData['ARP']['sum_price_gagal']);
            $sheet_summary->setCellValue('D9', $summaryData['BYU_AP']['count_gagal']);
            $sheet_summary->setCellValue('D10', $summaryData['BYU_AP']['sum_price_gagal']);

            $sheet_summary->setCellValue('E5', $summaryData['AP']['count_sukses']);
            $sheet_summary->setCellValue('E6', $summaryData['AP']['sum_price_sukses']);
            $sheet_summary->setCellValue('E7', $summaryData['ARP']['count_sukses']);
            $sheet_summary->setCellValue('E8', $summaryData['ARP']['sum_price_sukses']);
            $sheet_summary->setCellValue('E9', $summaryData['BYU_AP']['count_sukses']);
            $sheet_summary->setCellValue('E10', $summaryData['BYU_AP']['sum_price_sukses']);

            $sheet_summary->setCellValue('F5', $summaryData['AP']['count_updated']);
            $sheet_summary->setCellValue('F6', $summaryData['AP']['sum_price']);
            $sheet_summary->setCellValue('F7', $summaryData['ARP']['count_updated']);
            $sheet_summary->setCellValue('F8', $summaryData['ARP']['sum_price']);
            $sheet_summary->setCellValue('F9', $summaryData['BYU_AP']['count_updated']);
            $sheet_summary->setCellValue('F10', $summaryData['BYU_AP']['sum_price']);

            // ========================================================================================================================
            // Save the updated Excel file
            // ========================================================================================================================
            // Create CSV files
            $csv_arp_filename = 'FORMAT_UPDATE_GUI_ARP_' . date('Ymd') . '.csv';
            $csv_ap_byu_ap_filename = 'FORMAT_UPDATE_GUI_AP_BYU_AP_' . date('Ymd') . '.csv';
            $csv_arp = fopen($csv_arp_filename, 'w');
            $csv_ap_byu_ap = fopen($csv_ap_byu_ap_filename, 'w');

            // Write data from FORMAT_UPDATE_GUI_ARP to CSV
            $rowIndex = 2; // Start from the second row
            while ($sheet_format_gui_update_arp->getCell('G' . $rowIndex)->getValue() !== null) {
                $value = $sheet_format_gui_update_arp->getCell('G' . $rowIndex)->getValue();
                fwrite($csv_arp, $value . PHP_EOL);
                $rowIndex++;
            }

            // Write data from FORMAT_UPDATE_GUI_AP_BYU_AP to CSV
            $rowIndex = 2; // Start from the second row
            while ($sheet_format_gui_update_ap_byu_ap->getCell('G' . $rowIndex)->getValue() !== null) {
                $value = $sheet_format_gui_update_ap_byu_ap->getCell('G' . $rowIndex)->getValue();
                fwrite($csv_ap_byu_ap, $value . PHP_EOL);
                $rowIndex++;
            }

            // Close CSV files
            fclose($csv_arp);
            fclose($csv_ap_byu_ap);

            // Save the original Excel file
            $filename_excel = 'REKON_AP_ARP_BYU_AP_' . date('Ymd') . '.xlsx';
            $writer_excel = new Xlsx($spreadsheet);
            $writer_excel->save($filename_excel);

            // Create a zip file to bundle all files
            $zip = new \ZipArchive();
            $zip_filename = 'REKON_AP_ARP_BYU_AP_' . date('Ymd') . '.zip';
            if ($zip->open($zip_filename, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($csv_arp_filename, basename($csv_arp_filename));
                $zip->addFile($csv_ap_byu_ap_filename, basename($csv_ap_byu_ap_filename));
                $zip->addFile($filename_excel, basename($filename_excel));
                $zip->close();
            } else {
                throw new \Exception('Failed to create zip file');
            }

            // Return the response to download the zip file
            return response()->download($zip_filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        } finally {
            // Clean up temporary files
            if (isset($csv_arp_filename)) {
                unlink($csv_arp_filename);
            }
            if (isset($csv_ap_byu_ap_filename)) {
                unlink($csv_ap_byu_ap_filename);
            }
            if (isset($filename_excel)) {
                unlink($filename_excel);
            }
        }
    }
}

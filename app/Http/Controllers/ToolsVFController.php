<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ToolsVFController extends Controller
{
    public function importFilesVf(Request $request)
    {
        try {
            // ========================================================================================================================
            // Validation for the uploaded files
            // ========================================================================================================================
            if (!$request->hasFile('recon_file_vf')) {
                throw new \Exception('No file uploaded');
            }
            $file = $request->file('recon_file_vf');

            if (!$file->isValid()) {
                throw new \Exception('File upload error');
            }

            // ========================================================================================================================
            // Load the Excel file and get the required sheets
            // ========================================================================================================================
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet_data_recon = $spreadsheet->getSheetByName('DATA_RECON');
            $sheet_already_success = $spreadsheet->getSheetByName('ALREADY_SUCCESS');
            $sheet_dom = $spreadsheet->getSheetByName('DOM');
            $sheet_urp = $spreadsheet->getSheetByName('URP');
            $sheet_format_gui_update = $spreadsheet->getSheetByName('FORMAT_GUI_UPDATE');

            $sheet_data_recon->setCellValue('O1', 'UPDATED');
            $sheet_data_recon->setCellValue('P1', 'DOM_TRX_DATE');
            $sheet_data_recon->setCellValue('Q1', 'INTERVAL_TRX_DATE_TO_DOM');
            $sheet_data_recon->setCellValue('R1', 'DOM_STATUS');
            $sheet_data_recon->setCellValue('S1', 'URP_STATUS');
            $sheet_data_recon->setCellValue('T1', 'PARAMETER_ALREADY_SUCCESS');
            $sheet_data_recon->setCellValue('U1', 'PARAMETER_STATUS_UPDATE');
            $sheet_format_gui_update->setCellValue('A1', 'PHYSICAL_VOUCHER_ID');
            $sheet_format_gui_update->setCellValue('B1', 'PARAMETER_STATUS_UPDATE');
            $sheet_format_gui_update->setCellValue('C1', 'GUI_UPDATE');

            // ========================================================================================================================
            // Vlookup for ALREADY_SUCCESS
            // ========================================================================================================================
            // Create a lookup array for ALREADY_SUCCESS sheet starting from row 2
            $already_success_lookup = [];
            if ($sheet_already_success->getHighestRow() >= 2) {
                foreach ($sheet_already_success->getRowIterator(2) as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    if (!empty($cells[0])) {
                        $already_success_lookup[$cells[7]] = $cells[15]; // Assuming trx_id is in column H and REASON is in column P
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
                        $sheet_data_recon->setCellValue('T' . $row->getRowIndex(), $already_success_lookup[$trx_id]); // Assuming PARAMETER_ALREADY_SUCCESS should be placed in column T
                    }
                }
            }

            // ========================================================================================================================
            // Create a lookup array for URP sheet starting from row 2
            // ========================================================================================================================
            $urp_lookup = [];
            if ($sheet_urp->getHighestRow() >= 2) { // Check if there are at least 2 rows
                foreach ($sheet_urp->getRowIterator(2) as $row) { // Start from row 2 to skip header
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    if (!empty($cells[0])) { // Assuming SERIAL_NUMBER is in column A
                        $serial_number = $cells[0];
                        $urp_status = $cells[1]; // Assuming status is in column B
                        $urp_lookup[$serial_number] = $urp_status;
                    }
                }
            }

            // Iterate through DATA_RECON sheet and update URP_STATUS column
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
                foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    $serial_number = $cells[3]; // Assuming SERIAL_NUMBER is in column D
                    if (isset($urp_lookup[$serial_number])) {
                        $sheet_data_recon->setCellValue('S' . $row->getRowIndex(), $urp_lookup[$serial_number]); // Assuming URP_STATUS should be placed in column S
                    }
                }
            }

            // ========================================================================================================================
            // Create a lookup array for DOM sheet starting from row 2
            // ========================================================================================================================
            $dom_lookup = [];
            if ($sheet_dom->getHighestRow() >= 2) { // Check if there are at least 2 rows
                foreach ($sheet_dom->getRowIterator(2) as $row) { // Start from row 2 to skip header
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    if (!empty($cells[2])) { // Assuming TRX_ID is in column C
                        $trx_id = $cells[2];
                        $dom_trx_date = $cells[0]; // Assuming DOM_TRX_DATE is in column A
                        $status_code = $cells[6]; // Assuming statusCode is in column G
                        $dom_lookup[$trx_id][] = ['dom_trx_date' => $dom_trx_date, 'status_code' => $status_code];
                    }
                }
            }

            // ========================================================================================================================
            // Iterate through DATA_RECON sheet and update DOM_TRX_DATE, statusCode, datetime difference, and URP_STATUS columns
            // ========================================================================================================================
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
                foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    $trx_id = $cells[7]; // Assuming TRX_ID is in column H
                    $created_at_str = $cells[6]; // Assuming CREATED_AT is in column G
                    $serial_number = $cells[3]; // Assuming SERIAL_NUMBER is in column D

                    // Convert CREATED_AT to the desired format
                    $created_at = DateTime::createFromFormat('d-M-y h.i.s.u A', $created_at_str)->format('Y-m-d H:i:s');

                    if (isset($dom_lookup[$trx_id])) {
                        $dom_dates = $dom_lookup[$trx_id];
                        $closest_date = null;
                        $latest_date = null;
                        $selected_status_code = null;
                        foreach ($dom_dates as $dom_date) {
                            $dom_date_obj = DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $dom_date['dom_trx_date']);
                            $dom_date_str = $dom_date_obj->format('Y-m-d H:i:s');
                            if ($dom_date_obj > $created_at) {
                                if ($dom_date['status_code'] == "OK00") {
                                    if ($closest_date === null || $dom_date_obj < $closest_date) {
                                        $closest_date = $dom_date_obj;
                                        $selected_status_code = $dom_date['status_code'];
                                    }
                                }
                                if ($latest_date === null || $dom_date_obj > $latest_date) {
                                    $latest_date = $dom_date_obj;
                                    $selected_status_code = $dom_date['status_code'];
                                }
                            }
                        }
                        if ($closest_date !== null) {
                            $selected_date = $closest_date;
                        } else {
                            $selected_date = $latest_date;
                        }
                        if ($selected_date !== null) {
                            $sheet_data_recon->setCellValue('P' . $row->getRowIndex(), $selected_date->format('Y-m-d H:i:s'));
                            $sheet_data_recon->setCellValue('R' . $row->getRowIndex(), $selected_status_code);
                            $interval = $selected_date->diff(new DateTime($created_at));
                            $interval_str = $interval->format('%H:%I:%S');
                            $sheet_data_recon->setCellValue('Q' . $row->getRowIndex(), $interval_str);
                        }
                    }

                    // Set PARAMETER_STATUS_UPDATE based on conditions
                    $parameter_already_success = $cells[19]; // Assuming PARAMETER_ALREADY_SUCCESS is in column T
                    if (empty($parameter_already_success) && $urp_status > 0) {
                        if ($selected_status_code == "OK00") {
                            $sheet_data_recon->setCellValue('O' . $row->getRowIndex(), "SUKSES");
                            $sheet_data_recon->setCellValue('U' . $row->getRowIndex(), "SUKSES");
                        } else {
                            $sheet_data_recon->setCellValue('O' . $row->getRowIndex(), "SUKSES");
                            $sheet_data_recon->setCellValue('U' . $row->getRowIndex(), "SUKSES");
                        }
                    } else {
                        $sheet_data_recon->setCellValue('O' . $row->getRowIndex(), "GAGAL");
                        $sheet_data_recon->setCellValue('U' . $row->getRowIndex(), "GAGAL");
                    }
                }
            }

            // ========================================================================================================================
            // Update FORMAT_GUI_UPDATE sheet
            // ========================================================================================================================
            $row_index = 2; // Start from row 2 to skip header
            if ($sheet_data_recon->getHighestRow() >= 2) { // Check if there are at least 2 rows
                foreach ($sheet_data_recon->getRowIterator(2) as $row) { // Start from row 2 to skip header
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    $physical_id = $cells[0]; // Assuming PHYSICAL_ID is in column A
                    $parameter_status_update = $cells[20]; // Assuming PARAMETER_STATUS_UPDATE is in column U

                    $sheet_format_gui_update->setCellValue('A' . $row_index, $physical_id);
                    $sheet_format_gui_update->setCellValue('B' . $row_index, $parameter_status_update);
                    $sheet_format_gui_update->setCellValue('C' . $row_index, $physical_id . '|' . $parameter_status_update);

                    $row_index++;
                }
            }

            // Set default font to Calibri for the entire workbook
            $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');

            // Auto-adjust column width for all sheets
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            }

            // Define the columns to apply conditional formatting in DATA_RECON sheet
            $columns = ['O', 'U'];

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
            applyHeaderFormatting($sheet_data_recon, 'P1:S1', '00B050'); // Columns P to R with color 00B050
            applyHeaderFormatting($sheet_data_recon, 'T1', 'ED7D31'); // Columns W with color ED7D31
            applyHeaderFormatting($sheet_data_recon, 'U1', '002060', 'FFFFFF'); // Columns X to AA with color 002060 and text color FFFFFF

            $sheet_data_recon->setAutoFilter('A1:U' . $sheet_data_recon->getHighestRow());

            // ========================================================================================================================
            // Save the updated Excel file
            // ========================================================================================================================
            // Create CSV files
            $csv_vas_filename = 'FORMAT_UPDATE_GUI_VF_' . date('Ymd') . '.csv';
            $csv_vas = fopen($csv_vas_filename, 'w');

            // Write data from FORMAT_UPDATE_GUI to CSV
            $rowIndex = 2; // Start from the second row
            while ($sheet_format_gui_update->getCell('C' . $rowIndex)->getValue() !== null) {
                $value = $sheet_format_gui_update->getCell('C' . $rowIndex)->getValue();
                fwrite($csv_vas, $value . PHP_EOL);
                $rowIndex++;
            }

            // Close CSV files
            fclose($csv_vas);

            // Save the original Excel file
            $filename_excel = 'REKON_VF_INIT_DIPROSES' . date('Ymd') . '.xlsx';
            $writer_excel = new Xlsx($spreadsheet);
            $writer_excel->save($filename_excel);

            // Create a zip file to bundle all files
            $zip = new \ZipArchive();
            $zip_filename = 'REKON_VF_INIT_DIPROSES' . date('Ymd') . '.zip';
            if ($zip->open($zip_filename, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($csv_vas_filename);
                $zip->addFile($filename_excel);
                $zip->close();
            }

            // Return the response to download the zip file
            return response()->download($zip_filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        } finally {
            // Delete the temporary files after the download is complete
            if (file_exists($csv_vas_filename)) {
                unlink($csv_vas_filename);
            }
            if (file_exists($filename_excel)) {
                unlink($filename_excel);
            }
        }
    }
}

<?php

$file = $request->file('recon_file_arp');
$spreadsheet = IOFactory::load($file->getPathname());
$sheet = $spreadsheet->getSheetByName('DATA_RECON');

if ($sheet) {
    $sheet->setCellValue('O1', 'UPDATED');
    $data = $sheet->toArray();

    return view('preview', compact('data'));

    // $writer = new Xlsx($spreadsheet);
    // $filename = 'recon_updated_' . date('YmdHis') . '.xlsx';
    // $writer->save($filename);

    // return response()->download($filename)->deleteFileAfterSend(true);
} else {
    return back()->withErrors(['error' => 'Invalid file format']);
}

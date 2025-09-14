<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

include "config.php";

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$sql = "SELECT s.id, c.customer_name, 
               s.spx, s.anter, s.sicepat, 
               s.jnt, s.jne, s.jnt_cargo, 
               s.jne_cargo, s.pos, s.total
        FROM shipments s 
        JOIN customers c ON s.customer_id = c.id
        WHERE s.shipment_date = ?
        ORDER BY c.customer_name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Mutation_'.$date);

// Header
$headers = ['ID','Customer Name','Spx','Anter','Sicepat','J&T','JNE','JNT Cargo','JNE Cargo','Pos','Total'];
$sheet->fromArray($headers, NULL, 'A1');

// Styling header
$sheet->getStyle('A1:K1')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb'=>'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb'=>'4CAF50']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$rowNumber = 2;

// Inisialisasi total per kolom ekspedisi
$totals = [
    'spx'=>0, 'anter'=>0, 'sicepat'=>0, 'jnt'=>0, 'jne'=>0, 
    'jnt_cargo'=>0, 'jne_cargo'=>0, 'pos'=>0, 'total'=>0
];

while($row = $result->fetch_assoc()){
    $sheet->setCellValue('A'.$rowNumber, $row['id'])
          ->setCellValue('B'.$rowNumber, $row['customer_name'])
          ->setCellValue('C'.$rowNumber, $row['spx'])
          ->setCellValue('D'.$rowNumber, $row['anter'])
          ->setCellValue('E'.$rowNumber, $row['sicepat'])
          ->setCellValue('F'.$rowNumber, $row['jnt'])
          ->setCellValue('G'.$rowNumber, $row['jne'])
          ->setCellValue('H'.$rowNumber, $row['jnt_cargo'])
          ->setCellValue('I'.$rowNumber, $row['jne_cargo'])
          ->setCellValue('J'.$rowNumber, $row['pos'])
          ->setCellValue('K'.$rowNumber, $row['total']);

    $sheet->getStyle("A{$rowNumber}:K{$rowNumber}")->applyFromArray([
        'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]
    ]);

    // Hitung total
    $totals['spx'] += $row['spx'];
    $totals['anter'] += $row['anter'];
    $totals['sicepat'] += $row['sicepat'];
    $totals['jnt'] += $row['jnt'];
    $totals['jne'] += $row['jne'];
    $totals['jnt_cargo'] += $row['jnt_cargo'];
    $totals['jne_cargo'] += $row['jne_cargo'];
    $totals['pos'] += $row['pos'];
    $totals['total'] += $row['total'];

    $rowNumber++;
}

// Tulis footer total
$sheet->setCellValue('B'.$rowNumber, 'TOTAL')
      ->setCellValue('C'.$rowNumber, $totals['spx'])
      ->setCellValue('D'.$rowNumber, $totals['anter'])
      ->setCellValue('E'.$rowNumber, $totals['sicepat'])
      ->setCellValue('F'.$rowNumber, $totals['jnt'])
      ->setCellValue('G'.$rowNumber, $totals['jne'])
      ->setCellValue('H'.$rowNumber, $totals['jnt_cargo'])
      ->setCellValue('I'.$rowNumber, $totals['jne_cargo'])
      ->setCellValue('J'.$rowNumber, $totals['pos'])
      ->setCellValue('K'.$rowNumber, $totals['total']);

// Styling footer
$sheet->getStyle("A{$rowNumber}:K{$rowNumber}")->applyFromArray([
    'font' => ['bold'=>true, 'color'=>['rgb'=>'FFFFFF']],
    'fill' => ['fillType'=>Fill::FILL_SOLID, 'startColor'=>['rgb'=>'2196F3']],
    'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]
]);

// Kolom Total All diberi warna oranye
$sheet->getStyle("K{$rowNumber}")->getFill()->setFillType(Fill::FILL_SOLID)
      ->getStartColor()->setRGB('FF9800');

// Auto size kolom
foreach(range('A','K') as $col){
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Nama file
$filename = "mutasi_empty_" . date('dmY') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>

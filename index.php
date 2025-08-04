<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once 'vendor/autoload.php';
$env = parse_ini_file('.env');

$y = date('Y');
$m = date('m') - 1;

$spreadsheet = new Spreadsheet();
$activeWorksheet = $spreadsheet->getActiveSheet();

$data = [
    ["Reiskosten declaratie sheet $m-$y"],
    ['Naam:', $env['PERSON_NAME']],
    '',
    '',
    ['Datum', 'Van (lokatie)', 'Naar (locatie)', 'Aantal KM retour', 'Bedrag'],
    '',
];

$dates = [];

$totalDays = cal_days_in_month(CAL_GREGORIAN, $m, $y) + 1;
$excludes = explode(',', $env['EXCLUDES']);

for ($i = 1; $i < $totalDays; $i++) {
    if (in_array("$y-$m-$i", $excludes)) {
        continue;
    }

    $d = strtotime("$y-$m-$i");
    $wn = (int)date('w', $d);

    if ($wn < 6 && $wn > 0) {
        $dates[] = "$i-$m-$y";
    }
}

foreach ($dates as $i => $date) {
    $data[] = [
        $date,
        $env['START'],
        $env['END'],
        $env['DISTANCE'],
        '=D' . ($i + 7) . '*' . $env['COSTS']
    ];
}

$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);

$activeWorksheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
$activeWorksheet->getStyle('A2:B2')->getFont()->setSize(12);
$activeWorksheet->getStyle('A5:E5')->getFont()->setBold(true);
$activeWorksheet->getStyle('C33')->getFont()->setBold(true);

$activeWorksheet->getStyle('C33')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);;
$activeWorksheet->getStyle('D7:D33')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);;
$activeWorksheet->getStyle('E7:E33')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);;

$activeWorksheet->getStyle('A5:E5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
$activeWorksheet->getStyle('A32:E32')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

$activeWorksheet->fromArray($data);

$activeWorksheet->getCell('C33')->setValue('Totaal');
$activeWorksheet->getCell('D33')->setValue('=SUM(D7:D32)');
$activeWorksheet->getCell('E33')->setValue('=SUM(E7:E32)');

$writer = new Xlsx($spreadsheet);

$filename = [
    $y,
    $m,
    $env['FILENAME'],
    $env['PERSON_NAME'],
];

$writer->save(__DIR__ . '/export/' . implode('-', $filename) . '.xlsx');

echo "\r\n";
echo 'Thank you for using DOS';
echo "\r\n";

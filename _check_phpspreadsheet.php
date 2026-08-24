<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__.'/vendor/autoload.php';

echo class_exists(Spreadsheet::class) ? 'SPREADSHEET_OK' : 'SPREADSHEET_MISSING';
echo PHP_EOL;

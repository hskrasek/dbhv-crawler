<?php

$csvData = explode(PHP_EOL, file_get_contents('games-shnateman.csv'));
$csvData = array_filter($csvData);
$csvData = array_unique($csvData);

$file = fopen('games-shnateman.fixed.csv', 'w');

foreach ($csvData as $line) {
    fputcsv($file, array_map(function ($line) {
        return trim($line, '"');
    }, explode(',', $line)));
}

fclose($file);
<?php

use Carbon\Carbon;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

require __DIR__ . '/vendor/autoload.php';

$original = Reader::createFromPath(__DIR__ . '/games-shnateman.gamertags.csv');
$fixed = Writer::createFromPath(__DIR__ . '/games-shnateman.gamertags.fixed.csv', 'w');
$fixed->insertOne(['Link', 'Date', 'Gamertags']);

$records = (new Statement)->offset(1)->process($original);

foreach ($records as $record) {
    $link = array_shift($record);
    $date = Carbon::parse(str_replace('.', '/', array_shift($record)));
    $gamertags = $record;
    sort($gamertags);
    $gamertags = implode(',', $gamertags);

    $fixed->insertOne([
        $link, $date->format('Y-m-d H:i:s'), $gamertags
    ]);
}

<?php

require __DIR__ . '/vendor/autoload.php';

use Goutte\Client;

$client = new Client();

$csvData = array_filter(explode(PHP_EOL, file_get_contents('games-shnateman.fixed.csv')));

$file = fopen('games-shnateman.gamertags.csv', 'w');

foreach ($csvData as $line) {
    $data = array_map(function ($line) {
        return trim($line, '"');
    }, explode(',', $line));
    if ($data[0] == 'Link') {
        fputcsv($file, $data);
        continue;
    }

    $crawler = $client->request('GET', $data[0]);
    // Get the list of gamertags
    $gamertags = $crawler->filter('td.playerInfo p a')->each(function ($node) {
        return $node->text();
    });


    $gamertags = array_filter(array_map(function ($gamertag) {
        return trim($gamertag);
    }, $gamertags), function ($gamertag) {
        return in_array($gamertag, [
            'HEATSEEKERBUNGE',
            'Evasive Ebu',
            'BioticJoker',
            'DrZoidbergOBGYN',
            'TheeExistential',
            'Kygrx',
            'bigdsdeath79',
            'A Token Asian',
            'shnateman',
        ]);
    });

    $data = array_merge($data, $gamertags);
    fputcsv($file, $data);
}

fclose($file);
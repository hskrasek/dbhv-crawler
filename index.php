<?php

require __DIR__ . '/vendor/autoload.php';

use Goutte\Client;

$client = new Client();

//$crawler = $client->request('GET',
//    'http://halo.bungie.net/Stats/Reach/GameStats.aspx?gameid=333357771&player=HEATSEEKERBUNGE');
//


//Get the game history, click through a page
$csv = fopen('games-shnateman.csv', 'w+');
fputcsv($csv, ['Link', 'Date']);
$crawler = $client->request('GET',
    'http://halo.bungie.net/stats/reach/playergamehistory.aspx?vc=0&player=shnateman');

try {
    do {
        $links = $crawler->filter('.gameHistoryTable')->filter('a')->links();
        $links = array_filter($links, function ($link) {
            return strpos($link->getUri(), 'GameStats') !== false;
        });
        echo 'There are ' . count($links) . ' links to check...' . PHP_EOL;
        foreach ($links as $link) {
            echo 'Checking game: ' . $link->getUri() . PHP_EOL;
            getGameStats($link, $csv);
        }
        $link = $crawler->selectLink('Next')->link();
        echo 'Moving to: ' . $link->getUri() . PHP_EOL;
        $crawler = $client->click($link);
    } while (count($links) != 0);
} catch (InvalidArgumentException $e) {
}
fclose($csv);

/**
 * @param \Symfony\Component\DomCrawler\Link $link
 */
function getGameStats($link, $csv)
{
    $crawler = (new Client)->click($link);
    // Get the list of gamertags
    $data = $crawler->filter('td.playerInfo p a')->each(function ($node) {
        return $node->text();
    });

    $data = array_filter(array_map(function ($gamertag) {
        return trim($gamertag);
    }, $data), function ($gamertag) {
        return in_array($gamertag, [
            'HEATSEEKERBUNGE',
            'Evasive Ebu',
            'BioticJoker',
            'DrZoidbergOBGYN',
            'TheeExistential',
            'Kygrx',
            'bigdsdeath79',
            'A Token Asian',
        ]);
    });

    if (count($data) == 0) {
        return;
    }

    echo 'Found game!' . PHP_EOL;

    //Get the time of the game
    $date = $crawler->filter('p.time')->each(function ($node) {
        return trim(explode('|', $node->text())[0]);
    });
    $date = reset($date);

    fputcsv($csv, [$crawler->getUri(), $date]);
}

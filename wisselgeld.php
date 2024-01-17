<?php

$wisselgeld = isset($argv[1]) ? $argv[1] : null;

try {
    if ($wisselgeld === null) {
        throw new InvalidArgumentException('Verkeerd aantal argumenten. Roep de applicatie aan op de volgende manier: wisselgeld.php <bedrag>');
    }

    $wisselgeld = str_replace(',', '.', $wisselgeld);
    $wisselgeld = filter_var($wisselgeld, FILTER_VALIDATE_FLOAT);

    if ($wisselgeld === false) {
        throw new InvalidArgumentException('Input moet een valide getal zijn');
    }

    if ($wisselgeld < 0) {
        throw new InvalidArgumentException('Input moet een positief getal zijn');
    }

    $wisselgeldafgerond = rondafnaar5cent($wisselgeld);

    define('GELDEENHEDEN_EURO', [50, 20, 10, 5, 2, 1]);
    define('GELDEENHEDEN_CENT', [50, 20, 10, 5]);

    $euroRestbedrag = floor($wisselgeldafgerond);
    $centRestbedrag = round(($wisselgeldafgerond - $euroRestbedrag) * 100);

    Valuta($euroRestbedrag, GELDEENHEDEN_EURO, 'euro');
    Valuta($centRestbedrag, GELDEENHEDEN_CENT, 'cent');
} catch (InvalidArgumentException $e) {
    echo 'Error opgevangen: ', $e->getMessage(), PHP_EOL;
    throw new Exception('An error occurred in the application.', 0, $e);
} catch (Exception $e) {
    echo 'An unexpected error occurred: ', $e->getMessage(), PHP_EOL;
} finally {
    $a = 2 + 3;
}

function rondafnaar5cent($amount)
{
    return round($amount * 2, 1) / 2;
}

function GeldWisselen($restbedrag, $geldeenheden)
{
    $resultaat = [];

    foreach ($geldeenheden as $geldeenheid) {
        if ($restbedrag >= $geldeenheid) {
            $aantalKeerGeldEenheidInRestBedrag = floor($restbedrag / $geldeenheid);
            $restbedrag %= $geldeenheid;
            $resultaat[] = ["aantal" => $aantalKeerGeldEenheidInRestBedrag, "waarde" => $geldeenheid];
        }
    }

    return $resultaat;
}

function printresultaat($resultaat, $euro)
{
    foreach ($resultaat as $item) {
        echo $item['aantal'] . " x " . $item['waarde'] . " $euro\n";
    }
}

function Valuta($restbedrag, $geldeenheden, $euro)
{
    $resultaat = GeldWisselen($restbedrag, $geldeenheden);
    printresultaat($resultaat, $euro);
}
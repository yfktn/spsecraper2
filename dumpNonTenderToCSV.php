<?php

use League\Csv\Writer;
use Yfktn\Spsescraper2\DBNonTenderVendor;

require 'vendor/autoload.php';

$db = new DBNonTenderVendor();
$pathCSV = __DIR__ . "/nontender.csv";
$csvWriter = Writer::createFromPath($pathCSV, "w");
$currentPage = 1;
$keepLooping = true;
$headerAdded = false;
$patternPengumuman = "https://lpse.kalteng.go.id/eproc4/nontender/%d/pengumumanpl";
echo "Ditulis ke {$pathCSV}, Dump halaman: \n";
do {
    $data = $db->getTenderWithVendor($currentPage);
    echo " {$currentPage} ";
    if(count($data) > 0) {
        if(!$headerAdded) {
            $header = $db->getHeader($data);
            array_push($header, 'link_pengumuman');
            $headerAdded = true;
            $csvWriter->insertOne($header);
        }
        $dataToInsert = array_values($db->makeItTable($data, $patternPengumuman));
        $csvWriter->insertAll($dataToInsert);
    } else {
        $keepLooping = false;
    }
    $currentPage++;
} while($keepLooping);
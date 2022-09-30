<?php
require 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Yfktn\Spsescraper2\SPSE4GetVendorNonTender;

$client = Client::createChromeClient();
$crawler = new SPSE4GetVendorNonTender($client);
$crawler->setUrlPattern("https://lpse.kalteng.go.id/eproc4/nontender/%d/peserta");

try {
    $crawler->run();
} catch(Exception $e) {
    echo $e;
}

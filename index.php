<?php
require 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Yfktn\Spsescraper2\SPSE4DataTableCrawler;

$url = 'https://lpse.kalteng.go.id/eproc4/lelang';

$client = Client::createChromeClient();
$crawler = $client->request('GET', $url);

try {
    (new SPSE4DataTableCrawler($client))->run();
} catch(Exception $e) {
    echo $e;
}
// $client->takeScreenshot('screen.png');

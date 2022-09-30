<?php
require 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Yfktn\Spsescraper2\SPSE4DataTableCrawler;
use Yfktn\Spsescraper2\SPSE4NonTenderDataTableCrawler;
use Yfktn\Spsescraper2\SPSE4NonTenderDataTableItemCrawler;

$url = 'https://lpse.kalteng.go.id/eproc4/nontender';

$client = Client::createChromeClient();
$crawler = $client->request('GET', $url);

try {
    (new SPSE4NonTenderDataTableCrawler($client))
        ->setDataTableItemCrawler(new SPSE4NonTenderDataTableItemCrawler())
        ->run(110);
} catch(Exception $e) {
    echo $e;
}
// $client->takeScreenshot('screen.png');

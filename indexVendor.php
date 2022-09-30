<?php
require 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Yfktn\Spsescraper2\SPSE4GetVendor;

$client = Client::createChromeClient();

try {
    (new SPSE4GetVendor($client))->run();
} catch(Exception $e) {
    echo $e;
}

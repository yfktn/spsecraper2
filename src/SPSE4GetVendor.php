<?php namespace Yfktn\Spsescraper2;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;
use Yfktn\Spsescraper2\DB;

class SPSE4GetVendor
{
    protected $client;

    protected $db;

    protected $urlPattern = "";

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->db = new DB();
        $this->urlPattern = "https://lpse.kalteng.go.id/eproc4/lelang/%d/peserta";
    }

    public function setUrlPattern(string $urlPattern)
    {
        $this->urlPattern = $urlPattern;
    }

    public function run($startFromThisPage = 1)
    {
        $currentPage = $startFromThisPage;
        $data = [];
        $keepLooping = true;
        echo "Halaman: ";
        do {
            echo " {$currentPage} ";
            $data = $this->getData($currentPage);
            if(count($data) > 0) {
                // ini ada datanya maka ... 
                $result = $this->scrapeVendor($data);
                $this->saveToDb($result);
            } else {
                $keepLooping = false;
            }
            $currentPage = $currentPage + 1;
        } while($keepLooping);
    }

    protected function scrapeVendor($dataToScrape)
    {
        $contentArray = [];
        foreach($dataToScrape as $data) {
            $theUrl = sprintf($this->urlPattern, $data['kode']);
            $this->client->request('GET', $theUrl);
            try {
                $crawler = $this->client->waitForVisibility("#main > div > table");
                $crawler->filter("#main > div > table > tbody > tr")->each(
                    function(Crawler $parent, $i) use(&$contentArray, $data) {
                        $values = [];
                        $values['kode'] = $data['kode'];
                        $columns = $parent->filter('td');
                        $values['nama_peserta'] = $columns->eq(1)->text("?");
                        $values['npwp'] = $columns->eq(2)->text("?");
                        $values['harga_penawaran'] = $columns->eq(3)->text("0");
                        $values['harga_terkoreksi'] = $columns->eq(4)->text("0");
                        array_push($contentArray, $values);
                    }
                );
            } catch(NoSuchElementException $nse) {
                echo $nse->getMessage(), "\n" , $nse->getTraceAsString();
            } catch(Exception $e) {
                echo $e->getMessage(), "\n" , $e->getTraceAsString();
            }
        }
        return $contentArray;
    }

    protected function saveToDb($result)
    {
        $this->db->updateOrInsertDataVendor($result);
    }

    protected function getData($currentPage)
    {
        return $this->db->getTender($currentPage);
    }
}
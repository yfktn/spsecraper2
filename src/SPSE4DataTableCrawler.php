<?php namespace Yfktn\Spsescraper2;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\Panther\Client;

class SPSE4DataTableCrawler
{
    protected $client;
    
    protected $maxPage = -1;

    protected $db;

    protected $itemDataTableCrawler;

    protected $timeoutInSecond = 25;

    public function __construct(Client $client, $maxPage = -1, $timeoutInSecond = 25)
    {
        $this->client = $client;
        $this->maxPage = $maxPage;
        $this->timeoutInSecond = $timeoutInSecond;
        $this->db = new DB();
    }

    public function setMaxPage($maxPage)
    {
        $this->maxPage = $maxPage;
    }

    public function setDataTableItemCrawler(DataTableItemCrawler $dataTableItemCrawler)
    {
        $this->itemDataTableCrawler = $dataTableItemCrawler;
        return $this;
    }

    public function getDataTableItemCrawler()
    {
        return $this->itemDataTableCrawler ?? new SPSE4DataTableItemCrawler();
    }

    protected function isNextExists(Crawler $crawler) {
        $classes = $crawler->filter("#tbllelang_next")->attr('class');
        return strpos($classes, 'disabled') === false;
    }

    public function run($currentPage = 1)
    {
        // bila nilai ini false, maka lakukan pembacaan terhadap nilai item
        // bila ini nilainya selain false, berarti lagi scrolling saja tanpa harus membaca 
        // nilai item yang muncul.
        $gotoPageMode = $currentPage != 1? $currentPage: false;
        $currentPage = 1;
        $keepLooping = true;
        $gotError = 0;
        $maxGotError = 3;
        echo "Halaman: "; 
        do {
            // //*[@id="tbllelang_paginate"]/ul/li[3]/a
            echo ($gotoPageMode?" > ":""), " {$currentPage} ";
            if($this->maxPage > 0 && $currentPage > $this->maxPage) {
                break;
            }
            if($gotError > $maxGotError) {
                break;
            }
            try {
                $crawler = $this->client->waitForElementToContain(
                    "#tbllelang_paginate > ul > li.paginate_button.page-item.active > a",
                    $currentPage);
                if($gotoPageMode === false) {
                    $rows = $crawler->filter("#tbllelang > tbody >tr");
                    $result = $this->getDataTableItemCrawler()->runScraper($rows);
                    $this->saveToDb($result);
                } else {
                    // mode gotoPageMode selesai jika nilainya sudah > currentPage!
                    $gotoPageMode = ($gotoPageMode - 1 > $currentPage ? $gotoPageMode: false);
                }
                // test bila masih ada tombol next!
                if($this->isNextExists($crawler)) {
                    $currentPage++;
                    // $nextPageLink = $crawler->filter('#tbllelang_next > a')->link();
                    // gunakan inject script ke browser supaya bisa di klik di sana!
                    // klik next sampai habis!
                    $crawler = $this->client->executeScript("$('li#tbllelang_next a[aria-controls=\"tbllelang\"][aria-label=\"Next\"]').click()");
                    // $this->run($currentPage);
                } else {
                    $keepLooping = false;
                }
                // reset $gotError to 0
                $gotError = 0;
            } catch(Exception $e) {
                echo $e->getMessage(), "\n" , $e->getTraceAsString();
                $gotError++;
            }
        } while($keepLooping);
    }

    protected function saveToDb(array $result)
    {
        $this->db->updateOrinsertDataTender($result);
    }
}
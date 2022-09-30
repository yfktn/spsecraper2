<?php namespace Yfktn\Spsescraper2;

use Symfony\Component\Panther\DomCrawler\Crawler;

abstract class DataTableItemCrawler
{
    protected $rows;

    /**
     * Override this function!
     * @return array 
     */
    public function runScraper(Crawler $rows)
    {
        $this->rows = $rows;
        return [];
    }

    protected function saveToDb(array $result)
    {
        print_r($result);
    }
}
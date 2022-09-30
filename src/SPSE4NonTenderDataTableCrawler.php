<?php namespace Yfktn\Spsescraper2;

/**
 * Lakukan proses ke data yang non tender
 * @package Yfktn\Spsescraper2
 */
class SPSE4NonTenderDataTableCrawler extends SPSE4DataTableCrawler
{
    protected function saveToDb($result)
    {
        $this->db->updateOrinsertDataNonTender($result);
    }
}
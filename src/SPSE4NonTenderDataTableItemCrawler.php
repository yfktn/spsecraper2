<?php namespace Yfktn\Spsescraper2;

use Symfony\Component\DomCrawler\Crawler;

class SPSE4NonTenderDataTableItemCrawler extends DataTableItemCrawler
{
    public function runScraper(Crawler $rows)
    {
        $this->rows = $rows;
        // //*[@id="tbllelang_paginate"]/ul/li[3]/a
        $contentArray = [];
        $this->rows->each(function(Crawler $parent, $i) use(&$contentArray) {
            $values = [];
            $columns = $parent->filter('td');
            // kode paket
            $values['kode'] = $columns->eq(0)->text("?");
            // nama paket
            $namaPaket = $columns->eq(1)->filter('p');
            // ambil nama paket dan versinya
            $namaPaketLink = $columns->eq(1)->filter("p > a");
            $values['nama_paket'] = $namaPaketLink->text();
            $values['nama_paket_tagging'] = "";
            $offsetPattern = "";
            $namaPaketLink->filter("span")->each(function(Crawler $parent, $i) use(&$values, &$offsetPattern) {
                if($i==0) {
                    $offsetPattern = trim($parent->text()); // ambil tagging pertama
                }
                $values['nama_paket_tagging'] .= "*" . trim($parent->text());
            });
            if(!empty($offsetPattern)) { // ada tagging maka hilangkan tagging dari penamaan paket!
                if(($pos = strrpos($values['nama_paket'], $offsetPattern)) !== false) {
                    $values['nama_paket'] = substr($values['nama_paket'], 0, $pos-1);
                }
            }
            $values['versi_spse'] = $columns->eq(1)->filter("p > span")->text();
            // dapatkan jenis dan metodenya serta tahun anggaran
            $jenisMetode = $namaPaket->eq(1)->text();
            if($jenisMetode !== null) {
                $jenisMetodeA = explode("-", $jenisMetode);
                $values['jenis'] = trim($jenisMetodeA[0]);
                $values['tahun_anggaran'] = trim($jenisMetodeA[1]);
                $values['metode'] = trim($jenisMetodeA[2]);
                // $values['cara'] = trim($jenisMetodeA[3]);
            } else {
                $values['jenis'] = "?";
                $values['tahun_anggaran'] = "?";
                $values['metode'] = "?";
                // $values['cara'] = "?";
            }
            if(($kontrak = $namaPaket->eq(2)->text()) !== null) {
                $ke = explode(":", $kontrak);
                $values['kontrak'] = trim($ke[1]);
            } else {
                $values['kontrak'] = "Belum dibuat";
            }
            array_push($contentArray, $values);
        });
        return $contentArray;
    }
}
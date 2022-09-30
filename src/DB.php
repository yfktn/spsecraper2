<?php namespace Yfktn\Spsescraper2;
use SleekDB\Store;
use SleekDB\Query;

class DB {
    protected $dbPath = "/db";
    protected $configuration = [];

    protected function getStore($storeName)
    {
        return new Store($storeName, $this->dbPath, $this->configuration);
    }

    public function __construct($dbPath = __DIR__ . "/db")
    {
        $this->dbPath = $dbPath;
        $this->configuration = [
            "auto_cache" => true,
            "cache_lifetime" => null,
            "timeout" => false, // deprecated! Set it to false!
            "primary_key" => "_id",
            "search" => [
              "min_length" => 2,
              "mode" => "or",
              "score_key" => "scoreKey",
              "algorithm" => 1 // Query::SEARCH_ALGORITHM["hits"]
            ]
        ];
    }

    public function updateOrinsertDataTender($dataTender)
    {
        $tenderStore = new Store("tender", $this->dbPath, $this->configuration);
        foreach($dataTender as $tender) {
            $d = $tenderStore->findOneBy([
                'kode', '=', $tender['kode']
            ]);
            if($d == null) {
                $d = [];
            } else {
                // update
                foreach($tender as $key => $value) {
                    $d[$key] = $value;
                }
            }
            $tenderStore->updateOrInsert($d);
        };
    }

    public function getTender($page, $limit = 25, $store="tender")
    {
        $skip = ($page-1) * $limit;
        $tenderStore = new Store($store, $this->dbPath, $this->configuration);
        return $tenderStore->findAll(null, $limit, $skip );
    }

    public function updateOrInsertDataVendor($dataVendor, $store = "vendor") 
    {
        $vendorStore = new Store($store, $this->dbPath, $this->configuration);
        foreach($dataVendor as $vendor) {
            $d = $vendorStore->findOneBy([
                ['kode', '=', $vendor['kode']],
                ['npwp', '=', $vendor['npwp']]
            ]);
            if($d == null) {
                $d = $vendor;
            } else {
                // update
                foreach($vendor as $key => $value) {
                    $d[$key] = $value;
                }
            }
            $vendorStore->updateOrInsert($d);
        };
    }

    public function updateOrinsertDataNonTender($dataNonTender)
    {
        $nonTenderStore = new Store("nontender", $this->dbPath, $this->configuration);
        foreach($dataNonTender as $tender) {
            $d = $nonTenderStore->findOneBy([
                'kode', '=', $tender['kode']
            ]);
            if($d == null) { // insert new!
                $d = $tender;
            } else {
                // update
                foreach($tender as $key => $value) {
                    $d[$key] = $value;
                }
            }
            $nonTenderStore->updateOrInsert($d);
        };
    }
}
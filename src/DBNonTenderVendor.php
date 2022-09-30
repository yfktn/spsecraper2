<?php namespace Yfktn\Spsescraper2;

class DBNonTenderVendor extends DBTenderVendor
{

    public function __construct()
    {
        parent::__construct();
        $this->tenderStore = $this->getStore("nontender");
        $this->vendorStore = $this->getStore("vendor_non_tender");
    }
}
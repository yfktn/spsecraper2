<?php namespace Yfktn\Spsescraper2;

class DBTenderVendor extends DB
{
    protected $tenderStore;
    protected $vendorStore;

    public function __construct()
    {
        parent::__construct();
        $this->tenderStore = $this->getStore("tender");
        $this->vendorStore = $this->getStore("vendor");
    }

    public function getTenderWithVendor($page = 1, $limit = 25)
    {
        $skip = ($page-1) * $limit;
        $tenderData = $this->tenderStore->findAll(null, $limit, $skip );
        $tenderDataCount = count($tenderData);
        for($i=0;$i<$tenderDataCount;$i++) {
            $tenderData[$i]['vendor'] = $this->vendorStore->findBy(['kode', '=', $tenderData[$i]['kode']]);
        }
        return $tenderData;
    }

    public function getHeader($dataSample)
    {
        $header = [];
        foreach($dataSample as $value) {
            // cari data yang ada vendor nya supaya sekalian diambilkan
            if(isset($value['vendor']) && count($value['vendor'])>0) {
                // sekarang ini ada vendor nya maka ... 
                foreach($value as $kk => $kv) {
                    if($kk == 'vendor') { // ambil header punya vendor yg pertama saja
                        foreach($kv[0] as $kkk => $kkv) {
                            if($kkk == 'kode') {
                                continue; // tidak usah double
                            }
                            $keynya = $kkk == '_id'? '_idvendor': $kkk;
                            $header[] = $keynya;
                        }
                    } else {
                        $header[] = $kk;
                    }
                }
                break; // dapat sample,keluar kita
            } else {
                continue; // cari sampai dapat
            }
        }
        return $header;
    }

    /**
     * Previously as the result of join we got data like this:
     * $data = [
     *  'a' => 'A',
     *  'vendor' => [
     *      [ k => a, l = > b, m => c],
     *      [ k => c, l = > d, m => e],
     *   ],
     * ];
     * then we need to make it as table become like this
     * $data = [
     *  'a' => 'A', k => a, l = > b, m => c,
     *  'a' => 'A', k => c, l = > d, m => e,
     * ]
     * @param array $data 
     * @return array
     */
    public function makeItTable($dataNya, $linkPengumuman)
    {
        $table = [];
        foreach($dataNya as $data) {
            $tableRow = [];
            foreach($data as $k => $d) {
                if($k == 'vendor') {
                    continue;
                } else {
                    $tableRow[$k] = $d;
                }
            }
            if(isset($data['vendor'])) {
                foreach($data['vendor'] as $dataVendor) {
                    $tbrow = $tableRow;
                    foreach($dataVendor as $kv => $kd) {
                        if($kv == 'kode') {
                            continue; // tidak usah double
                        }
                        $kv = $kv == "_id"? "_idvendor": $kv;
                        $tbrow[$kv] = $kd;
                    }
                    $tbrow['link_pengumuman'] = sprintf($linkPengumuman, $tbrow['kode']);
                    array_push($table, $tbrow);
                }
            } else {
                $tbrow['link_pengumuman'] = sprintf($linkPengumuman, $tableRow['kode']);
                array_push($table, $tableRow);
            }
        }
        return $table;
    }
}
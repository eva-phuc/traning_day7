<?php

namespace Fuel\Tasks;

class Eva_tri {

    public static function run($file = null) {

        \Package::load('iteminfo');
        \Package::load('batch');
        \Config::load('sites', 's');
        $sites = \Config::get('s.site');
        \Config::load('batch_option', 'o');
        $logSet = \Config::get('o.log_set.batch_execute');        

        if ($file === "" || !is_file($file)) {
            echo "Please include exact the filename.";
            return false;
        }

        // ログ
        if ($logSet == 'on') {
            \Batch\Batchlog::write('ITEM UPDATE BATCH [Eva-Tri] START');
        }

        $handle = fopen($file, "r") or die("Unable to open file!");
        
        $result = "";
        while (!feof($handle)) {
            //get url from file content
            $url = fgets($handle);

            $iteminfo = new \Siteparse();
            $is_site = $iteminfo->setUrl(trim($url));            
            if($is_site === false){
                 $result .=  "対応していないサイトです。\n";
                continue;
            }
            
            $itemData = $iteminfo->getData();
            if ($is_site === false || empty($itemData)) {
                echo "クリップに対応していないページです。\n";
                continue;
            }                        
            
            $result .= $itemData['site'] . "\t";
            $result .= $itemData['url'] . "\t";
            $result .= (isset($itemData['item_code']) ? $itemData['item_code'] : "") . "\t";
            $result .= (isset($itemData['shop_code']) ? $itemData['shop_code'] : "") . "\t";
            $result .= (isset($itemData['item']['item_name']) ? $itemData['item']['item_name'] : "") . "\t";
            $result .= (isset($itemData['item']['default_price']) ? $itemData['item']['default_price'] : "") . "\t";
            $result .= (isset($itemData['item']['price']) ? $itemData['item']['price'] : "") . "\t";
            $result .= (isset($itemData['item']['sale_price']) ? $itemData['item']['sale_price'] : "") . "\t";
            $result .= (isset($itemData['item']['sale']) ? $itemData['item']['sale'] : "") . "\t";
            $result .= date('Y-m-d H:i:s') . "\n";
            sleep(1);
        }
        fclose($handle);
        
        echo $result;
    }

}

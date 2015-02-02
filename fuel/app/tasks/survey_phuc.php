<?php
namespace Fuel\Tasks;

class Survey_Phuc
{
    const RETRY_COUNT = 3;

    public static function run($file = null)
    {
        \Package::load('iteminfo');
        $logSet = \Config::get('o.log_set.batch_execute');

        if (empty($file) || !is_file($file)) {
            echo "FILE ERROR \n";

            return;
        }

        if ($logSet == 'on') {
            \Batch\Batchlog::write('ITEM UPDATE BATCH [servey-phuc] START');
        }

        $handle = @fopen($file, 'r');
        while (!feof($handle)) {
            $data[] = fgets($handle);
        }
        fclose($handle);
        foreach ($data as $item) {
            $success = false;
            $item = trim($item);
            if (empty($item)) continue;
            $itemData = array();
            for ($try = 0; $try < self::RETRY_COUNT; $try++) {
                try {
                    $itemInfo = new \Siteparse();
                    $is_site = $itemInfo->setUrl($item);
                    if ($is_site)
                        $itemData = $itemInfo->getData();

                } catch (\Exception $e) {
                    usleep(500000);
                    continue;
                }
                if ($itemData !== false && !empty($itemData)) {
                    $success = true;
                    break;
                }
                sleep(1);
            }
            if (!$success) {
                echo $item . "\t" . "クリップに対応していないページです。";
                continue;
            }

            $result = array(
                'site'               => $itemData['site'],
                'url'                => $itemData['url'],
                'item_code'          => $itemData['item_code'],
                'shop_code'          => @$itemData['shop_code'],
                'item.default_price' => $itemData['item']['default_price'],
                'item.price'         => $itemData['item']['price'],
                'item.sale_price'    => @$itemData['item']['sale_price'],
                'item.sale'          => $itemData['item']['sale'],
                'datetime'           => date('Y-m-d H:i:s'),
            );

            echo implode("\t", $result) . "\n";
            usleep(500000);
        }

        if ($logSet == 'on') {
            \Batch\Batchlog::write('ITEM UPDATE BATCH [servey-phuc] END');
        }
    }

}
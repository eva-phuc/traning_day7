<?php
namespace Fuel\Tasks;

/**
 * 各サイトの商品情報の更新
 * 
 * @author higuchi
 */
class Item_update {
    
    const RETRY_COUNT = 3; // 商品情報取得失敗時のリトライ回数
    const MISS_KEEP_COUNT = 50; // 商品取得連続失敗時のアラートまでの回数
    
    /**
     * 更新実行
     * 
     * @access public
     * @return array
     * @author higuchi
     */
    public static function run($site = null) {
    
        \Package::load('iteminfo');
        \Package::load('batch');
        \Config::load('sites', 's');
        $sites = \Config::get('s.site');
        \Config::load('batch_option', 'o');
        $logSet = \Config::get('o.log_set.batch_execute');
        
        // 実行コマンドエラー
        if (! in_array($site, $sites)) {
            echo "shop code ERROR \n";
            return;
        }
        
        // ログ
        if ($logSet == 'on') {
            \Batch\Batchlog::write('ITEM UPDATE BATCH ['.$site.'] START');
        }        
        
        // ショップの登録商品データを取得
        $items = \Batch\Model_Items::getData(\Config::get('s.shop_id.'.$site));
        
        // 商品データを順にアップデート
        $missCount = 0;
        foreach ($items as $value) {
            
            // 最新商品データ取得
            $success = false;
            for ($try = 0; $try < self::RETRY_COUNT; $try ++) {
                try{
                    switch ($site) {
                        case'yshop':
                            $itemCode = $value['shop_code'].'_'.$value['item_code'];
                            $newData = \Iteminfo\Model_Site_Yshop::getData($itemCode);
                            break;
                        
                        case 'zozo':
                            $newData = \Iteminfo\Model_Site_Zozo::getData($value['url']);
                            break;

                        case 'felissimo':
                            $newData = \Iteminfo\Model_Site_Felissimo::getData($value['url']);
                            break;

                        case 'locondo':
                            $newData = \Iteminfo\Model_Site_Locondo::getData($value['url']);
                            break;

                        case 'fashionwalker':
                            $newData = \Iteminfo\Model_Site_Fashionwalker::getData($value['url']);
                            break;

                        case 'dinos':
                            $newData = \Iteminfo\Model_Site_Dinos::getData($value['url']);
                            break;
                        
                        case 'nissen':
                            $newData = \Iteminfo\Model_Site_Nissen::getData($value['url']);
                            break;
                        
                        case 'bellemaison':
                            $newData = \Iteminfo\Model_Site_Bellemaison::getData($value['url']);
                            break;
                        
                        case 'magaseek':
                            $newData = \Iteminfo\Model_Site_Magaseek::getData($value['url']);
                            break;
                        
                        case 'stylife':
                            $newData = \Iteminfo\Model_Site_Stylife::getData($value['url']);
                            break;
                    }
                
                } catch (\Exception $e) {
                     usleep(500000);
                     continue;
                }
                
                if($newData !== false && ! empty($newData)) {
                    $success = true;
                    break;
                }
                sleep(1);
            }
            
            // 商品情報アップデート
            if ($success == true) {
                // 成功時
                \Batch\Model_Items::upData($newData, $value['item_id']);
                $missCount = 0;
                
            } else {
                // 失敗時
                \Batch\Model_Items::upFail($value['item_id']);
                $missCount++;
                // 連続で一定回数以上エラーがあった場合エラーアラート
                if ($missCount >= self::MISS_KEEP_COUNT) {
                    \Common\Error::instance()
                        ->set_log("Item UpDate Batch Error \nHTML Parse or API Error \nShop Code : ".$site."\nSite URL : ".$value['url'])
                        ->set_email("Item UpDate Batch Error \nHTML Parse or API Error \nShop Code : ".$site."\nSite URL : ".$value['url'],'[ERROR] API ')
                        ->logging();
                    $missCount = 0;
                }
            }

            
            usleep(\Config::get('s.exe_wait_time.'.$site));
        }
        
        // ログ
        if ($logSet == 'on') {
            \Batch\Batchlog::write('ITEM UPDATE BATCH ['.$site.'] END');
        }        
    }


}
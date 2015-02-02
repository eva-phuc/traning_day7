<?php
namespace Iteminfo;

/**
 * Yahoo!ショッピング用 商品データ解析
 * 
 * @author higuchi
 */
class Model_Site_Yshop extends \Model {

    /**
     * 商品データ取得・解析
     * 
     * @access public
     * @param string $itemCode
     * @return mixed
     * @author higuchi
     */
	public static function getData($itemCode) {
        
        \Config::load('apiconf', 'a');
        $confParams = \Config::get('a.yshop');
        
        // リクエストデータ作成
        $resParams['appid'] = $confParams['appid'];
        $resParams['responsegroup']  = 'large';
        $resParams['itemcode']  = $itemCode;
        $resParams['image_size']  = 600;
        
        // 商品データ取得・整形
        try{
            // 商品データ取得
            $apiData= Model_Getapi::getData($confParams['api_url'], $resParams, 'serialize');
            if($apiData === false) return false;
            if(isset($apiData['Error'])) return false;
            if($apiData['ResultSet']['firstResultPosition'] == 0) return false;
            
            // 必要なデータを選別
            if(! empty($apiData['ResultSet'][0]['Result'][0]['Name'])){
                $itemParams['item_name'] = $apiData['ResultSet'][0]['Result'][0]['Name'];
            }
            if(! empty($apiData['ResultSet'][0]['Result'][0]['ExImage']['Url'])){
                $itemParams['img_url'] = $apiData['ResultSet'][0]['Result'][0]['ExImage']['Url'];
            }
            
            if(! empty($apiData['ResultSet'][0]['Result'][0]['Price']['_value'])){
                $itemParams['price'] = $apiData['ResultSet'][0]['Result'][0]['Price']['_value'];
            }
            
            if(! empty($apiData['ResultSet'][0]['Result'][0]['PriceLabel']['DefaultPrice'])){
                $itemParams['default_price'] = $apiData['ResultSet'][0]['Result'][0]['PriceLabel']['DefaultPrice'];
            }
            
            if(! empty($apiData['ResultSet'][0]['Result'][0]['PriceLabel']['SalePrice'])){
                $itemParams['sale'] = 'yes';
                $itemParams['sale_price'] = $apiData['ResultSet'][0]['Result'][0]['PriceLabel']['SalePrice'];
            } else {
                $itemParams['sale'] = 'no';
            }
            if(! empty($apiData['ResultSet'][0]['Result'][0]['Availability'])
                && $apiData['ResultSet'][0]['Result'][0]['Availability'] == 'instock'){
                $itemParams['stock'] = 99;
            } else {
                $itemParams['stock'] = 0;
            }
            
            // 必須選択オプションがある場合
            if(! empty($apiData['ResultSet'][0]['Result'][0]['Inventories'])){
                
                
                $idOption = $apiData['ResultSet'][0]['Result'][0]['Inventories'][0]['Order']; // 必須オプション
                $ollOrder = $apiData['ResultSet'][0]['Result'][0]['Order']; // 全オプション
                unset($idOption['_container']);
                unset($ollOrder['_container']);
                $optionCnt = 0;
                // オプション選択用の配列を整形
                foreach ($idOption as $idVal) {
                    foreach ($ollOrder as $ollVal) {
                        if ($idVal['Name'] == $ollVal['Name']) {
                            $subOption[$optionCnt]['name'] = $ollVal['Name'];
                            unset($ollVal['Values']['_container']);
                            foreach ($ollVal['Values'] as $key => $val) {
                                $subOption[$optionCnt]['value'][$key+1] = $val['Value'];
                            }
                            $optionCnt++;
                        }
                    }
                }
                
                // オプションデータの整形
                $cnt = 0;
                foreach ($apiData['ResultSet'][0]['Result'][0]['Inventories'] as $value) {
                    if (is_array($value['Order'])){
                        $itemParams['options'][$cnt]['option_id'] = $value['SubCode'];
                        if ($value['Availability'] == 'instock') {
                            $itemParams['options'][$cnt]['stock'] = 99;
                        } else {
                            $itemParams['options'][$cnt]['stock'] = 0;
                        }
                        // カラー・サイズ等の詳細情報の整形
                        unset($value['Order']['_container']);
                        $subCount = 0;
                        $subOptionKey = array();
                        foreach($value['Order'] as $optionData) {
                            $itemParams['options'][$cnt]['option_value'][$subCount]['name'] = $optionData['Name'];
                            $itemParams['options'][$cnt]['option_value'][$subCount]['value'] = $optionData['Value'];
                            
                            // sub_option_id作成用
                            foreach ($subOption as $cateValue) {
                                if ($optionData['Name'] == $cateValue['name']) {
                                    foreach ($cateValue['value'] as $vKey => $vValue) {
                                        if ($optionData['Value'] == $vValue)  {
                                            $subOptionKey[] = $vKey;
                                            break;
                                        }
                                    }
                                }
                            }
                            $subCount++;
                        }
                        $itemParams['options'][$cnt]['sub_option_id'] = implode(",", $subOptionKey);
                        $cnt++;
                    }
                }
                $itemParams['sub_options'] = $subOption;
            }
            
            // 商品名、価格いずれか取れなければエラー
            if(empty($itemParams['item_name']) || empty($itemParams['price'])) {
                return false;
            }
        
        } catch (\Exception $e) {
            return false;
        }
        
        return $itemParams;
    
    }
}
    
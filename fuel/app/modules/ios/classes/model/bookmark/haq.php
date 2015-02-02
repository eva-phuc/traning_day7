<?php
namespace Ios;

/**
 * ブックマーク登録
 * 
 * @author higuchi
 */
class Model_Bookmark_Haq extends \Model {
    
    public function __construct(){
        \Package::load('iteminfo');
        \Config::load('sites', 's');
    }
    
    /**
    * 商品及びブックマークへのデータ登録
    * 
    * @access public
    * @param array $inputParams
    * @param array $itemData
    * @param string $user
    * @return bool
    * @author higuchi
    */
    public function setData($inputParams, $itemData, $user) {
        
        // 既存ブックマークデータの確認
        $regitInfo = $this->checkBookmark($inputParams, $itemData, $user);
        if($regitInfo === false) return false;
        
        // 商品データ新規登録
        if ($regitInfo['status'] == 'new') {
            $addResult = $this->addItem($inputParams, $itemData);
            if ($addResult === false) return false;
            $item_id = $addResult;
        }
        
        // bookmerk登録        
        if ($regitInfo['status'] == 'new' || $regitInfo['status'] == 'add') {
            $existingItemData = null;
            if ($regitInfo['status'] == 'add') {
                $item_id = $regitInfo['existing_item_data']['item_id'];
                $existingItemData = $regitInfo['existing_item_data'];
            }
            $result = $this->addBookmerks($inputParams, $itemData, $user, $item_id, $existingItemData);
            if ($result === false) return false;
            return 'success';
        }
        return  'exist';
    }
    
    /**
    * ブックマークデータの確認
    * 
    * @access public
    * @param array $inputParams
    * @param array $itemData
    * @param string $user
    * @return mixed
    * @author higuchi
    */
    private function checkBookmark($inputParams, $itemData, $user) {
        
        // Itemのマスタ商品の有無
        $getItemParams = $this->getItemData($itemData);
        if($getItemParams === false) return false;
        if (empty($getItemParams)) {
            // 既存商品データなし。新規登録
            return array('status' => 'new'); 
        }
        // 商品の選択オプション情報取得
        $optionData = $this->getOption($inputParams, $itemData);
        
        // ブックマークデータ取得
        $bookmarkData = $this->getBookmarkId($user, $getItemParams['item_id'], $optionData);
        if($bookmarkData === false) return false;
        if (empty($bookmarkData)) {
             // 既存商品データ有り。ブックマーク情報のみ追加
            return array('status' => 'add', 'existing_item_data' => $getItemParams);
        }
        
        // ブックマーク登録済
        return array('status' => 'exist');
    
    }
    
    /**
    * 商品データの取得
    * 
    * @access public
    * @param arry $itemDatal
    * @return mixed
    * @author higuchi
    */
    private function getItemData($itemData) {
        try{
            $arry_shop_id = \Config::get('s.shop_id');
            $shop_id = $arry_shop_id[$itemData['site']];
            
            $shopCode = '';
            if (! empty($itemData['shop_code'])) {
                $shopCode = ' AND shop_code = :shop_code';
            }
            
            $query = \DB::query('SELECT * FROM items WHERE shop_id = '.$shop_id.' AND item_code = :item_code'.$shopCode);
            $query->bind('item_code', $itemData['item_code']);
            
            if (! empty($itemData['shop_code'])) {
                $query->bind('shop_code', $itemData['shop_code']);
            }
            
            $results = $query->execute();
            return $results->current();
            
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
    
    
    /**
    * ブックマークIDの確認
    * 
    * @access public
    * @param int $user
    * @param int $itemId
    * @param mixed $option
    * @return mixed
    * @author higuchi
    */
    private function getBookmarkId($user, $itemId, $option = false) {
        try{
            $optionQuery = '';
            if($option !== false) {
                $optionQuery = ' and option_id = :option_id';
            }
            $query = \DB::query('SELECT user_id FROM bookmarks WHERE deleted_at IS NULL AND user_id = :user_id AND item_id = :item_id' . $optionQuery);
            $query->bind('user_id', $user)->bind('item_id', $itemId);
            if($option !== false) {
                 $query->bind('option_id', $option['option_id']);
            }
            $results = $query->execute();
            return $results->current();
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
    
    /**
    * 商品オプションID確認
    * 
    * @access public
    * @param int $itemId
    * @param string $option_id
    * @return bool
    * @author higuchi
    */
    private function checkOptionItem($item_id, $option_id) {
        try{
            $query = \DB::query('SELECT option_id FROM item_options WHERE item_id = :item_id and option_id = :option_id');
            $query->bind('item_id', $item_id)->bind('option_id', $option_id);
            $results = $query->execute();
            return $results->current();
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
    
    
    
    /**
    * 商品情報の登録
    * 
    * @access public
    * @param array $inputParams
    * @param array $itemData
    * @return mixed
    * @author higuchi
    */
    private function addItem($inputParams, $itemData) {
        
        $arry_shop_id = \Config::get('s.shop_id');
        $shop_id = $arry_shop_id[$itemData['site']];
        $items = $itemData['item'];
        
        // 登録項目の生成
        $queryStr = 'shop_id, url, item_name, price, bookmark_high_price, bookmark_count, item_code, updated_at';
        $queryVal = ':shop_id, :url, :item_name, :price, :price, 0, :item_code, NOW()';
        $bind = array('shop_id' => $shop_id, 'url' => $itemData['url'], 
            'item_name' => $items['item_name'], 'price' => $items['price'], 'item_code' => $itemData['item_code']);
        if(! empty($items['default_price'])) {
            $queryStr .= ', default_price';
            $queryVal .= ', :default_price';
            $bind['default_price'] = $items['default_price'];
        }
        if(! empty($items['sale_price'])) {
            $queryStr .= ', sale_price';
            $queryVal .= ', :sale_price';
            $bind['sale_price'] = $items['sale_price'];
        }
        if(! empty($items['sale'])) {
            $queryStr .= ', sale';
            $queryVal .= ', :sale';
            $bind['sale'] = $items['sale'];
        }
        if(! empty($items['stock']) || $items['stock'] === 0) {
            $queryStr .= ', stock';
            $queryVal .= ', :stock';
            $bind['stock'] = $items['stock'];
        }
        if(! empty($items['img_url'])) {
            $queryStr .= ', img_url';
            $queryVal .= ', :img_url';
            $bind['img_url'] = $items['img_url'];
        }
        if(! empty($items['brand'])) {
            $queryStr .= ', brand';
            $queryVal .= ', :brand';
            $bind['brand'] = $items['brand'];
        }
        if(! empty($items['category'])) {
            $queryStr .= ', category';
            $queryVal .= ', :category';
            $bind['category'] = $items['category'];
        }
        if(! empty($itemData['shop_code'])) {
            $queryStr .= ', shop_code';
            $queryVal .= ', :shop_code';
            $bind['shop_code'] = $itemData['shop_code'];
        }
        
        try{
            // 商品メインテーブルへ情報追加
            $query = \DB::query('INSERT INTO items ('.$queryStr.') VALUES('.$queryVal.')');
            $query->parameters($bind);
            $results = $query->execute();
            
            return $results[0];
            
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
    
    
    /**
    * ブックマーク、オプションデータの登録
    * 
    * @access public
    * @param array $inputParams
    * @param array $itemData
    * @param int $user_id
    * @param int $item_id
    * @param mixed $existingItemData
    * @return mixed
    * @author higuchi
    */
    private function addBookmerks($inputParams, $itemData, $user_id, $item_id, $existingItemData = null) {
        
        $arry_shop_id = \Config::get('s.shop_id');
        $shop_id = $arry_shop_id[$itemData['site']];
        $item = $itemData['item'];
        
        // 商品の選択オプション情報取得
        $optionData = $this->getOption($inputParams, $itemData);
        
        // 商品オプションがある場合
        if ($optionData !== false) {
            $is_option = $this->checkOptionItem($item_id, $optionData['option_id']);
            if($is_option === false) return false;
            // DB未登録のオプション場合、商品オプション登録項目の生成
            if (empty($is_option)) {
                $values = serialize($optionData['option_value']);
                $values = base64_encode($values);
                $optionQueryStr = 'item_id, option_id, option_values, updated_at';
                $optionQueryVal = ':item_id, :option_id, :option_values, NOW()';
                $optionBind = array('item_id' => $item_id, 
                    'option_id' => $optionData['option_id'], 'option_values' => $values);
                if(! empty($optionData['stock']) || $optionData['stock'] === 0) {
                    $optionQueryStr .= ', stock';
                    $optionQueryVal .= ', :stock';
                    $optionBind['stock'] = $optionData['stock'];
                }
                if(! empty($optionData['option_img'])) {
                    $optionQueryStr .= ', img_url';
                    $optionQueryVal .= ', :img_url';
                    $optionBind['img_url'] = $optionData['option_img'];
                }
            }
        }
        
        // ブックマーク登録項目の生成
        $alertSetPrice = 1; // --アラート内容のセット-現在は1円でも安くなった場合のみ--
        $bookmerkQueryStr = 'user_id, item_id, alert, price, updated_at, alert_set_price';
        $bookmerkQueryVal = ':user_id, :item_id, :alert, :price,  NOW(), '.$alertSetPrice;
        $bookmerkBind = array('user_id' => $user_id, 'item_id' => $item_id, 'alert' => $inputParams['alert'], 'price' => $item['price']);
        if(! empty($optionData['stock']) || $optionData['stock'] === 0) {
            $bookmerkQueryStr .= ', stock';
            $bookmerkQueryVal .= ', :stock';
            $bookmerkBind['stock'] = $optionData['stock'];
        } elseif (! empty($item['stock']) || $item['stock'] === 0) {
            $bookmerkQueryStr .= ', stock';
            $bookmerkQueryVal .= ', :stock';
            $bookmerkBind['stock'] = $item['stock'];
        }
        if ($optionData !== false) {
            $bookmerkQueryStr .= ', option_id';
            $bookmerkQueryVal .= ', :option_id';
            $bookmerkBind['option_id'] = $optionData['option_id'];
        }
        
        try{
            \DB::start_transaction();
            
            // オプション登録
            if ($optionData !== false && empty($is_option)) {
                $optQuery = \DB::query('INSERT INTO item_options ('.$optionQueryStr.') VALUES('.$optionQueryVal.')');
                $optQuery->parameters($optionBind);
                $optQuery->execute();
            }
            
            // ブックマーク登録
            $bookQuery = \DB::query('INSERT INTO bookmarks ('.$bookmerkQueryStr.') VALUES('.$bookmerkQueryVal.')');
            $bookQuery->parameters($bookmerkBind);
            $bookQuery->execute();
            
            // 商品情報の更新、ブックマーク数インクリメント
            $updateQuerySet = 'bookmark_count = bookmark_count+1, updated_at = NOW()';
            $updateBind = array('item_id' => $item_id);
            if(! empty($existingItemData) && 
                $existingItemData['bookmark_high_price'] < $item['price']) {
                // 登録最高価格の更新
                $updateQuerySet .= ', bookmark_high_price = :price';
                $updateBind['price'] = $item['price'];
            }
            $updateQuery = \DB::query('UPDATE items SET '.$updateQuerySet.' WHERE item_id = :item_id');
            $updateQuery->parameters($updateBind);
            $updateQuery->execute();
            
            \DB::commit_transaction();
            return true;
            
        } catch (\Exception $e) {
            \DB::rollback_transaction();
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
    
    
    /**
    * 商品のオプション情報の取得
    * 
    * @access private
    * @param array $inputParams
    * @param array $itemData
    * @return mixed
    * @author higuchi
    */
    private function getOption($inputParams, $itemData) {
        if (empty($inputParams['option']) || empty($itemData['item']['sub_options'])) {
            return false;
        }
        
        // オプション情報取得
        $subOptionId = implode(",", $inputParams['option']);
        foreach ($itemData['item']['options'] as $Key => $data) {
            if ($data['sub_option_id'] == $subOptionId) {
                $option = $data;
                break;
            } 
        }
        if(! isset($option)) return false;
        
        return $option;
    }
}
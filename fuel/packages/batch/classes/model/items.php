<?php
namespace Batch;

/**
 * 登録商品情報取得
 * 
 * @author higuchi
 */
class Model_Items extends \Model {


    /**
    * 情報取得
    * 
    * @access public
    * @return mixed
    * @author higuchi
    */
    public static function getData($id) {
        
        try{
            $query = \DB::query(
                'SELECT * 
                FROM items 
                WHERE shop_id = '.$id.' AND deleted_at IS NULL 
                ORDER BY item_id'
            );
            $results = $query->execute();
            return $results->as_array();
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
    
    
    /**
    * 情報更新
    * 
    * @access public
    * @param array $itemData
    * @param integer $item_id
    * @return mixed
    * @author higuchi
    */
    public static function upData($itemData, $itemId) {
        
        $success = true;
        
        // 商品のメイン情報更新
        $is_main = self::itemUpDate($itemData, $itemId);
        if ($is_main === false) return false;
        
        // オプション更新
        if (! empty($itemData['options'])) {
            foreach ($itemData['options'] as $value) {
                $is_option = self::optionUpDate($value, $itemId);
                if ($is_option === false) {
                    $success = false;
                }
            }
        }
        
        return $success;
    }
    
    
    /**
    * 商品メイン情報の更新
    * 
    * @access private
    * @param array $itemData
    * @param integer $item_id
    * @return mixed
    * @author higuchi
    */
    private static function itemUpDate($itemData, $itemId) {
    
        // 更新項目の生成
        $queryStr = 'old_price = price, updated_at = NOW(), update_fail_count = 0, price = :price';
        $bind = array('price' => $itemData['price']);
        if(! empty($itemData['default_price'])) {
            $queryStr .= ', default_price = :default_price';
            $bind['default_price'] = $itemData['default_price'];
        }
        if(! empty($itemData['sale_price'])) {
            $queryStr .= ', sale_price = :sale_price';
            $bind['sale_price'] = $itemData['sale_price'];
        }
        if(! empty($itemData['sale'])) {
            $queryStr .= ', sale = :sale';
            $bind['sale'] = $itemData['sale'];
        }
        if(! empty($itemData['stock']) || $itemData['stock'] === 0) {
            $queryStr .= ', stock = :stock';
            $bind['stock'] = $itemData['stock'];
        }
        
        try{
            // 商品メイン情報更新
            $query = \DB::query('UPDATE items SET '.$queryStr.' WHERE item_id = '.$itemId);
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
    * オプション情報の更新
    * 
    * @access private
    * @param array $optionData
    * @param integer $item_id
    * @return mixed
    * @author higuchi
    */
    private static function optionUpDate($optionData, $itemId) {
        // 更新項目の生成
        $queryStr = 'updated_at = NOW()';
        $bind = array('option_id' => $optionData['option_id']);
        if(! empty($optionData['stock']) || $optionData['stock'] === 0) {
            $queryStr .= ', stock = :stock';
            $bind['stock'] = $optionData['stock'];
        }
        
        try{
            // 商品オプション情報更新
            $query = \DB::query('UPDATE item_options 
                SET '.$queryStr.' 
                WHERE item_id = '.$itemId.' AND option_id = :option_id'
            );
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
    * 更新失敗数のインクリメント
    * 
    * @access public
    * @param integer $item_id
    * @return mixed
    * @author higuchi
    */
    public static function upFail($itemId) {
        
        try{
            // 商品メイン情報更新
            $query = \DB::query('UPDATE items 
                SET updated_at = NOW(), update_fail_count = (update_fail_count+1) 
                WHERE item_id = '.$itemId);
            $results = $query->execute();
            return $results[0];
            
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
        
        return $success;
    }
}
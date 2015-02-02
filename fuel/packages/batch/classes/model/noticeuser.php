<?php
namespace Batch;

/**
 * 通知関連
 * 
 * @author higuchi
 */
class Model_Noticeuser extends \Model {


    /**
    * 通知対象ユーザーの取得
    * 
    * @access public
    * @return mixed
    * @author higuchi
    */
    public static function getData() {
        
        try{
            $query = \DB::query(
                "SELECT DISTINCT b.user_id,n.is_mail_alert_deny, n.os_type, n.device_token 
                 FROM bookmarks AS b 
                 INNER JOIN items AS i ON b.item_id = i.item_id 
                 INNER JOIN notification_info AS n ON n.user_id = b.user_id 
                 WHERE b.deleted_at IS NULL AND b.alert = 'on' AND n.deleted_at IS NULL 
                  AND (b.alert_done_price IS NULL AND (b.price - b.alert_set_price) >= i.price 
                  OR b.alert_done_price IS NOT NULL AND b.alert_done_price > i.price) 
                  AND (n.is_mail_alert_deny != 1 OR n.device_token IS NOT NULL)"
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
    * ユーザーのブックマークデータへの通知済情報の更新
    * 
    * @access public
    * @return mixed
    * @author higuchi
    */
    public static function updateNotice() {
        
        try{
            $query = \DB::query(
                "UPDATE bookmarks AS b 
                 INNER JOIN items AS i ON b.item_id = i.item_id 
                 INNER JOIN notification_info AS n ON n.user_id = b.user_id 
                 SET b.alert_done_price = i.price 
                 WHERE b.deleted_at IS NULL AND b.alert = 'on' AND n.deleted_at IS NULL 
                  AND (b.alert_done_price IS NULL AND (b.price - b.alert_set_price) >= i.price 
                  OR b.alert_done_price IS NOT NULL AND b.alert_done_price > i.price) 
                  AND (n.is_mail_alert_deny != 1 OR n.device_token IS NOT NULL)"
            );
            $query->execute();
            return true;
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
    
    
    
    /**
    * 通知対象のブックマーク商品情報の取得
    * 
    * @access public
    * @param integer $userId
    * @return mixed
    * @author higuchi
    */
    public static function getBookmarkItem($userId) {
        
        try{
            $query = \DB::query(
                "SELECT i.*, S.name, S.code, b.bookmark_id, b.price as bookmark_price
                 FROM bookmarks AS b 
                 INNER JOIN items AS i ON b.item_id = i.item_id 
                 INNER JOIN shops AS S ON S.shop_id = i.shop_id 
                 WHERE b.user_id = ".$userId." AND b.deleted_at IS NULL AND alert = 'on' 
                  AND (b.alert_done_price IS NULL AND (b.price - b.alert_set_price) >= i.price 
                  OR b.alert_done_price IS NOT NULL AND b.alert_done_price > i.price) 
                  ORDER BY created_at DESC, item_id DESC"
            );
            $results = $query->execute();
            $itemData = $results->as_array();
            // アフィリタグの追加
            \Package::load('iteminfo');
            $resultItems = \Iteminfo\Affili::url_replace($itemData);
            
            return $resultItems;
            
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
}
    